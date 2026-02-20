<?php

namespace App\Http\Controllers;

use App\Models\FabricType;
use Illuminate\Http\JsonResponse;

class FabricTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $data = FabricType::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
