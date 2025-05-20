<?php

namespace App\AuditConfigs;

abstract class BaseAuditConfig
{
  /**
   * Devuelve los nombres amigables de los campos.
   */
  abstract public static function fieldMap(): array;

  /**
   * Devuelve las relaciones tipo FK para resolver los valores.
   */
  public static function foreignKeys(): array
  {
    return [];
  }

  /**
   * Devuelve los mapeos de valores fijos como enums simples.
   */
  public static function valueLabels(): array
  {
    return [];
  }

  /**
   * Lista los campos auditables (por defecto todos del fieldMap).
   */
  public static function auditableFields(): array
  {
    return array_keys(static::fieldMap());
  }
}
