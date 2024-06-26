<?php

namespace App\Http\Controllers;

use App\Imports\PeramalanImport;
use Illuminate\Http\Request;
use App\Models\Peramalan;
use App\Utils\PeramalanUtils;
use App\Utils\YahooFinanceScraper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade as PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Http;
use KubAT\PhpSimple\HtmlDomParser;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ForecastTemplateExport;

use Symfony\Component\HttpClient\HttpClient;


class PeramalanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $peramalans = Peramalan::all();
        return view('peramalans.index', ['peramalan' => $peramalans]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('peramalans.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi input sesuai kebutuhan
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'nilai_aktual' => 'required|numeric',
        ]);
        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
        //cek tanggal yang dimasukkan lebih dari tanggal terakhir di database
        $lastData = Peramalan::orderBy('tanggal', 'desc')->first();
        $firstData = Peramalan::orderBy('tanggal', 'asc')->first();

        if ($lastData && $firstData) {
            $lastDateTimestamp = Carbon::parse($lastData->tanggal)->timestamp;
            $inputDateTimestamp = Carbon::parse($validatedData['tanggal'])->timestamp;
            $firstDateTimestamp = Carbon::parse($firstData->tanggal)->timestamp;
            if ($inputDateTimestamp >= $lastDateTimestamp || $inputDateTimestamp <= $firstDateTimestamp) {
                return redirect()->route('peramalans.index')->with('error', 'Tanggal yang dimasukkan harus sebelum tanggal terakhir dan sesudah tanggal pertama di data peramalan.');
            }
        } else {
            return redirect()->route('peramalans.index')->with('error', 'Data peramalan tidak ditemukan.');
        }

        //ambil semua data yang kurang dari tanggal yang diinput
        $data = Peramalan::where('tanggal', '>=', $validatedData['tanggal'])->orderBy('tanggal', 'asc')->get();
        $jumlah_bobot = $data->count();

        if ($jumlah_bobot === 0) {
            return redirect()->route('peramalans.index')->with('error', 'Tidak ada data peramalan yang tersedia untuk tanggal yang dimasukkan.');
        }

        // Hitung Weighted Moving Average
        $weightedSum = 0;
        $totalWeight = 0;

        // Hitung total bobot (sum of days)
        for ($i = 1; $i <= $jumlah_bobot; $i++) {
            $totalWeight += $i;
        }

        // Hitung Weighted Moving Average
        for ($i = 0; $i < $jumlah_bobot; $i++) {
            $bobot = $jumlah_bobot - $i;
            $weightedValue = $data[$i]->nilai_aktual * ($bobot / $totalWeight);
            $weightedSum += $weightedValue;
        }

        $WMA = $weightedSum;
        $MAPE = abs(($validatedData['nilai_aktual'] - $WMA) / $validatedData['nilai_aktual']) * 100;

        $addDay = Carbon::parse($lastData->tanggal)->addDay();
        $oneDay = Carbon::parse($addDay)->format('Y-m-d');

        // Buat objek Peramalan dengan data yang divalidasi dan hasil perhitungan
        $peramalan = new Peramalan([
            'user_id' => Auth::id(),
            'tanggal' => $oneDay,
            'nilai_aktual' => $validatedData['nilai_aktual'],
            'nilai_peramalan' => $WMA,
            'nilai_error' => $MAPE
        ]);

        // Simpan objek Peramalan ke database
        $peramalan->save();

        // Redirect atau lakukan apa yang perlu setelah berhasil menyimpan
        return redirect()->route('peramalans.index')->with('status', 'Data peramalan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Mengambil data peramalan berdasarkan ID
        $peramalan = Peramalan::findOrFail($id);

        // Mengirim data peramalan ke view untuk proses edit
        return view('peramalans.edit', compact('peramalan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi input sesuai kebutuhan
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'nilai_aktual' => 'required|numeric',
        ]);

        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
        $rentang = Peramalan::where('nilai_peramalan', null)->where('nilai_error', null)->count();

        // Mengambil data aktual terbaru sesuai rentang yang diberikan
        $data_aktual_terbaru = Peramalan::where('tanggal', '<', $validatedData['tanggal'])
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
            $PE = abs(($validatedData['nilai_aktual'] - $nilai_peramalan) / $validatedData['nilai_aktual'] * 100);
        } else {
            // Set nilai_peramalan dan MAPE menjadi null jika data tidak tersedia
            $nilai_peramalan = null;
            $PE = null;
        }

        // Mengambil objek Peramalan berdasarkan ID
        $peramalan = Peramalan::findOrFail($id);

        // Mengupdate data peramalan dengan data yang divalidasi dan hasil perhitungan
        $peramalan->user_id = Auth::id();
        $peramalan->tanggal = $validatedData['tanggal'];
        $peramalan->nilai_aktual = $validatedData['nilai_aktual'];
        $peramalan->nilai_peramalan = $nilai_peramalan;
        $peramalan->nilai_error = $PE;

        // Simpan perubahan data peramalan ke database
        $peramalan->save();

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

                $nilai_peramalan = $weighted_sum / $total_weight;

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

        // Redirect atau lakukan apa yang perlu setelah berhasil mengupdate
        return redirect()->route('peramalans.index')->with('status', 'Data berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
        $rentang = Peramalan::where('nilai_peramalan', null)->where('nilai_error', null)->count();

        $peramalan = Peramalan::find($id);
        $peramalan->delete();

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

                $nilai_peramalan = $weighted_sum / $total_weight;

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

        return redirect()->route('peramalans.index');
    }

    function generatePDF()
    {
        /*
        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
        $peramalans = Peramalan::latest('tanggal')->limit(5)->get();
        return view('peramalans.pdf', compact('peramalans'));
        */
        
        // Ambil data peramalan dari database
        $peramalans = Peramalan::orderBy('tanggal', 'asc')->get();
        $peramalanArray = json_decode($peramalans->toJson(), true);

        // Hitung nilai peramalan untuk 1 hari ke depan
        $rentang = Peramalan::whereNull('nilai_peramalan')->whereNull('nilai_error')->count();
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

        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

        // Load view dengan data yang sudah didecode
        $view = view('peramalans.report', compact('peramalanArray', 'forecast_tomorrow'))->render();

        // Konfigurasi DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Buat objek Dompdf dan muat view
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($view);

        // Render PDF
        $dompdf->render();

        // Kirim file PDF ke browser untuk didownload
        return $dompdf->stream("peramalans.pdf", ["Attachment" => 1]);

    }

    public function hitungNilaiPeramalan(Request $request)
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

    public function scrap()
    {
        $scraper = new YahooFinanceScraper();
        $crawler = $scraper->scrape();
        try {
            foreach ($crawler as $key => $value) {
                // $rentang = Peramalan::where('nilai_peramalan', null)->where('nilai_error', null)->count();
                $rentang = 2;
                if (isset($value['adj_close']) && $value['adj_close'] !== null && $value['adj_close'] !== 0) {
                    PeramalanUtils::scrap($rentang, $value['date'], $value['adj_close']);
                }
            }
        } catch (\DivisionByZeroError $e) {
            // Menangkap kesalahan division by zero dan langsung redirect
            return redirect()->route('peramalans.index');
        } 
        /*
        if(isset($value['adj_close']) && $value['adj_close'] !== null && $value['adj_close'] !== 0)
        return response()->json([
            'status' => true,
            'data' => 'success scrap !'
        ]);
        */
        return redirect()->route('peramalans.index');
    }

    public function scrapRequest(Request $request)
    {
        $request->validate([
            'bobot' => 'required|numeric'
        ]);

        $data_aktual = Peramalan::orderBy('tanggal', 'asc')->get()->count();
        if ($data_aktual == 0) {
            return redirect()->route('peramalans.index')->with('error', 'Data peramalan masih kosong, harap tambahkan data terlebih dahulu.');
        }

        $scraper = new YahooFinanceScraper();
        $crawler = $scraper->scrape();
        foreach ($crawler as $key => $value) {
            PeramalanUtils::changeWeight($request->bobot, $value['date'], $value['adj_close']);
        }
        return redirect()->route('peramalans.index')->with('status', 'Data rentang hari berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        if ($request->hasFile('csv')) {
            $file = $request->file('csv');

            // Pengecekan format file
            if ($file->getClientOriginalExtension() !== 'csv') {
                return back()->with("error", "Format file salah. Harap unggah file dengan format .csv");
            }

            Excel::import(new PeramalanImport(), $file);
            return back()->with("status", "Import data csv berhasil");
        } else {
            // Tindakan jika file CSV tidak disertakan dalam permintaan
            return back()->with("error", "Masukkan file csv");
        }   
    }

    public function downloadTemplate()
    {
        return Excel::download(new ForecastTemplateExport, 'forecast_template.csv');
    }

    public function hitungTotalMAPE(Request $request)
    {
        // Ambil data peramalan dari database
        $peramalans = Peramalan::all();

        $totalError = 0;
        $validCount = 0;

        // Loop melalui setiap data peramalan
        foreach ($peramalans as $peramalan) {
            $nilaiError = $peramalan->nilai_error;

            // Hitung total error jika nilaiError tidak null
            if (!is_null($nilaiError)) {
                $totalError += $nilaiError;
                $validCount++;
            }
        }

        // Hitung rata-rata MAPE
        $averageMAPE = $validCount > 0 ? $totalError / $validCount : 0;

        // Bulatkan rata-rata MAPE ke 4 desimal
        $roundedMAPE = round($averageMAPE, 4);

        // Kembalikan hasil sebagai response JSON
        return response()->json(['total_mape' => $roundedMAPE]);
    }

    public function resetData()
    {
        Peramalan::truncate(); // Menghapus semua data di tabel peramalans
        return redirect()->back()->with('success', 'Data berhasil direset.');
    }

}
