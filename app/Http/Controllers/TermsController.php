<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function getTerms(): string
    {
        $termsSetting = Setting::where('key', 'terms')->first();
        return $termsSetting ? $termsSetting->value : '';
    }
    public function index()
    {
        $termsSetting = Setting::where('key', 'terms')->first();
        $terms = $termsSetting?->value ?? '';
        $termsUpdatedAt = $termsSetting?->updated_at;

        return view('Academy.pages.terms.index', compact('terms', 'termsUpdatedAt'));
    }

}
