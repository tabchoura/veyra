<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MaterialsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        $fullName = trim($row['name']);
        $code = null;
        $region = 'Global';

        if (preg_match('/\(([^)]+)\)/', $fullName, $matches)) {
            $region = trim($matches[1]);
        }

        if (preg_match('/ - ([A-Z\/\.]+)$/', $fullName, $matches)) {
            $code = trim($matches[1]);
        }

        $name = preg_replace('/\s*\([^)]+\)\s*/', '', $fullName);
        $name = preg_replace('/\s*-\s*[A-Z\/\.]+\s*$/', '', $name);
        $name = trim($name);

        return Material::updateOrCreate(
            ['name' => $name, 'region' => $region],
            ['code' => $code]
        );
    }

    public function chunkSize(): int
    {
        return 100;
    }
}