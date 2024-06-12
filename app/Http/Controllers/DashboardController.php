<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peramalan;

class DashboardController extends Controller
{
    public function index()
    {
        $peramalans = Peramalan::latest('tanggal')->limit(5)->get();
        return view('dashboard', compact('peramalans'));
    }

    public function hitungNilaiPeramalan2(Request $request)
    {
        $PE = 0;

        // Mengambil data yang ada
        $data_aktual = Peramalan::orderBy('tanggal', 'asc')->get();

        // Menghitung rentang sebagai jumlah baris dengan nilai_peramalan dan nilai_error yang null
        $rentang = Peramalan::whereNull('nilai_peramalan')->whereNull('nilai_error')->count();

        // Variabel untuk menyimpan jumlah item yang valid untuk menghitung rata-rata PE nanti
        $validItemsCount = 0;

        // Perulangan untuk menghitung nilai PE
        $data_aktual->each(function ($item, $key) use ($rentang, &$PE, &$validItemsCount) {
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

                $nilai_peramalan = $weighted_sum / $total_weight;

                // Menghitung MAPE berdasarkan formula
                $PE += abs(($item->nilai_aktual - $nilai_peramalan) / $item->nilai_aktual * 100);
                $validItemsCount++;
            }
        });

        // Menghitung rata-rata PE jika ada item yang valid
        if ($validItemsCount > 0) {
            $PE /= $validItemsCount;
        }

        // Menghitung nilai peramalan untuk satu hari ke depan
        $latest_data = Peramalan::orderBy('tanggal', 'desc')->take($rentang)->get();

        if ($latest_data->count() == $rentang) {
            $total_weight = 0;
            $weighted_sum = 0;

            for ($i = 0; $i < $rentang; $i++) {
                $weight = $rentang - $i;
                $total_weight += $weight;
                $weighted_sum += $latest_data[$i]->nilai_aktual * $weight;
            }

            $forecast_tomorrow = $weighted_sum / $total_weight;
        } else {
            $forecast_tomorrow = null;
        }

        $latest_data = Peramalan::orderBy('tanggal', 'desc')->take($rentang)->get();

        if ($latest_data->count() == $rentang) {
            $total_weight = 0;
            $weighted_sum = 0;
        
            for ($i = 0; $i < $rentang; $i++) {
                $weight = $rentang - $i;
                $total_weight += $weight;
                $weighted_sum += $latest_data[$i]->nilai_aktual * $weight;
            }
        
            $forecast_tomorrow = $weighted_sum / $total_weight;
        
            // Jika nilai peramalan kurang dari 0, set menjadi 0
            if ($forecast_tomorrow < 0) {
                $forecast_tomorrow = 0;
            }
        } else {
            $forecast_tomorrow = null;
        }
        
        // Kembalikan respons dengan nilai peramalan
        return response()->json(['nilai_peramalan' => $forecast_tomorrow]);
    }
}
