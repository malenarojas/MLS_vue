<?php

namespace App\Services\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Contracts\AuditFieldResolverInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
  public function __construct(
    protected AuditFieldResolverInterface $resolver
  ) {}

  public function generateLogs(array $newData, Model $model, array $fieldMap = []): array
  {
    $logs = [];

    foreach ($newData as $field => $newValue) {
      $oldValue = $model->getOriginal($field);

      if ((string) $oldValue !== (string) $newValue) {
        $resolvedOld = $this->resolver->resolve($field, $oldValue);
        $resolvedNew = $this->resolver->resolve($field, $newValue);

        $name = $fieldMap[$field] ?? ucfirst(str_replace('_', ' ', $field));

        $logs[] = [
          'field_name' => $name,
          'old_value' => $resolvedOld,
          'new_value' => $resolvedNew,
          'notes'     => "$name: '$resolvedOld' → '$resolvedNew'",
          'user_id'   => Auth::id(),
        ];
      }
    }

    return $logs;
  }
}
