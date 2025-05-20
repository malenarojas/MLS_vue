<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait AutenticationTrait
{
  // Utils methods
  public function getAuthenticate(): ?User
  {
    return session()->get('login_as')
      ? User::where('id', session()->get('login_as'))->with('agent')->first()
      : Auth::user();
  }
}
