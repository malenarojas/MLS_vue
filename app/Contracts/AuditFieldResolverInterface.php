<?php

namespace App\Contracts;

interface AuditFieldResolverInterface
{
  public function resolve(string $field, mixed $value): string;
}
