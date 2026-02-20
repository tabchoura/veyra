<?php

namespace App\Http\Controllers;

use App\Models\FinishingMethod;

class FinishingMethodController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => FinishingMethod::orderBy('name')->get()
        ]);
    }
}
