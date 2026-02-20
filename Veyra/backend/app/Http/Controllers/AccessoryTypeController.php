<?php

namespace App\Http\Controllers;

use App\Models\AccessoryType;

class AccessoryTypeController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => AccessoryType::query()->orderBy('name')->get()
        ]);
    }
}
