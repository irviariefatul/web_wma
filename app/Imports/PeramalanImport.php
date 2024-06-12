<?php

namespace App\Imports;

use App\Models\Peramalan;
use App\Utils\PeramalanUtils;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PeramalanImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $arrayData = $collection->toArray();

        // Remove the first element if needed
        unset($arrayData[0]);

        // Define a set of known example data to exclude
        $exampleData = [
            ['1/2/2024', 0, 0, 0, 0, 7323.59, 0],
            ['1/3/2024', 0, 0, 0, 0, 7279.09, 0],
            ['1/4/2024', 0, 0, 0, 0, 7359.76, 0],
        ];

        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

        $batchSize = 100; // Ukuran batch
        $batchData = [];

        foreach ($arrayData as $item) {
            // Check if item is not part of the example data
            if (in_array($item, $exampleData)) {
                continue;
            }

            // Check if $item[5] is not null or 0
            if (isset($item[5]) && $item[5] != null && $item[5] != 0) {
                $batchData[] = [
                    'date' => $item[0],
                    'adj' => $item[5]
                ];

                // Jika ukuran batch mencapai batchSize, proses batch
                if (count($batchData) >= $batchSize) {
                    try {
                        PeramalanUtils::scrapBatch(2, $batchData);
                        $batchData = []; // Reset batch
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                }
            }
        }

        // Proses sisa data dalam batch terakhir
        if (!empty($batchData)) {
            try {
                PeramalanUtils::scrapBatch(2, $batchData);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
