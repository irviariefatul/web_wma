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

        // Hapus elemen pertama jika diperlukan
        unset($arrayData[0]);

        // Definisikan satu set data contoh yang diketahui untuk dikecualikan
        $exampleData = [
            ['1/2/2024', 0, 0, 0, 0, 7323.59, 0],
            ['1/3/2024', 0, 0, 0, 0, 7279.09, 0],
            ['1/4/2024', 0, 0, 0, 0, 7359.76, 0],
        ];

        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 detik = 5 menit

        $batchSize = 100; // Ukuran batch
        $batchData = [];

        foreach ($arrayData as $item) {
            // Periksa jika item bukan bagian dari data contoh
            if (in_array($item, $exampleData)) {
                continue;
            }

            // Periksa jika $item[5] tidak null, tidak 0, dan merupakan angka yang valid
            if (isset($item[5]) && $item[5] != null && $item[5] != 0 && is_numeric($item[5])) {
                $batchData[] = [
                    'date' => $item[0],
                    'adj' => (float)$item[5] // Pastikan adj adalah float
                ];

                // Jika ukuran batch mencapai batchSize, proses batch
                if (count($batchData) >= $batchSize) {
                    try {
                        PeramalanUtils::scrapBatch(2, $batchData);
                        $batchData = []; // Reset batch
                    } catch (\Throwable $th) {
                        // Tangkap dan lempar ulang error jika terjadi
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
                // Tangkap dan lempar ulang error jika terjadi
                throw $th;
            }
        }
    }
}
