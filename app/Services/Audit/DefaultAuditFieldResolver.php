<?php

namespace App\Services\Audit;

use App\Contracts\AuditFieldResolverInterface;

class DefaultAuditFieldResolver implements AuditFieldResolverInterface
{
  public function __construct(
    protected array $foreignKeys = [], // 'field_name' => [Model::class, 'label_column']
    protected array $valueLabels = [],  // 'field_name' => [0 => 'No', 1 => 'Sí']
  ) {}

  public function resolve(string $field, mixed $value): string
  {
    if (is_null($value) || $value === '') return '—';

    if (isset($this->valueLabels[$field]) && is_array($this->valueLabels[$field])) {
      return $this->valueLabels[$field][$value] ?? (string) $value;
    }

    if (isset($this->foreignKeys[$field])) {
      [$model, $column] = $this->foreignKeys[$field];
      return $model::find($value)?->{$column} ?? 'Desconocido';
    }

    return (string) $value;
  }
}
