<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Traits\AutenticationTrait;

class MenuItemService
{
  use AutenticationTrait;

  public function __construct() {}

  public function getMenuItems(): array
  {
    $user = $this->getAuthenticate();
    $permissions = $user->getAllPermissions()->pluck('id')->toArray();

    $menuItems = MenuItem::whereNull('parent_id')
      ->where(function ($query) use ($permissions) {
        $query->whereIn('permission_id', $permissions)
          ->orWhereNull('permission_id');
      })
      ->orderBy('order')
      ->get();

    if ($menuItems->isEmpty()) {
      return [];
    }

    $menuItemsFormateados = $this->formatearMenuItems($menuItems);

    return $menuItemsFormateados;
  }

  public function getMenuItemsByPermissions($permissions): array
  {
    $menuItems = MenuItem::whereNull('parent_id')
      ->whereIn('permission_id', $permissions)
      ->get();

    if ($menuItems->isEmpty()) {
      return [];
    }

    $menuItemsFormateados = $this->formatearMenuItems($menuItems);

    return $menuItemsFormateados;
  }

  public function formatearMenuItems($menuItems): array
  {
    $menuFormateados = [];
    $user = $this->getAuthenticate();
    $permissions = $user->getAllPermissions()->pluck('id')->toArray();
    
    foreach ($menuItems as $menuItem) {
      $item = [
        'key' => $menuItem->id . '',
        'label' => $menuItem->name,
        'icon' => $menuItem->icon,
        'route' => $menuItem->route,
        'permission_id' => $menuItem->permission_id,
      ];

      $subItems = MenuItem::where('parent_id', $menuItem->id)
        ->whereIn('permission_id', $permissions)
        ->get();

      if ($subItems->isNotEmpty()) {
        $item['items'] = $this->formatearMenuItems($subItems);
      }

      $menuFormateados[] = $item;
    }

    return $menuFormateados;
  }
}
