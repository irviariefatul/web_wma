<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="card gradient-1">
                <div class="card-body">
                    <h3 class="card-title text-white">Peramalan Nilai IHSG 1 Hari Kedepan</h3>
                    <div class="d-inline-block">
                        <h2 id="nilai_besok" class="text-white">0</h2>
                        <p class="text-white mb-0">Hari libur tidak termasuk.</p>
                    </div>
                    <span class="float-right display-5 opacity-5"><i class="fa fa-search"></i></span>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="active-member">
                        <div class="table-responsive">
                            <table class="table table-xs mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nilai Aktual</th>
                                        <th>Nilai Peramalan</th>
                                        <th>PE (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($peramalans as $p)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $p->tanggal)->format('d-m-Y') }}
                                            </td>
                                            <td id="nilai_aktual_{{ $p->id }}">
                                                {{ number_format($p->nilai_aktual, 2, '.', ',') }}</td>
                                            <td id="nilai_peramalan_{{ $p->id }}">
                                                {{ $p->nilai_peramalan !== null ? number_format($p->nilai_peramalan, 2, '.', ',') : '-' }}
                                            </td>
                                            <td>{{ $p->nilai_error !== null ? number_format($p->nilai_error, 4, '.', ',') . '%' : '-' }}
                                            </td>
                                        </tr>
                                        <script>
                                            hitungTotal();
                                        </script>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <span>Keterangan</span><br>
                                            <p>PE : Percentage Error</p>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Grafik nilai Aktual IHSG</h4>
                            <canvas id="lineChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Grafik nilai Peramalan IHSG</h4>
                            <canvas id="peramalanChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Grafik Perbandingan Nilai Aktual dan Nilai Peramalan IHSG
                            </h4>
                            <canvas id="comparisonChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
