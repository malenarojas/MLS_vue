<?php

namespace App\Traits;

use App\Models\User;

trait HasAccessRules
{
  public function canAccess(User $user, string $accessType): bool
  {
    $rules = $this->access_rules->where('access_type', $accessType);

    $roles = $rules->where('type', 'role')->pluck('value')->toArray();
    $permissions = $rules->where('type', 'permission')->pluck('value')->toArray();

    $hasRole = empty($roles) || $user->hasAnyRole($roles);
    $hasPermission = empty($permissions) || $user->hasAnyPermission($permissions);

    return $hasRole && $hasPermission;
  }

  public function isVisibleToUser(User $user): bool
  {
    return $this->canAccess($user, 'view');
  }

  public function isDownloadableByUser(User $user): bool
  {
    return $this->canAccess($user, 'download');
  }
}
