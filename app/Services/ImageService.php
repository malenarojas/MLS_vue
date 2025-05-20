<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Laravel\Facades\Image;
// use Intervention\Image\Drivers\Imagick\Driver;
// use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;

//use Intervention\Image\ImageManagerStatic as Image;


class ImageService
{
    // private $imageManager;
    // public function __construct()
    // {
    //     $this->imageManager = new ImageManager(new Driver());
    // }

    /**
     * Subir y redimensionar una imagen desde Base64 en una ruta específica dentro de storage.
     *
     * @param string $base64Image Imagen en formato Base64.
     * @param string $folder Carpeta donde se guardará la imagen.
     * @param string|null $fileName Nombre del archivo para guardar. Si es null, se generará uno.
     * @param int $width Ancho deseado de la imagen.
     * @param int $height Alto deseado de la imagen.
     * @return string Ruta relativa de la imagen guardada.
     * @throws \Exception Si ocurre un error al procesar la imagen.
     */
    public function uploadAndResizeImageFromBase64(
        string $base64Image,
        string $folder,
        ?string $fileName,
        int $width,
        int $height
    ): string {
        try {

            $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
            // Decodificar la imagen Base64
            $imageData = base64_decode($base64Image);
            if ($imageData === false) {
                throw new \Exception('Error al decodificar la imagen Base64.');
            }

            // Obtener el tipo MIME de la imagen
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);

            // Log para depuración
            // Log::info('Tipo MIME detectado: ' . $mimeType);

            // Determinar la extensión según el MIME
            $extension = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                default => throw new \Exception('Formato de imagen no soportado: ' . $mimeType),
            };

            // Generar un nombre de archivo único si no se proporciona
            if (!$fileName) {
                $fileName = Str::random(20) . '.' . $extension;
            }

            // Definir la ruta relativa dentro de 'storage/app/public'
            $filePath = $folder . '/' . $fileName;

