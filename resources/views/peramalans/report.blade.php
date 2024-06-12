<!DOCTYPE html>
<html>

<head>
    <title>Hasil Peramalan WMA</title>
    <style type="text/css">
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    <h3 align="center">Laporan Hasil Peramalan Menggunakan Metode Weighted Moving Average (WMA)</h3>
    @php
        $validCount = 0;
        $totalError = 0;

        // Mengurutkan array berdasarkan tanggal
        usort($peramalanArray, function ($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        $tanggalAwal = $peramalanArray[0]['tanggal'];
        $tanggalAkhir = end($peramalanArray)['tanggal'];
    @endphp
    <p><b>Periode</b>: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalAkhir)->format('d F Y') }} -
        {{ \Carbon\Carbon::createFromFormat('Y-m-d', $tanggalAwal)->format('d F Y') }}</p>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nilai Aktual</th>
                    <th>Nilai Peramalan</th>
                    <th>Error (%)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 0; @endphp
                @foreach ($peramalanArray as $p)
                    <tr>
                        @php
                            if (!is_null($p['nilai_error'])) {
                                $totalError += $p['nilai_error'];
                                $validCount++; // Hitung jumlah data yang valid
                            }
                        @endphp
                        <td>{{ ++$no }}</td>
                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $p['tanggal'])->format('d-m-Y') }}</td>
                        <td id="nilai_aktual_{{ $p['id'] }}">{{ number_format($p['nilai_aktual'], 2, '.', ',') }}
                        </td>
                        <td id="nilai_peramalan_{{ $p['id'] }}">
                            {{ $p['nilai_peramalan'] !== null ? number_format($p['nilai_peramalan'], 2, '.', ',') : '-' }}
                        </td>
                        <td>{{ $p['nilai_error'] !== null ? number_format($p['nilai_error'], 4, '.', ',') . '%' : '-' }}
                        </td>
                    </tr>
                @endforeach
                @php
                    $averageMAPE = $validCount > 0 ? $totalError / $validCount : 0;
                @endphp
            </tbody>
            <tfoot style="font-weight: bold;">
                <tr>
                    <td colspan="4">MAPE</td>
                    <td id="total_mape">{{ number_format($averageMAPE, 4, '.', ',') }}%</td>
                </tr>
                <tr>
                    <td colspan="4">Peramalan IHSG 1 Hari Kedepan</td>
                    <td id="forecast_tomorrow">{{ number_format($forecast_tomorrow, 2, '.', ',') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
