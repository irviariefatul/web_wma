<!DOCTYPE html>
<html>

<head>
    <title>Hasil Peramalan WMA</title>
    <style type="text/css">
        body {
            margin: 20px;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            font-size: 20px;
        }

        th {
            font-weight: bold;
            text-align: center;
        }

        h1 {
            font-size: 28px;
        }

        h2 {
            font-size: 26px;
        }

        h3 {
            font-size: 24px;
        }

        h4 {
            font-size: 22px;
        }

        p {
            font-size: 20px;
        }
    </style>
</head>

<body>
    <h2 align="center">Laporan Hasil Peramalan Menggunakan Metode Weighted Moving Average (WMA)</h2>
    @php
        $validCount = 0;
        $totalError = 0;
        $tanggalAwal = $peramalans->first()->tanggal;
        $tanggalAkhir = $peramalans->last()->tanggal;
    @endphp
    <h3><b>Periode</b>: {{ \Carbon\Carbon::parse($tanggalAwal)->format('d F Y') }} -
        {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d F Y') }}</h3>

    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-lg-4 col-sm-6">
                <div class="card gradient-1">
                    <div class="card-body">
                        <h4 class="card-title text-white">Peramalan Nilai IHSG 1 Hari Kedepan*</h4>
                        <div class="d-inline-block">
                            <h4 id="nilai_besok" class="text-white">0</h4>
                            <p class="text-white mb-0">*Hari libur tidak termasuk.</p>
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
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nilai Aktual</th>
                                            <th>Nilai Peramalan</th>
                                            <th>PE (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 0; @endphp
                                        @foreach ($peramalans as $p)
                                            <tr>
                                                <td>{{ ++$no }}</td>
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
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5">
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
                    <div class="col-lg-6 page-break">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Grafik Nilai Aktual IHSG</h4>
                                <canvas id="lineChart" height="150" width="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 page-break">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Grafik Nilai Peramalan IHSG</h4>
                                <canvas id="peramalanChart" height="150" width="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 page-break">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Grafik Perbandingan Nilai Aktual dan Nilai Peramalan IHSG</h4>
                                <canvas id="comparisonChart" height="150" width="600"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button id="downloadPDF">Download PDF</button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>

    <script>
        $(document).ready(function() {
            hitungTotal();

            function hitungTotal() {
                var tanggalTerakhir = $('tbody tr:last-child td:nth-child(1)').text();
                $.ajax({
                    url: '/hitung-nilai-peramalan2',
                    type: 'GET',
                    data: {
                        tanggal: tanggalTerakhir
                    },
                    success: function(response) {
                        var nilaiPeramalanBesok = parseFloat(response.nilai_peramalan).toFixed(2);
                        var formattedNilaiPeramalan = parseFloat(nilaiPeramalanBesok).toLocaleString(
                            'id-ID', {
                                minimumFractionDigits: 2
                            });
                        $('#nilai_besok').text(formattedNilaiPeramalan);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        $(function() {
            var ctx1 = document.getElementById('lineChart').getContext('2d');
            var ctx2 = document.getElementById('peramalanChart').getContext('2d');
            var ctx3 = document.getElementById('comparisonChart').getContext('2d');

            var labels = {!! json_encode(
                $peramalans->pluck('tanggal')->map(function ($tanggal) {
                    return \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
                }),
            ) !!};
            var values1 = {!! json_encode($peramalans->pluck('nilai_aktual')) !!};
            var values2 = {!! json_encode($peramalans->pluck('nilai_peramalan')) !!};

            var minYValue = Math.min(...values1, ...values2.filter(v => v !== null));

            var lineChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nilai Aktual IHSG',
                        data: values1,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            display: true,
                            ticks: {
                                fontSize: 12 // Mengatur ukuran font pada sumbu x
                            },
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: false,
                                min: minYValue,
                                fontSize: 12 // Mengatur ukuran font pada sumbu y
                            },
                        }]
                    },
                    legend: {
                        display: true,
                        labels: {
                            fontSize: 14 // Mengatur ukuran font pada label grafik
                        }
                    }
                }
            });

            var peramalanChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nilai Peramalan IHSG',
                        data: values2,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            display: true,
                            ticks: {
                                fontSize: 12 // Mengatur ukuran font pada sumbu x
                            },
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: false,
                                min: minYValue,
                                fontSize: 12 // Mengatur ukuran font pada sumbu y
                            },
                        }]
                    },
                    legend: {
                        display: true,
                        labels: {
                            fontSize: 14 // Mengatur ukuran font pada label grafik
                        }
                    }
                }
            });

            var comparisonChart = new Chart(ctx3, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nilai Aktual IHSG',
                        data: values1,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        fill: false
                    }, {
                        label: 'Nilai Peramalan IHSG',
                        data: values2,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            display: true,
                            ticks: {
                                fontSize: 12 // Mengatur ukuran font pada sumbu x
                            },
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: false,
                                min: minYValue,
                                fontSize: 12 // Mengatur ukuran font pada sumbu y
                            },
                        }]
                    },
                    legend: {
                        display: true,
                        labels: {
                            fontSize: 14 // Mengatur ukuran font pada label grafik
                        }
                    }
                }
            });
        });
        document.getElementById('downloadPDF').addEventListener('click', function() {
            html2canvas(document.body).then(function(canvas) {
                var imgData = canvas.toDataURL('image/png', 1.0);
                var pdf = new jspdf.jsPDF('p', 'mm', 'a4');
                var imgWidth = 190; // Adjusted to leave margin
                var pageHeight = 295;
                var imgHeight = canvas.height * imgWidth / canvas.width;
                var heightLeft = imgHeight;
                var position = 20; // Start position with margin
                var margin = 10; // Margin for the PDF

                // Adjust the font size for the PDF document
                pdf.setFontSize(14);

                // Add the image
                pdf.addImage(imgData, 'JPEG', margin, position, imgWidth, imgHeight);
                heightLeft -= (pageHeight - position);

                while (heightLeft > 0) {
                    position = heightLeft - imgHeight + margin;
                    pdf.addPage();
                    pdf.addImage(imgData, 'JPEG', margin, position, imgWidth, imgHeight);
                    heightLeft -= (pageHeight - margin);
                }

                pdf.save('laporan_peramalan.pdf');
            });
        });
    </script>
</body>

</html>
