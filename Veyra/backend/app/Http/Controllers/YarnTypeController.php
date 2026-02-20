<?php

namespace App\Http\Controllers;

use App\Models\YarnType;

class YarnTypeController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => YarnType::orderBy('name')->get()
        ]);
    }
}
