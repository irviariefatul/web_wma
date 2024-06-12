<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peramalan;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $peramalans = Peramalan::latest('tanggal')->limit(5)->get();
        return view('home', compact('peramalans')); // Menggunakan compact() untuk memasukkan variabel peramalans ke dalam view
    }
}
