<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display basic application info (used instead of closure so routes can be cached).
     */
    public function index(Request $request)
    {
        return ['Laravel' => app()->version()];
    }
}
