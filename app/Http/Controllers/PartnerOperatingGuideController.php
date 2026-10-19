<?php

namespace App\Http\Controllers;

use App\Support\PartnerWorkspace;
use Illuminate\Http\Request;

class PartnerOperatingGuideController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('Academy.pages.operating-guide', [
            'workspace' => PartnerWorkspace::forUser(auth('academy')->user(), $request->route()->getName()),
        ]);
    }
}
