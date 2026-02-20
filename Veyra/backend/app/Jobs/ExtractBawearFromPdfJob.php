<?php
namespace App\Jobs;

use App\Models\BawearAssessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExtractBawearFromPdfJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function __construct(public int $assessmentId) {}

  public function handle(): void
  {
    $a = BawearAssessment::findOrFail($this->assessmentId);

    try {
      // TODO: appeler un extracteur PDF tableau
      // Pour l'instant : placeholder
      $extracted = [
        'tables' => [
          ['row' => ['Indicator', 'Value'], 'data' => [['CO2', '12.3'], ['Water', '45']]]
        ]
      ];

      $normalized = [
        'impacts' => [
          ['name' => 'CO2', 'value' => 12.3, 'unit' => 'kgCO2e'],
          ['name' => 'Water', 'value' => 45, 'unit' => 'L'],
        ]
      ];

      $a->update([
        'extracted_payload' => $extracted,
        'normalized_payload' => $normalized,
        'status' => 'processed',
        'errors' => null,
      ]);

    } catch (\Throwable $e) {
      $a->update([
        'status' => 'error',
        'errors' => ['message' => $e->getMessage()],
      ]);
    }
  }
}


