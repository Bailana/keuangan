<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function toggleSidebar(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->sidebar_collapsed = ! $user->sidebar_collapsed;
        $user->save();

        return back();
    }
}