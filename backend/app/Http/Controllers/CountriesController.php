<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CountriesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            ['code' => 'UA', 'name' => 'Ukraine'],
            ['code' => 'US', 'name' => 'United States'],
            ['code' => 'GB', 'name' => 'United Kingdom'],
            ['code' => 'DE', 'name' => 'Germany'],
            ['code' => 'FR', 'name' => 'France'],
            ['code' => 'JP', 'name' => 'Japan'],
            ['code' => 'CA', 'name' => 'Canada'],
            ['code' => 'AU', 'name' => 'Australia'],
            ['code' => 'BR', 'name' => 'Brazil'],
            ['code' => 'IN', 'name' => 'India'],
            ['code' => 'IT', 'name' => 'Italy'],
            ['code' => 'ES', 'name' => 'Spain'],
            ['code' => 'NL', 'name' => 'Netherlands'],
            ['code' => 'SE', 'name' => 'Sweden'],
            ['code' => 'PL', 'name' => 'Poland'],
        ]);
    }
}
