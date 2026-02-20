<?php

namespace App\Http\Controllers;

use App\Models\FinishTreatment;

class FinishTreatmentController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => FinishTreatment::orderBy('name')->get()
        ]);
    }
}
