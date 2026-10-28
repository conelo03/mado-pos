<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', Rule::in(config('app.available_locales'))],
        ]);

        $request->user()->update(['locale' => $validated['locale']]);

        return back();
    }
}
