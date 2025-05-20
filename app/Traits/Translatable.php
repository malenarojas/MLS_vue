<?php

namespace App\Traits;

trait Translatable
{
    /**
     * Devuelve el valor traducido de un campo o el valor predeterminado.
     */
    public function getTranslatedField(string $field, string $language = 'en')
    {
        $translations = $this->translations ?? [];
        return $translations[$language][$field] ?? $this->{$field};
    }

    /**
     * Devuelve todas las traducciones para los campos traducibles.
     */
    public function getAllTranslations(): array
    {
        $translations = $this->translations;
        return $translations;
    }

    // public function getAllTranslations(): array
    // {
    //     $translations = $this->translations ?? [];
    //     $result = [];

    //     // Incluir los valores predeterminados como idioma base
    //     $defaultLanguage = 'es';
    //     $result[$defaultLanguage] = [];

    //     foreach ($this->getTranslatableFields() as $field) {
    //         $result[$defaultLanguage][$field] = $this->{$field};
    //     }

    //     // Agregar las traducciones agrupadas por idioma
    //     foreach ($translations as $lang => $fields) {
    //         $result[$lang] = [];

    //         foreach ($this->getTranslatableFields() as $field) {
    //             $result[$lang][$field] = $fields[$field] ?? null;
    //         }
    //     }

    //     return $result;
    // }

    /**
     * Establece una traducción para un campo específico.
     */
    public function setTranslation(string $field, string $language, string $value): void
    {
        // Validar si el campo es traducible
        if (!in_array($field, $this->getTranslatableFields())) {
            throw new \Exception("The field '{$field}' is not translatable.");
        }

        // Actualizar o crear la traducción
        $translations = $this->translations ?? [];
        $translations[$language][$field] = $value;

        $this->translations = $translations;
    }

    /**
     * Agrega o actualiza varias traducciones al mismo tiempo.
     */
    public function addTranslations(string $language, array $data): void
    {
        $translations = $this->translations ?? [];

        foreach ($data as $field => $value) {
            if (in_array($field, $this->getTranslatableFields())) {
                $translations[$language][$field] = $value;
            }
        }

        $this->translations = $translations;
    }

    /**
     * Obtiene los campos traducibles definidos en el modelo.
     */
    public function getTranslatableFields(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }
}
