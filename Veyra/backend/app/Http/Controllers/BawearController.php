<?php
namespace App\Http\Controllers;

use App\Jobs\ExtractBawearFromPdfJob;
use App\Models\BawearAssessment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BawearController extends Controller
{
  public function showLatest(Product $product)
  {
    $latest = BawearAssessment::where('product_id', $product->id)->latest()->first();
    return response()->json(['data' => $latest]);
  }

  public function uploadPdf(Request $request, Product $product)
  {
    $data = $request->validate([
      'pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB
    ]);

    $path = $data['pdf']->store("bawear/{$product->id}", 'public');

    $assessment = BawearAssessment::create([
      'product_id' => $product->id,
      'source_type' => 'pdf',
      'pdf_path' => $path,
      'status' => 'draft',
      'created_by' => $request->user()->id,
    ]);

    // Lance l'extraction (mets QUEUE_CONNECTION=database ou redis)
    ExtractBawearFromPdfJob::dispatch($assessment->id);

    return response()->json([
      'message' => 'PDF uploaded, extraction queued',
      'data' => $assessment
    ], 201);
  }

  public function updateNormalized(Request $request, Product $product, BawearAssessment $assessment)
  {
    abort_unless($assessment->product_id === $product->id, 404);

    $data = $request->validate([
      'normalized_payload' => ['required', 'array'],
      'score_value' => ['nullable', 'numeric'],
      'score_unit' => ['nullable', 'string', 'max:20'],
      'status' => ['nullable', Rule::in(['draft','processed','error'])],
    ]);

    $assessment->fill($data)->save();

    return response()->json(['data' => $assessment]);
  }

  public function calculateFromApi(Request $request, Product $product)
  {
    // CDC: "Calcul bAwear (API)" à intégrer 
    return response()->json([
      'message' => 'bAwear API integration not implemented yet'
    ], 501);
  }
}
