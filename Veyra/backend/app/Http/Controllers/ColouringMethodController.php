<?php

namespace App\Http\Controllers;

use App\Models\ColouringMethod;

class ColouringMethodController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => ColouringMethod::orderBy('name')->get()
        ]);
    }
}
