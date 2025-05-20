<?php

namespace App\Services\Migrations;

use App\Constants\ListingConstants;
use App\Dtos\Excels\ListingExcelDto;
use App\Dtos\Iconnect\CommisionIcoDto;
use App\Dtos\Iconnect\DescriptionIcoDto;
use App\Dtos\Iconnect\DocumentIcoDto;
use App\Dtos\Iconnect\ListingIcoDto;
use App\Dtos\Iconnect\ListingInformationIcoDto;
use App\Dtos\Iconnect\LocationIcoDto;
use App\Dtos\Iconnect\OwnerIcoDto;
use App\Dtos\Iconnect\PriceIcoDto;
use App\Dtos\Iconnect\RoomIcoDto;
use App\Enum\ListingMigrationStatus;
use App\Jobs\MigrateListingChunk;
use App\Jobs\MigratePropiedadesChunkJob;
use App\Jobs\MigratePropiedadJob;
use App\Models\Agent;
use App\Models\AgentListings;
use App\Models\CancellationReason;
use App\Models\City;
use App\Models\Contact;
use App\Models\Documentation;
use App\Models\Feature;
use App\Models\Listing;
use App\Models\ListingInformation;
use App\Models\ListingMigrationLog;
use App\Models\Multimedia;
use App\Models\Propiedad;
use App\Models\Province;
use App\Models\State;
use App\Models\Zone;
use App\Services\ImageService;
use App\Traits\FormatExceptionError;
use App\Traits\HandlesDescriptions;
use App\Utils\FormatString;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class ListingMigrationService
{
	use HandlesDescriptions, FormatExceptionError;

	private string $jsonFilePath;
	private bool $activar = false;
	private array $status = [];
	private bool $migrateAll = false;
	private bool $migrateFromFirst = false;
	private string $token;

	public function __construct(
		private ImageService $imageService,
		private IconnectService $iconnectService,
	) {
		$this->jsonFilePath = 'migraciones/migracion_' . now()->format('Ymd_His') . '.json';
	}

	public function migrateReport()
	{
		Propiedad::orderBy('MLS_ID')->chunk(1000, function ($propiedades) {
			MigratePropiedadesChunkJob::dispatch($propiedades);
		});
	}

	public function storeDto() {}

	public function changedStatus(string $filePath, int $status_id)
	{
		Log::info("Comenzando migración con status ID: $status_id");
		$spreadsheet = IOFactory::load($filePath);
		$sheet = $spreadsheet->getActiveSheet();
		$rows = $sheet->toArray(null, true, true, true);
		$count = 0;
		$notFound = 0;

		// Crear hoja para registros no encontrados
		$notFoundSheet = new Spreadsheet();
		$notFoundActiveSheet = $notFoundSheet->getActiveSheet();
		$notFoundActiveSheet->setTitle('No encontrados');
		$notFoundActiveSheet->setCellValue('A1', 'MLSID');
		$notFoundActiveSheet->setCellValue('B1', 'Motivo');
		$nfRowIndex = 2;

		foreach ($rows as $index => $row) {
			if ($index === 1) continue;

			try {
				$listingData = ListingExcelDto::from([
					'mlsId' => $row['A'] ?? null,
					'cancellation_date' => $row['B'] ?? null,
					'cancellation_reason' => $row['C'] ?? null,
					'date_of_listing' => $row['D'] ?? null,
					'updated_at' => $row['E'] ?? null,
					'contract_end_date' => $row['F'] ?? null,
					'property_category' => $row['G'] ?? null,
				]);

				Log::info("$index: Listings MLSID $listingData->mlsId");

				$listing = Listing::where('MLSID', $listingData->mlsId)->first();

				if (!$listing) {
					Log::info("No se encontró el listing con MLSID {$listingData->mlsId}");
					$notFound++;
					// Guardar en el Excel de no encontrados
					$notFoundActiveSheet->setCellValue("A$nfRowIndex", $listingData->mlsId);
					$notFoundActiveSheet->setCellValue("B$nfRowIndex", 'No encontrado');
					$nfRowIndex++;
					continue;
				}

				if (isset($listingData->cancellation_reason)) {
					$cancellation_reason = trim($listingData->cancellation_reason);
					$cancelationReason = CancellationReason::firstOrCreate([
						'name' => $cancellation_reason,
					]);
					$listing->cancellation_reason_id = $cancelationReason->id;
				}

				$listing->cancellation_date = FormatString::formatFromDMYToYMD($listingData->cancellation_date);
				$listing->date_of_listing = FormatString::formatFromDMYToYMD($listingData->date_of_listing);
				$listing->updated_at = FormatString::formatFromDMYToYMD($listingData->updated_at);
				$listing->status_listing_id = $status_id;

				if ($listingData->property_category_id) {
					Log::info("Actualizando categoria de propiedad {$listingData->property_category_id} y tipo transacción 3");
					$listing->listing_information()->updateOrCreate(
						['listing_id' => $listing->id],
						['property_category_id' => $listingData->property_category_id]
					);
					$listing->transaction_type_id = 3;
				}

				$listing->save();
				$count++;

				Log::info("Listing {$listingData->mlsId} actualizado a estado {$status_id}");
				Log::info("Actualizando listing ID {$listing->id}: ", $listing->only([
					'status_listing_id',
					'cancellation_reason_id',
					'cancellation_date'
				]));
			} catch (\Exception $e) {
				Log::error("Error procesando fila {$index}: " . $e->getMessage());
			}
		}

		// Guardar el archivo de no encontrados
		$filename = 'no-encontrados-' . now()->format('Ymd-His') . '.xlsx';
		$writer = new Xlsx($notFoundSheet);
		$savePath = storage_path("app/$filename");
		$writer->save($savePath);

		Log::info("Total de listings actualizados: $count");
		Log::info("Total de listings no encontrados: $notFound");
		Log::info("Archivo de listings no encontrados guardado en: $savePath");
	}

	public function insertFromExcel(string $filePath)
	{
		$spreadsheet = IOFactory::load($filePath);
		$sheet = $spreadsheet->getActiveSheet();
		$rows = $sheet->toArray(null, true, true, true);

		foreach ($rows as $index => $row) {
			if ($index === 1) continue; // Saltar encabezados

			try {
				DB::beginTransaction();

				$listingData = ListingExcelDto::from([
					'OfficeID' => $row['A'] ?? null,
					'AgentID' => $row['B'] ?? null,
					'ListingKey' => $row['C'] ?? null,
					'mlsId' => $row['D'] ?? null,
					'SoldPrice' => $row['E'] ?? null,
					'SoldPriceCurrency' => $row['F'] ?? null,
					'CurrentListingPrice' => $row['G'] ?? null,
					'CurrentListingCurrency' => $row['H'] ?? null,
					'PropertyType' => $row['I'] ?? null,
					'TransactionType' => $row['J'] ?? null,
					'City' => $row['K'] ?? null,
					'Province' => $row['L'] ?? null,
					'Region' => $row['M'] ?? null,
					'TotalArea' => $row['N'] ?? null,
					'LotSize' => $row['O'] ?? null,
					'Key' => $row['P'] ?? null,
				]);

				$key = $listingData->ListingKey;

				if (!$key) {
					break;
				}

				Log::info("$index: Listings keys $key");
				Log::info("$index: Listings Dto", (array) $listingData);

				$this->saveListingFromExcelDto($listingData);

				DB::commit();
			} catch (\Exception $e) {
				DB::rollBack();
				Log::error("Error procesando fila {$index}: " . $e->getMessage());
			}
		}
	}

	public function migrateFromDB(array $status, bool $migrateFromFirst = false)
	{
		$this->status = $status;
		$this->migrateFromFirst = $migrateFromFirst;
		Log::info("Iniciando migración desde la base de datos");

		$token = $this->iconnectService->getAuthToken();
		$processedRows = 0;

		$query = Listing::select('id', 'MLSID', 'key')->orderBy('id');

		if (!$migrateFromFirst) {
			$lastListingMigration = ListingMigrationLog::whereNotNull('listing_id')
				->orderByDesc('id')
				->first();

			$lastMigratedId = $lastListingMigration?->listing_id;

			if ($lastMigratedId) {
				$query->where('id', '>', $lastMigratedId);
				Log::info("Último ID migrado: $lastMigratedId");
			} else {
				Log::info("No se encontró un ID migrado anteriormente.");
			}
		} else {
			Log::info("Migrando todos los registros desde el principio.");
		}

		// Migrar de a uno con cursor para eficiencia
		$query->cursor()->each(function ($listing) use (&$processedRows, $token) {
			if (!$listing->key) {
				Log::warning("Listing ID {$listing->id} ignorado: key inválido");
				return;
			}

			Log::info("Procesando Listing ID {$listing->id} (MLSID = {$listing->MLSID}, Key = {$listing->key})");

			$this->processData($listing->MLSID, $listing->key, $token);
			$processedRows++;
		});

		Log::info("Migración finalizada. Total de listings procesados: $processedRows");
	}

	public function prepareMigrationFromDB(bool $migrateAll = false, bool $migrateFromFirst = false): void
	{
		$this->migrateAll = $migrateAll;
		$this->migrateFromFirst = $migrateFromFirst;

		$token = $this->iconnectService->getAuthToken();

		$query = Listing::select('id')->orderBy('id');

		if (!$migrateFromFirst) {
			$lastListingMigration = ListingMigrationLog::whereNotNull('listing_id')
				->orderByDesc('id')
				->first();
			$lastMigratedId = $lastListingMigration?->listing_id;

			if ($lastMigratedId) {
				$query->where('id', '>', $lastMigratedId);
				Log::info("Último ID migrado: $lastMigratedId");
			} else {
				Log::info("No se encontró un ID migrado anteriormente.");
			}
		}

		$query->chunk(500, function ($listings) use ($token) {
			$jobs = [];

			foreach ($listings as $listing) {
				$jobs[] = new MigrateListingChunk($listing->id, $token);
			}

			Bus::batch($jobs)
				->onQueue('default')
				->name('Migración de Listings')
				->allowFailures()
				->dispatch();
		});

		Log::info("Se despacharon todos los batches de migración.");
	}

	// public function prepareMigrationFromDB(bool $migrateAll = false, bool $migrateFromFirst = false): void
	// {
	// 	$this->migrateAll = $migrateAll;
	// 	$this->migrateFromFirst = $migrateFromFirst;

	// 	$token = $this->iconnectService->getAuthToken();

	// 	$query = Listing::select('id')->orderBy('id');

	// 	if (!$migrateFromFirst) {
	// 		$lastListingMigration = ListingMigrationLog::whereNotNull('listing_id')
	// 			->orderByDesc('id')
	// 			->first();
	// 		$lastMigratedId = $lastListingMigration?->listing_id;
	// 		if ($lastMigratedId) {
	// 			$query->where('id', '>', $lastMigratedId);
	// 		}
	// 	}

	// 	foreach ($query->pluck('id') as $id) {
	// 		MigrateListingChunk::dispatch($id, $token);
	// 	}

	// 	// $query->chunk(1, function ($listings) use ($token) {
	// 	// 	$listing = $listings->first();
	// 	// 	if ($listing) {
	// 	// 		MigrateListingChunk::dispatch($listing->id, $token);
	// 	// 	}
	// 	// });
	// }

	public function migrateFromExcel(string $filePath, bool $isActive, array $status)
	{
		$this->status = $status;
		$this->activar = $isActive;
		Log::info("Iniciando migración");

		$reader = IOFactory::createReaderForFile($filePath);
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filePath);
		$sheet = $spreadsheet->getActiveSheet();

		$rowIndex = 1;
		$processedRows = 0;
		$token = $this->iconnectService->getAuthToken();

		$excelFilename = 'no-encontrados-' . now()->format('Ymd-His') . '.xlsx';
		$excelRow = 2;

		foreach ($sheet->getRowIterator(2) as $row) {
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);

			$mls = null;
			$key = null;
			$columnIndex = 0;

			foreach ($cellIterator as $cell) {
				if ($columnIndex == 0) {
					$mls = trim((string) $cell->getValue());
				} elseif ($columnIndex == 1) {
					$key = trim((string) $cell->getValue());
					break;
				}
				$columnIndex++;
			}

			Log::info("Fila $rowIndex: MLSID = $mls, ListingKey = $key");

			if (!$key) {
				Log::warning("Fila $rowIndex ignorada: No se encontró ListingKey en el archivo.");

				$listing = Listing::where('MLSID', $mls)->first();

				if ($listing) {
					$key = $listing->key;
				} else {
					Log::warning("No se encontró el listing con MLSID $mls");
					$this->appendNotFoundToExcel($excelFilename, $mls, null, $excelRow);
					$rowIndex++;
					continue;
				}
			}

			Log::info("$rowIndex Procesar listings");
			$this->processData($mls, $key, $token);
			$processedRows++;
			$rowIndex++;
		}

		Log::info("Migración finalizada. Total de filas procesadas: $processedRows");
		Log::info("Archivo de no encontrados guardado: storage/app/$excelFilename");
	}


	protected function appendNotFoundToExcel(string $filename, string $mlsId, ?string $key, int &$rowIndex)
	{
		$filepath = storage_path("app/$filename");

		// Si es la primera vez, se crea el archivo con encabezados
		if (!file_exists($filepath)) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle('No encontrados');
			$sheet->setCellValue('A1', 'MLSID');
			$sheet->setCellValue('B1', 'Key');

			$writer = new Xlsx($spreadsheet);
			$writer->save($filepath);
			$rowIndex = 2;
		}

		// Cargar el archivo existente
		$spreadsheet = IOFactory::load($filepath);
		$sheet = $spreadsheet->getActiveSheet();

		// Escribir el nuevo dato
		$sheet->setCellValue("A$rowIndex", $mlsId);
		$sheet->setCellValue("B$rowIndex", $key ?? '');

		// Guardar en disco inmediatamente
		$writer = new Xlsx($spreadsheet);
		$writer->save($filepath);

		$rowIndex++;
	}

	// public function migrateFromExcel(string $filePath, bool $isActive, array $status)
	// {
	// 	// $this->migrateAll = $migrateAll;
	// 	$this->status = $status;
	// 	$this->activar = $isActive;
	// 	Log::info("Iniciando migración");

	// 	$reader = IOFactory::createReaderForFile($filePath);
	// 	$reader->setReadDataOnly(true);
	// 	$spreadsheet = $reader->load($filePath);
	// 	$sheet = $spreadsheet->getActiveSheet();

	// 	$rowIndex = 1;
	// 	$processedRows = 0;

	// 	$token = $this->iconnectService->getAuthToken();

	// 	// Iterar fila por fila, comenzando en la segunda fila (para omitir encabezados)
	// 	foreach ($sheet->getRowIterator(2) as $row) {
	// 		$cellIterator = $row->getCellIterator();
	// 		$cellIterator->setIterateOnlyExistingCells(false); // Leer celdas vacías

	// 		$mls = null;
	// 		$key = null;
	// 		$columnIndex = 0;

	// 		// Iteramos sobre las celdas de la fila y tomamos solo las dos primeras columnas
	// 		foreach ($cellIterator as $cell) {
	// 			if ($columnIndex == 0) {
	// 				$mls = trim((string) $cell->getValue());
	// 			} elseif ($columnIndex == 1) {
	// 				$key = trim((string) $cell->getValue());
	// 				break; // Salimos después de leer las dos primeras columnas
	// 			}
	// 			$columnIndex++;
	// 		}

	// 		Log::info("Fila $rowIndex: MLSID = $mls, ListingKey = $key");

	// 		if (!$key) {
	// 			Log::warning("Fila $rowIndex ignorada: No se encontró ListingKey en el archivo.");

	// 			$listing = Listing::where('MLSID', $mls)->first();

	// 			if ($listing) {
	// 				$key = $listing->key;
	// 			} else {
	// 				Log::warning("No se encontró el listing con MLSID $mls");
	// 				continue;
	// 			}
	// 		}

	// 		Log::info("$rowIndex Procesar listings");
	// 		$this->processData($mls, $key, true, $token);
	// 		$processedRows++;
	// 		$rowIndex++;
	// 	}

	// 	Log::info("Migración finalizada. Total de filas procesadas: $processedRows");
	// }

	public function migrateFromJson(string $filePath, bool $isActive)
	{
		$this->activar = $isActive;

		$jsonContent = file_get_contents($filePath);
		$jsonData = json_decode($jsonContent, true);

		if ($jsonData === null && json_last_error() !== JSON_ERROR_NONE) {
			Log::error("Error decoding JSON from file: " . $filePath, [
				'json_error' => json_last_error_msg(),
				'raw_content' => $jsonContent,
			]);

			return;
		}

		if (!is_array($jsonData)) {
			Log::error("Invalid JSON structure in file: " . $filePath);
			return;
		}

		$i = 1;
		foreach ($jsonData as $key => $data) {
			if (!isset($data['error'])) {
				Log::info("Procesando Listing $i: $key");
				$i++;
			}
		}
	}

	public function processData($mls, $key, $token)
	{
		$apiUrl = 'https://api.goiconnect.com/odata/Listings';
		Log::info("Procesando Listing: MLSID = $mls, ListingKey = $key");

		if (!$key) {
			Log::warning("No se encontró ListingKey en el archivo.");
			$listing = Listing::where('MLSID', $mls)->first();

			if ($listing) {
				$key = $listing->key;
			} else {
				Log::warning("No se encontró el listing con MLSID $mls");
				return;
			}
		}

		$log = ListingMigrationLog::firstOrCreate([
			'key' => $key,
		], ['key' => $key]);

		Log::info("Verificar los estados", [
			'status' => $log->status,
			'datos a migrar' => $this->status,
		]);

		// dd($log->status,  $this->status, in_array((string) $log->status, $this->status));
		if (!in_array((string) $log->status, $this->status)) {
			Log::info("Listing ya procesado anteriormente");
			return;
		}

		$data = null;
		if ($log->data !== null) {
			Log::info("Datos recuperados de la migración anterior");
			$data = $log->data;
			$log->message = "Datos recuperados de la migración anterior";
			$log->save();
		} else {
			Log::info("Fetcg Listing data from API");
			$data = $this->fetchListingData($apiUrl, $key, $token, $log);
		}

		if (!isset($data['error']) && $data !== null) {
			$this->storeListing($data, strtoupper($key), $mls, $log);
		} else {
			Log::error("Error al procesar Listing $key: ", $data);
		}
	}

	private function fetchListingData($apiUrl, $key, $token, ListingMigrationLog $log)
	{
		try {
			sleep(1); // Esperar 1 segundos antes de la siguiente petición
			$response = Http::withHeaders([
				'Authorization' => 'Bearer ' . $token,
				'Accept' => 'application/json',
			])->timeout(150)->get("$apiUrl/($key)");

			if (!$response->successful()) {
				$log->message_error = Str::limit("Error en API (Status: {$response->status()})");
				$log->errors = $response->json();

				if ($response->status() === Response::HTTP_NOT_FOUND) {
					$log->status = ListingMigrationStatus::NOT_FOUND;
				} else {
					$log->status = ListingMigrationStatus::ERROR;
				}
				$log->save();

				Log::info("Error en la peticion con listing key $key");
				return ['error' => "Error en API (Status: {$response->status()})"];
			}

			$responseJson = $response->json();

			$log->message = "Respuesta existosa de la API (Status: {$response->status()}";
			$log->data = $responseJson;
			$log->status = ListingMigrationStatus::SUCCESS;
			$log->MLSID = $responseJson['MLSID'];
			$log->message_error = null;
			$log->errors = null;
			$log->save();

			Log::info("Respuesta existosa de la API (Status: {$response->status()}");

			return (array) $responseJson;
		} catch (\Exception $e) {
			Log::error("Exception en la peticion, con el listings con key $key");
			return ['error' => "Error de conexión: " . $e->getMessage()];
		}
	}

	private function storeListing($data, $key, ?string $mls = null, ListingMigrationLog $log)
	{
		try {
			DB::beginTransaction();

			$listingDto = ListingIcoDto::from($data);
			if ($this->activar) {
				Log::info("Activar listings");
				$listingDto->status_listing_id = 2; // Activar
			}

			$listing = Listing::updateOrCreate(
				['key' => $listingDto->key, 'MLSID' => $listingDto->MLSID],
				(array) $listingDto
			);

			$this->saveDescription($data['Descriptions'], $listing);
			$listingInformationDto = ListingInformationIcoDto::from($data);
			$listingInformation = $this->saveListingInformation($listingInformationDto, $listing);
			$this->saveRooms($data['Rooms'], $listingInformation);
			$this->associateAgent($listing, $listingDto);
			$commisionDto = CommisionIcoDto::from($data['Commissions']);
			$listing->commission_option()->updateOrCreate(
				[
					'listing_id' => $listing->id
				],
				(array) $commisionDto
			);
			$this->updatePrice(PriceIcoDto::from($data['PriceDetails']), $listing);
			$this->processImages($data['Images'], $listing);
			$this->saveLocation(LocationIcoDto::from($data['Address']), $listing);
			$this->saveFeature($data['Features'], $listing);
			$this->saveOwner($data['Owners'], $listing);
			$this->savePublicDocumentation($data['PublicDocuments'], $listing);

			$log->listing_id = $listing->id;
			$log->status = ListingMigrationStatus::SUCCESS;
			$log->message = "Listing procesado con éxito";
			$log->message_error = null;
			$log->errors = null;
			$log->save();

			Log::info("Log", (array) $log);

			DB::commit();
		} catch (\Exception $e) {
			DB::rollBack();
			$log->message_error = Str::limit("Error al procesar Listing $key: " . $e->getMessage(), 200);
			$log->errors = $this->formatExceptionError($e);
			$log->status = ListingMigrationStatus::ERROR;
			$log->save();
			Log::error("Error al procesar Listing $key", ['exception' => $e->getMessage()]);
		}
	}

	private function saveJsonError($key, $message, $firstItem)
	{
		if (!$firstItem) {
			Storage::append($this->jsonFilePath, ",\n");
		}
		Storage::append($this->jsonFilePath, json_encode([$key => ['error' => $message]], JSON_PRETTY_PRINT));
	}

	private function saveJsonData($key, $data, $firstItem)
	{
		if (!$firstItem) {
			Storage::append($this->jsonFilePath, ",\n");
		}
		Storage::append($this->jsonFilePath, json_encode([$key => $data], JSON_PRETTY_PRINT));
	}

	private function saveListingInformation(ListingInformationIcoDto $listingInformationDto, Listing $listing)
	{
		$listingInformationDto->listing_id = $listing->id;
		return ListingInformation::updateOrCreate(
			['listing_id' => $listing->id],
			(array) $listingInformationDto
		);
	}

	private function associateAgent($listing, $listingDto)
	{
		$agent = Agent::where('agent_internal_id', $listingDto->agent_internal_id)->first();
		if ($agent) {
			AgentListings::updateOrCreate(
				['listing_id' => $listing->id],
				['listing_id' => $listing->id, 'agent_id' => $agent->id]
			);
		}
		return $agent;
	}

	private function updatePrice(PriceIcoDto $priceDto, Listing $listing)
	{
		$listing->listing_prices()->updateOrCreate(
			['listing_id' => $listing->id],
			['amount' => $priceDto->price, 'currency_id' => $priceDto->currency_id]
		);
	}

	private function processImages($images, Listing $listing)
	{
		$existMultimedias = Multimedia::where('listing_id', $listing->id)->pluck('link')->toArray();
		$mainFolder = "listings/$listing->key";

		foreach ($existMultimedias as $link) {
			$this->imageService->deleteImage($link);

			$pathInfo = pathinfo($link);
			$folder = $pathInfo['dirname'];  // "listings/{key}"
			$fileName = $pathInfo['basename'];

			foreach (Multimedia::TYPE_IMAGEN as $typeImage) {
				$newPath = $folder . '/' . $typeImage . '/' . $fileName;
				$this->imageService->deleteImage($newPath);
			}
		}

		Multimedia::where('listing_id', $listing->id)->delete();
		$multimedias = [];

		foreach ($images as $image) {
			$fileName = $this->imageService->generateFileName();

			// Almacenar imagen principal
			$link = $this->imageService->downloadImageFromUrl($image[Multimedia::IMAGEN_DEFAULT], $mainFolder, $fileName);

			if (!$link) {
				Log::warning("Error al descargar imagen principal", ['image' => $image]);
				continue;
			}

			$multimedias[] = [
				'link' => $link, // Listings/{key}/{filename} Link Imagen principal
				'listing_id' => $listing->id,
				'multimedia_type_id' => 3,
				'is_default' => $image['Default'],
			];

			foreach (Multimedia::TYPE_IMAGEN as $typeImage) {
				if (!isset($image[$typeImage])) {
					Log::warning("Image typeImage $typeImage is missing", ['image' => $image]);
					continue;
				}
				$folder = "$mainFolder/$typeImage"; // Almacenar todas la imagenes de forma clasificada
				$this->imageService->downloadImageFromUrl($image[$typeImage], $folder, $fileName);
			}
		}

		Multimedia::insert($multimedias);
	}

	private function saveLocation(LocationIcoDto $locationDto, Listing $listing)
	{
		Log::info("Location $locationDto->country - $locationDto->province - $locationDto->city - $locationDto->zone");
		$state = State::where('name', $locationDto->country)->first();
		if (!$state && $locationDto->country) {
			Log::info("No esta registrado el departamento $locationDto->country");
			$state = State::create([
				'name' => $locationDto->country,
			]);
		}

		$province = Province::where('name', $locationDto->province)->first();
		if (!$province) {
			Log::info("No esta registrado la provincia $locationDto->province");
			if ($locationDto->province) {
				$province = Province::create([
					'name' => $locationDto->province,
					'state_id' => $state->id,
				]);
			}
		}
		$locationDto->province_id = $province?->id;

		$city = City::where('name', $locationDto->city)->first();
		if (!$city) {
			Log::info("No esta registrado la ciudad $locationDto->city");
			if ($locationDto->city) {
				$city = City::create([
					'name' => $locationDto->city,
					'province_id' => $province->id,
				]);
			}
		}
		$locationDto->city_id = $city?->id;

		$zone = Zone::where('name', $locationDto->zone)->first();
		if (!$zone) {
			Log::info("No esta registrado la zona $locationDto->zone");
			if ($locationDto->zone) {
				$zone = Zone::create([
					'name' => $locationDto->zone,
					'city_id' => $city->id,
				]);
			}
		}
		$locationDto->zone_id = $zone?->id;

		Log::info("Modelos", [
			'state' => $state,
			'province' => $province,
			'city' => $city,
			'zone' => $zone,
		]);

		$listing->location()->updateOrCreate(
			[
				'listing_id' => $listing->id
			],
			(array) $locationDto
		);
	}

	private function saveFeature(array $features, Listing $listing)
	{
		foreach ($features as $feature) {
			if (empty($feature['FeatureName'])) {
				Log::warning("FeatureName is missing or empty", ['feature' => $feature]);
				continue;
			}

			$featureName = trim($feature['FeatureName']);

			$feature = Feature::firstOrCreate(
				['name' => $featureName]
			);

			$listing->features()->syncWithoutDetaching([$feature->id]);
		}
	}

	public function saveRooms(array $rooms, ListingInformation $listingInformation)
	{
		$listingInformation->rooms()->delete();

		foreach ($rooms as $room) {
			$roomDto = RoomIcoDto::from($room);
			$listingInformation->rooms()->create((array) $roomDto);
		}
	}

	private function saveOwner(array $owners, Listing $listing)
	{
		$currentOwnerContacts = $listing->owners()->get();
		$listing->owners()->detach();

		foreach ($currentOwnerContacts as $contact) {
			$contact->owners()->delete();
			$contact->delete();
		}

		foreach ($owners as $owner) {
			$ownerDto = OwnerIcoDto::from($owner);

			$contact = Contact::where(function ($query) use ($ownerDto) {
				$query->where('name', 'like', "%{$ownerDto->name}%")
					->orWhere('email', 'like', "%{$ownerDto->email}%");
			})->first();

			if (!$contact) {
				Log::info("Owner not found, creating new contact", (array) $ownerDto);
				$contact = Contact::create([
					'name' => $ownerDto->name,
					'email' => $ownerDto->email,
					'mobile' => $ownerDto->mobile,
				]);
			}

			$listing->owners()->attach($contact->id);
		}
	}

	private function savePublicDocumentation(array $documentations, Listing $listing)
	{
		$listing->documentation()->detach(); // Remove old relations

		foreach ($documentations as $documentation) {
			$documentationDto = DocumentIcoDto::from($documentation);

			$link = $this->imageService->downloadImageFromUrl($documentationDto->link, 'documentations');

			$newDocumentation = Documentation::create([
				'link' => $link,
				'description' => $documentationDto->description ?? null,
				'documentation_type_id' => $documentationDto->documentation_type_id,
				'original_name' => $documentationDto->original_name ?? null,
			]);

			$listing->documentation()->attach($newDocumentation->id);
		}
	}

	private function saveDescription(array $descriptions, Listing $listing)
	{
		$translations = [];

		foreach ($descriptions as $description) {
			$description = DescriptionIcoDto::from($description);
			$languageCode = $description->languageCode;
			$descText = $description->description;
			$descType = $description->descriptionType;

			if ($languageCode === ListingConstants::LANGUAGE_ESB) {
				$this->savePrimaryDescription($listing, $descType, $descText);
			} else {
				$this->saveTranslatedDescription($translations, $languageCode, $descType, $descText);
			}
		}

		$listing->translations = $translations;
		$listing->save();
	}

	private function saveListingFromExcelDto(ListingExcelDto $listing_excel_dto)
	{
		$listing = Listing::updateOrCreate(
			[
				'key' => $listing_excel_dto->ListingKey,
				'MLSID' => $listing_excel_dto->mlsId
			],
			[
				'key' => $listing_excel_dto->ListingKey,
				'MLSID' => $listing_excel_dto->mlsId,
				'area_id' => 2,
				'transaction_type_id' => $listing_excel_dto->TransactionType,
				'status_listing_id' => 2,
			]
		);

		$listing->listing_prices()->updateOrCreate(
			[
				'listing_id' => $listing->id,
			],
			[
				'amount' => $listing_excel_dto->CurrentListingPrice,
				'currency_id' => 1,
			]
		);

		$agent = Agent::where('agent_internal_id', $listing_excel_dto->AgentID)->first();

		if ($agent) {
			DB::table('agent_listing')->updateOrInsert(
				[
					'listing_id' => $listing->id,
					'agent_id' => $agent->id,
				],
				[]
			);
		} else {
			Log::info("No existe el agente con el ID " . $listing_excel_dto->AgentID);
		}
	}

	/*
	public function processStoredJson(string $jsonPath)
	{
		$jsonData = json_decode(Storage::get($jsonPath), true);
		foreach ($jsonData as $key => $data) {
			if (!isset($data['error'])) {
				$this->storeListing($data, $key, null);
			}
		}
		Log::info("Procesamiento de JSON finalizado: " . $jsonPath);
	}
	*/
}
