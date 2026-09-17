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
        $rawTerms = $termsSetting?->value ?? '';
        $terms = $this->sanitizeHtml($rawTerms);
        $termsUpdatedAt = $termsSetting?->updated_at;

        return view('Academy.pages.terms.index', compact('terms', 'termsUpdatedAt'));
    }

    private function sanitizeHtml(string $html): string
    {
        // Remove dangerous script, iframe, and embed tags with their contents
        $html = preg_replace('#<(script|iframe|object|embed|style|meta|link)[^>]*>.*?</\1>#is', '', $html);

        // Strip disallowed tags, keeping safe rich-text formatting tags
        $allowedTags = '<h1><h2><h3><h4><h5><h6><p><br><strong><b><em><i><u><ul><ol><li><blockquote><table><thead><tbody><tr><th><td><span><div><a><hr>';
        $cleaned = strip_tags($html, $allowedTags);

        // Remove all inline event handlers (e.g. onclick, onerror, onload)
        $cleaned = preg_replace('/\s*on[a-z]+\s*=\s*(["\'][^"\']*["\']|[^\s>]+)/i', '', $cleaned);

        // Disallow javascript: links in href
        $cleaned = preg_replace('/href\s*=\s*["\']\s*javascript:[^"\']*["\']/i', 'href="#"', $cleaned);

        return $cleaned;
    }
}
