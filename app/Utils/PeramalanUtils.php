<?php


namespace App\Utils;

use App\Models\Peramalan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PeramalanUtils
{
    static public function scrap($rentang, $date, $adj)
    {
        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
        // Mengambil data aktual terbaru sesuai rentang yang diberikan
        $data_aktual_terbaru = Peramalan::where('tanggal', '<', $date)
            ->orderBy('tanggal', 'desc')
            ->take($rentang)
            ->get();

        // jika $date formatnya tidak datetime, maka ubah ke datetime
        if (!is_a($date, Carbon::class)) {
            $date = Carbon::parse($date)->format('Y-m-d');
        }

        // Memastikan nilai aktual tersedia sebanyak rentang yang diminta
        if ($data_aktual_terbaru->count() == $rentang) {
            // Menghitung Weighted Moving Average berdasarkan rentang dan bobot
            $total_weight = 0;
            $weighted_sum = 0;

            for ($i = 0; $i < $rentang; $i++) {
                $weight = $rentang - $i;
                $total_weight += $weight;
                $weighted_sum += $data_aktual_terbaru[$i]->nilai_aktual * $weight;
            }

            $nilai_peramalan = $weighted_sum / $total_weight;

            // Menghitung MAPE berdasarkan formula
            $PE = abs(($adj - $nilai_peramalan) / $adj * 100);
        } else {
            // Set nilai_peramalan dan MAPE menjadi null jika data tidak tersedia
            $nilai_peramalan = null;
            $PE = null;
        }

        // Simpan atau perbarui objek Peramalan ke database
        $check = Peramalan::where('tanggal', $date)->first();
        if ($check) {
            $check->nilai_aktual = $adj;
            $check->nilai_peramalan = $nilai_peramalan;
            $check->nilai_error = $PE;
            $check->save();
        } else {
            $peramalan = new Peramalan([
                'user_id' => Auth::id(),
                'tanggal' => $date,
                'nilai_aktual' => $adj,
                'nilai_peramalan' => $nilai_peramalan,
                'nilai_error' => $PE
            ]);
            $peramalan->save();
        }

        // Perbarui semua peramalan yang ada dengan rentang yang dimasukkan
        $data_aktual = Peramalan::orderBy('tanggal', 'asc')->get();
        $data_aktual->each(function ($item, $key) use ($rentang) {
            $data_aktual_terbaru = Peramalan::where('tanggal', '<', $item->tanggal)
                ->orderBy('tanggal', 'desc')
                ->take($rentang)
                ->get();

            if ($data_aktual_terbaru->count() == $rentang) {
                // Menghitung Weighted Moving Average berdasarkan rentang dan bobot
                $total_weight = 0;
                $weighted_sum = 0;

                for ($i = 0; $i < $rentang; $i++) {
                    $weight = $rentang - $i;
                    $total_weight += $weight;
                    $weighted_sum += $data_aktual_terbaru[$i]->nilai_aktual * $weight;
                }

                // Memastikan nilai peramalan tidak kurang dari 0
                $nilai_peramalan = max(0, $weighted_sum / $total_weight);

                // Menghitung MAPE berdasarkan formula
                $PE = abs(($item->nilai_aktual - $nilai_peramalan) / $item->nilai_aktual * 100);
            } else {
                // Set nilai_peramalan dan MAPE menjadi null jika data tidak tersedia
                $nilai_peramalan = null;
                $PE = null;
            }

            $check = Peramalan::where('tanggal', $item->tanggal)->first();
            // Periksa apakah data sudah ada, jika ada perbarui, jika tidak buat data baru
            if ($check) {
                $check->nilai_peramalan = $nilai_peramalan;
                $check->nilai_error = $PE;
                $check->save();
            }
        });

        return true;
    }

    static public function changeWeight($rentang = 2)
    {
        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
        // Ambil semua data Peramalan yang ada
        $data_aktual = Peramalan::orderBy('tanggal', 'asc')->get();

        // Perbarui semua peramalan yang ada dengan rentang yang dimasukkan
        $data_aktual->each(function ($item, $key) use ($rentang) {
            // Mengambil data aktual terbaru sesuai rentang yang diberikan
            $data_aktual_terbaru = Peramalan::where('tanggal', '<', $item->tanggal)
                ->orderBy('tanggal', 'desc')
                ->take($rentang)
                ->get();

            // Memastikan nilai aktual tersedia sebanyak rentang yang diminta
            if ($data_aktual_terbaru->count() == $rentang) {
                // Menghitung Weighted Moving Average berdasarkan rentang dan bobot
                $total_weight = 0;
                $weighted_sum = 0;

                for ($i = 0; $i < $rentang; $i++) {
                    $weight = $rentang - $i;
                    $total_weight += $weight;
                    $weighted_sum += $data_aktual_terbaru[$i]->nilai_aktual * $weight;
                }

                // Memastikan nilai peramalan tidak kurang dari 0
                $nilai_peramalan = max(0, $weighted_sum / $total_weight);

                // Menghitung MAPE berdasarkan formula
                $PE = abs(($item->nilai_aktual - $nilai_peramalan) / $item->nilai_aktual * 100);
            } else {
                // Set nilai_peramalan dan MAPE menjadi null jika data tidak tersedia
                $nilai_peramalan = null;
                $PE = null;
            }

            // Perbarui objek Peramalan ke database jika data sudah ada
            $item->nilai_peramalan = $nilai_peramalan;
            $item->nilai_error = $PE;
            $item->save();
        });

        return true;
    }

    static public function scrapBatch($rentang, $dataArray)
    {
        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
        foreach ($dataArray as $item) {
            $date = $item['date'];
            $adj = $item['adj'];

            // Mengambil data aktual terbaru sesuai rentang yang diberikan
            $data_aktual_terbaru = Peramalan::where('tanggal', '<', $date)
                ->orderBy('tanggal', 'desc')
                ->take($rentang)
                ->get();

            // jika $date formatnya tidak datetime, maka ubah ke datetime
            if (!is_a($date, Carbon::class)) {
                $date = Carbon::parse($date)->format('Y-m-d');
            }

            // Memastikan nilai aktual tersedia sebanyak rentang yang diminta
            if ($data_aktual_terbaru->count() == $rentang) {
                // Menghitung Weighted Moving Average berdasarkan rentang dan bobot
                $total_weight = 0;
                $weighted_sum = 0;

                for ($i = 0; $i < $rentang; $i++) {
                    $weight = $rentang - $i;
                    $total_weight += $weight;
                    $weighted_sum += $data_aktual_terbaru[$i]->nilai_aktual * $weight;
                }

                $nilai_peramalan = $weighted_sum / $total_weight;

                // Menghitung MAPE berdasarkan formula
                $PE = abs(($adj - $nilai_peramalan) / $adj * 100);
            } else {
                // Set nilai_peramalan dan MAPE menjadi null jika data tidak tersedia
                $nilai_peramalan = null;
                $PE = null;
            }

            // Simpan atau perbarui objek Peramalan ke database
            $check = Peramalan::where('tanggal', $date)->first();
            if ($check) {
                $check->nilai_aktual = $adj;
                $check->nilai_peramalan = $nilai_peramalan;
                $check->nilai_error = $PE;
                $check->save();
            } else {
                $peramalan = new Peramalan([
                    'user_id' => Auth::id(),
                    'tanggal' => $date,
                    'nilai_aktual' => $adj,
                    'nilai_peramalan' => $nilai_peramalan,
                    'nilai_error' => $PE
                ]);
                $peramalan->save();
            }
        }

        // Perbarui semua peramalan yang ada dengan rentang yang dimasukkan
        $data_aktual = Peramalan::orderBy('tanggal', 'asc')->get();
        $data_aktual->each(function ($item, $key) use ($rentang) {
            $data_aktual_terbaru = Peramalan::where('tanggal', '<', $item->tanggal)
                ->orderBy('tanggal', 'desc')
                ->take($rentang)
                ->get();

            if ($data_aktual_terbaru->count() == $rentang) {
                // Menghitung Weighted Moving Average berdasarkan rentang dan bobot
                $total_weight = 0;
                $weighted_sum = 0;

                for ($i = 0; $i < $rentang; $i++) {
                    $weight = $rentang - $i;
                    $total_weight += $weight;
                    $weighted_sum += $data_aktual_terbaru[$i]->nilai_aktual * $weight;
                }

               // Memastikan nilai peramalan tidak kurang dari 0
                $nilai_peramalan = max(0, $weighted_sum / $total_weight);

                // Menghitung MAPE berdasarkan formula
                $PE = abs(($item->nilai_aktual - $nilai_peramalan) / $item->nilai_aktual * 100);
            
            } else {
                // Set nilai_peramalan dan MAPE menjadi null jika data tidak tersedia
                $nilai_peramalan = null;
                $PE = null;
            }

            $check = Peramalan::where('tanggal', $item->tanggal)->first();
            // Periksa apakah data sudah ada, jika ada perbarui, jika tidak buat data baru
            if ($check) {
                $check->nilai_peramalan = $nilai_peramalan;
                $check->nilai_error = $PE;
                $check->save();
            }
        });

        return true;
    }
}
