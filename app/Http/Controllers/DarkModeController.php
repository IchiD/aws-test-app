<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DarkModeController extends Controller
{
  /**
   * Update the user's dark mode preference.
   */
  public function update(Request $request)
  {
    $validated = $request->validate([
      'dark_mode' => 'required|boolean',
    ]);

    $user = Auth::user();
    $user->dark_mode = $validated['dark_mode'];
    $user->save();

    return back()->with('dark_mode', $validated['dark_mode']);
  }
}
