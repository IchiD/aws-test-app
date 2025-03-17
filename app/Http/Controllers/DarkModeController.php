<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DarkModeController extends Controller
{
  public function update(Request $request)
  {
    $request->validate([
      'dark_mode' => 'required|boolean',
    ]);

    $user = $request->user();
    $user->dark_mode = $request->dark_mode;
    $user->save();

    return back()->with([
      'dark_mode' => $user->dark_mode
    ]);
  }
}