            // Redimensionar la imagen con Intervention Image
            $image = Image::read($imageData)
                ->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio(); // Mantener la proporción
                    $constraint->upsize(); // Evitar ampliación innecesaria
                });

            // Guardar la imagen en el disco "public"
            Storage::disk('public')->put($filePath, (string) $image->encode());

            // Log para depuración
            // Log::info("Imagen guardada en: storage/app/public/{$filePath}");

            // Retornar el nombre del archivo para referencia
            return $fileName;
        } catch (\Exception $e) {
            Log::error('Error en uploadAndResizeImageFromBase64: ' . $e->getMessage());
            throw new \Exception('Error al subir y redimensionar la imagen: ' . $e->getMessage());
        }
    }
    public function updateuploadAndReplaceImageFromBase64(
        string $base64Image,
        string $folder,
        ?string $fileName,
        int $width,
        int $height,
        ?string $currentFileName
    ): string {
        try {
            if ($currentFileName) {
                $currentFilePath = $folder . '/' . $currentFileName;
                if (Storage::disk('public')->exists($currentFilePath)) {
                    Storage::disk('public')->delete($currentFilePath);
                    Log::info("Imagen actual eliminada: storage/app/public/{$currentFilePath}");
                }
            }

            $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
            $imageData = base64_decode($base64Image);

            if ($imageData === false) {
                throw new \Exception('Error al decodificar la imagen Base64.');
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);

            // Log::info('Tipo MIME detectado: ' . $mimeType);
            $extension = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                default => throw new \Exception('Formato de imagen no soportado: ' . $mimeType),
            };

            if (!$fileName) {
                $fileName = Str::random(20) . '.' . $extension;
            }

            $filePath = $folder . '/' . $fileName;

            $image = Image::read($imageData);

            // 🧠 Solo redimensionar si es más grande que los límites permitidos
            if ($image->width() > $width || $image->height() > $height) {
                // Log::info("La imagen supera los límites {$width}x{$height}, se va a redimensionar.");
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } else {
                // Log::info("La imagen está dentro del tamaño permitido, se guardará tal cual.");
            }

            Storage::disk('public')->put($filePath, (string) $image->encode());

            // Log::info("Imagen guardada en: storage/app/public/{$filePath}");
            return $fileName;
        } catch (\Exception $e) {
            Log::error('Error en uploadAndReplaceImageFromBase64: ' . $e->getMessage());
            throw new \Exception('Error al subir y redimensionar la imagen: ' . $e->getMessage());
        }
    }


    public function uploadImageFromFile(
        UploadedFile $file,
        string $folder,
        ?string $fileName = null, // Nombre del archivo
    ): string {
        try {
            $fileId = null;
            if (!$fileName) {
                $fileId = $this->generateFileName() . '.' . $file->getClientOriginalExtension();
            } else {
                $fileId = $fileName . '.' . $file->getClientOriginalExtension();
            }

            $filePath = $folder . '/' . $fileId;
            Storage::disk('public')->put($filePath, file_get_contents($file->getRealPath()));

            // Log::info("Imagen guardada en: storage/app/public/{$filePath}");

            return $folder . '/' . $fileId;
        } catch (\Exception $e) {
            Log::error('Error en uploadImageFromFile: ' . $e->getMessage());
            throw new \Exception('Error al subir la imagen desde el archivo: ' . $e->getMessage());
        }
    }

    public function resizeImage(string $filePath, int $width, ?int $height = null, ?string $destinatioPath = null, bool $watermark = false): void
    {
        try {
            $path = Storage::disk('public')->path($filePath);
            // Log::info("Redimensionando imagen: $path");
            $image = Image::read(Storage::disk('public')->get($filePath));

            if ($height) {
                $image = $image->contain($width, $height);
            } else {
                $image = $image->contain($width, $width);
            }

            if ($watermark) {
                $image->place(public_path('img/LogoMarcaAgua.png'), 'center', 0, 10, 16);
            }

            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $encodedImage = match ($extension) {
                'jpg', 'jpeg' => $image->encode(new JpegEncoder(quality: 90)),
                'png' => $image->encode(new PngEncoder()),
                'webp' => $image->encode(new WebpEncoder(quality: 90)),
                default => $image->encode(new JpegEncoder(quality: 90)), // fallback
            };

            $fileName = pathinfo($filePath, PATHINFO_BASENAME);

            $destinatioPath = $destinatioPath
                ? rtrim($destinatioPath, '/') . '/' . $fileName
                : $filePath;

            Storage::disk('public')->put($destinatioPath, (string) $encodedImage);

            // Log::info("Imagen redimensionada y guardada en: storage/app/public/{$destinatioPath}");
        } catch (\Exception $e) {
            Log::error('Error en resizeImage: ' . $e->getMessage());
            throw new \Exception('Error al redimensionar la imagen: ' . $e->getMessage());
        }
    }


    // public function resizeImage(string $filePath, int $width, int $height, ?string $destinatioPath = null, bool $watermark = false): void
    // {
    //     try {
    //         $path = Storage::disk('public')->path($filePath);
    //         Log::info("Redimensionando imagen: $path");

    //         $image = Image::read(Storage::disk('public')->get($filePath))
    //             ->resize($width, $height, function ($constraint) {
    //                 $constraint->aspectRatio();
    //                 $constraint->upsize();
    //             });

    //         if ($watermark) {
    //             $image->place(public_path('img/LogoMarcaAgua.png'), 'center', 0, 10, 16);
    //         }

    //         $fileName = pathinfo($filePath, PATHINFO_BASENAME);

    //         // Construir la ruta completa del archivo de destino
    //         if ($destinatioPath) {
    //             $destinatioPath = rtrim($destinatioPath, '/') . '/' . $fileName;
    //         } else {
    //             $destinatioPath = $filePath; // Guardar en la misma ubicación con el nuevo nombre si no hay destino
    //         }

    //         Storage::disk('public')->put($destinatioPath, (string) $image->encode());

    //         Log::info("Imagen redimensionada y guardada en: storage/app/public/{$destinatioPath}");
    //     } catch (\Exception $e) {
    //         Log::error('Error en resizeImage: ' . $e->getMessage());
    //         throw new \Exception('Error al redimensionar la imagen: ' . $e->getMessage());
    //     }
    // }

    public function generateFileName(): string
    {
        return Str::random(20);
    }

    public function downloadImageFromUrl(string $imageUrl, string $folder, ?string $fileName = null): ?string
    {
        try {
            $response = Http::get($imageUrl);

            if ($response->failed()) {
                Log::error("Error al descargar la imagen: {$imageUrl}");
                throw new \Exception("No se pudo descargar la imagen.");
            }

            $imageContent = $response->body();
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);

            $fileId = null;
            if (!$fileName) {
                $fileId = $this->generateFileName() . '.' . $extension;
            } else {
                $fileId = $fileName . '.' . $extension;
            }

            $filePath = "{$folder}/{$fileId}";
            Storage::disk('public')->put($filePath, $imageContent);

            // Log::info("Imagen descargada y guardada en: storage/app/public/{$filePath}");

            return "$folder/$fileId";
        } catch (\Exception $e) {
            Log::error('Error en downloadImageFromUrl: ' . $e->getMessage());
            return null;
        }
    }

    public function downloadImageFromUrlAgent(string $imageUrl, string $folder, ?string $fileName = null): ?string
    {
        try {
            $response = Http::get($imageUrl);

            if ($response->failed()) {
                Log::error("Error al descargar la imagen: {$imageUrl}");
                throw new \Exception("No se pudo descargar la imagen.");
            }

            $imageContent = $response->body();
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION);

            $fileId = null;
            if (!$fileName) {
                $fileId = $this->generateFileName() . '.' . $extension;
            } else {
                // Evita duplicar la extensión si ya viene incluida
                if (str_ends_with($fileName, '.' . $extension)) {
                    $fileId = $fileName;
                } else {
                    $fileId = $fileName . '.' . $extension;
                }
            }
            $filePath = "{$folder}/{$fileId}";

            Storage::disk('public')->put($filePath, $imageContent);

            // Log::info("Imagen descargada y guardada en: storage/app/public/{$filePath}");

            return "$folder/$fileId";
        } catch (\Exception $e) {
            Log::error('Error en downloadImageFromUrl: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteImage(string $filePath): bool
    {
        try {
            // Verificar si el archivo existe en el disco público
            if (Storage::disk('public')->exists($filePath)) {
                // Eliminar el archivo
                Storage::disk('public')->delete($filePath);
                // Log::info("Imagen eliminada de: storage/app/public/{$filePath}");
                return true;
            } else {
                Log::warning("Intento de eliminar una imagen no existente: storage/app/public/{$filePath}");
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error en deleteImage: ' . $e->getMessage());
            throw new \Exception('Error al eliminar la imagen: ' . $e->getMessage());
        }
    }
}
