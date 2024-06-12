@extends('layouts.appDashboard')

@section('content')
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div class="d-flex flex-wrap justify-content-between">
                            <h4 class="card-title">Forecasting</h4>
                            <div>
                                <a href="{{ route('scraping') }}" class="btn btn-success" id="pdfButton">
                                    Scraping<span class="btn-icon-right"><i class="fa fa-globe"></i>
                                </a>
                                <a href="{{ url('/download-forecast-template') }}" class="btn btn-secondary">
                                    CSV Template<span class="btn-icon-right"><i class="fa fa-file-excel-o"></i>
                                </a>
                                <div class="btn btn-primary" id="csvButton">
                                    Import CSV<span class="btn-icon-right"><i class="fa fa-upload"></i></span>
                                </div>
                            </div>
                        </div>
                        @include('peramalans.create')
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr style="text-align: center;">
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nilai Aktual</th>
                                        <th>Nilai Peramalan</th>
                                        <th>PE (%)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 0; @endphp
                                    @php
                                        // Urutkan $peramalan berdasarkan tanggal terbaru
                                        $peramalan = \App\Models\Peramalan::orderBy('tanggal', 'desc')->get();
                                    @endphp
                                    @foreach ($peramalan as $p)
                                        <tr style="text-align: center;">
                                            <td>{{ ++$no }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d', $p->tanggal)->format('d-m-Y') }}
                                            </td>
                                            <td id="nilai_aktual_{{ $p->id }}">
                                                {{ number_format($p->nilai_aktual, 2, '.', ',') }}</td>
                                            <td id="nilai_peramalan_{{ $p->id }}">
                                                {{ $p->nilai_peramalan !== null ? number_format($p->nilai_peramalan, 2, '.', ',') : '-' }}
                                            </td>
                                            <td>{{ $p->nilai_error !== null ? number_format($p->nilai_error, 4, '.', ',') . '%' : '-' }}
                                            </td>
                                            <td>
                                                <form action="/peramalans/{{ $p->id }}" method="POST">
                                                    @method('GET')
                                                    <a href="/peramalans/{{ $p->id }}/edit"
                                                        class="btn mb-1 btn-info">Edit<span class="btn-icon-right"><i
                                                                class="fa fa-edit"></i></span>
                                                    </a>
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" name="delete" class="btn mb-1 btn-danger">
                                                        Delete<span class="btn-icon-right"><i
                                                                class="fa fa-trash"></i></span></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <!-- Baris MAPE -->
                                    <tr style="text-align: center;">
                                        <td colspan="4"><b>MAPE</b></td>
                                        <td id="total_mape" style="font-weight: bold;">0</td>
                                        <td></td>
                                    </tr>
                                    <!-- Baris Peramlan 1 hari kedepan -->
                                    <tr style="text-align: center;">
                                        <td colspan="4"><b>Nilai Peramalan 1 hari kedepan</b></td>
                                        <td id="nilai_besok" style="font-weight: bold;">0</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-4 float-right">
                            <a href="{{ route('reset-data') }}" class="btn btn-danger ml-2" id="resetButton"
                                onclick="return confirm('Apakah Anda yakin ingin mereset semua data?');">
                                Reset Data
                                <span class="btn-icon-right"><i class="fa fa-refresh"></i></span>
                            </a>
                            <a href="{{ route('report-pdf') }}" class="btn btn-success" id="pdfButton">
                                Download PDF
                                <span class="btn-icon-right"><i class="fa fa-download"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Upload CSV</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>


                <form action="{{ route('import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input class="form-control" type="file" accept=".csv" name="csv" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>

                <!-- Modal Body -->


                <!-- Modal Footer -->


            </div>
        </div>
    </div>

    <script>
        let csvButton = document.getElementById('csvButton')
        csvButton.addEventListener("click", function() {
            // Memunculkan modal
            $('#myModal').modal('show');
        });
    </script>
@endsection
