<?php

namespace App\Traits;

trait FormatExceptionError
{
  protected function formatExceptionError(\Exception $exception)
  {
    return [
      'message' => $exception->getMessage(),
      'file' => $exception->getFile(),
      'line' => $exception->getLine(),
      'trace' => $exception->getTrace(),
    ];
  }
}
