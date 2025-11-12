<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CsrfCookieController extends Controller
{
    public function show(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'CSRF cookie set']);
    }
}
