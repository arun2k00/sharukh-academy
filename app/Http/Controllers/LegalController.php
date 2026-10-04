<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Static legal/policy pages, linked from the footer. Content lives directly
 * in the views since it changes by editing copy, not by adding logic.
 */
class LegalController extends Controller
{
    public function terms(): View
    {
        return view('public.legal.terms');
    }

    public function privacy(): View
    {
        return view('public.legal.privacy');
    }

    public function refunds(): View
    {
        return view('public.legal.refunds');
    }
}
