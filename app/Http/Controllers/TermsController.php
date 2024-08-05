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
        $terms = $this->getTerms();
        return view('Academy.pages.terms.index', compact('terms'));
    }

}
