<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>WMA</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('quixlab/images/logo.png') }}">
    <!-- Pignose Calender -->
    <link href="{{ asset('quixlab/./plugins/pg-calendar/css/pignose.calendar.min.css') }}" rel="stylesheet">
    <!-- Font -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <!-- Chartist -->
    <link rel="stylesheet" href="{{ asset('quixlab/./plugins/chartist/css/chartist.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('quixlab/./plugins/chartist-plugin-tooltips/css/chartist-plugin-tooltip.css') }}">
    <!-- Custom Stylesheet -->
    <link href="{{ asset('quixlab/css/style.css') }}" rel="stylesheet">
    <!-- Tabel -->
    <link href="{{ asset('quixlab/./plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link
        href="{{ asset('quixlab/./plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}"
        rel="stylesheet">
    <!-- Date picker plugins css -->
    <link href="{{ asset('quixlab/./plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }} " rel="stylesheet">
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3"
                    stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <div class="brand-logo">
                <a href="{{ route('home') }}">
                    <b class="logo-abbr"><img src="{{ asset('quixlab/images/logo.png') }}"> </b>
                    <span class="logo-compact"><img src="{{ asset('quixlab/./images/logo-compact.png') }}"></span>
                    <span class="brand-title">
                        <img src="{{ asset('quixlab/images/logo-text.png') }}">
                    </span>
                </a>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content clearfix">
                <div class="header-right">
                    <ul class="clearfix">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item nav-profile"><a class="nav-link"
                                        href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            @endif
                        @else
                            <li class="nav-item nav-profile dropdown">
                                <a class="nav-link dropdown-toggle pl-0 pr-0" href="#" data-toggle="dropdown"
                                    id="profileDropdown" aria-expanded="false">
                                    <i class="typcn typcn-user-outline text-white"></i>
                                    <span class="nav-profile-name">{{ Auth::user()->name }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                                    aria-labelledby="profileDropdown">
                                    {{-- <a class="dropdown-item" href="{{ route('profil') }}">
                                        <i class="typcn typcn-user-outline text-gray"></i>
                                        Profil
                                    </a> --}}
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="typcn typcn-power-outline text-gray"></i>
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body2">
            @include('diagram')

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

            <script>
                $(document).ready(function() {
                    hitungTotal();

                    function hitungTotal() {
                        // Fungsi untuk melakukan format angka
                        function numberFormat(number, decimals, dec_point, thousands_sep) {
                            // Pastikan number adalah angka
                            number = parseFloat(number);
                            if (isNaN(number)) {
                                return '-';
                            }

                            // Tentukan tanda desimal, ribuan, dan jumlah desimal
                            decimals = decimals || 0;
                            dec_point = dec_point || '.';
                            thousands_sep = thousands_sep || ',';

                            // Format angka ke dalam string
                            var parts = number.toFixed(decimals).toString().split('.');
                            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

                            // Gabungkan bagian integer dan desimal dengan tanda desimal
                            return parts.join(dec_point);
                        }

                        // Mengambil tanggal terakhir
                        var tanggalTerakhir = $('tbody tr:last-child td:nth-child(1)').text();

                        // Melakukan permintaan Ajax
                        $.ajax({
                            url: '/hitung-nilai-peramalan2',
                            type: 'GET',
                            data: {
                                tanggal: tanggalTerakhir
                            },
                            success: function(response) {
                                // Ambil nilai peramalan dan format menggunakan fungsi numberFormat
                                var formattedNilaiPeramalan = numberFormat(response.nilai_peramalan, 2, '.',
                                    ',');

                                // Tampilkan nilai peramalan
                                $('#nilai_besok').text(formattedNilaiPeramalan);
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });

                $(function() {
                    "use strict";

                    var ctx1 = document.getElementById('lineChart').getContext('2d');
                    var ctx2 = document.getElementById('peramalanChart').getContext('2d');
                    var ctx3 = document.getElementById('comparisonChart').getContext('2d');

                    // Data untuk grafik nilai aktual IHSG
                    var labels1 = {!! json_encode($peramalans->pluck('tanggal')) !!};
                    var values1 = {!! json_encode($peramalans->pluck('nilai_aktual')) !!};

                    // Data untuk grafik nilai peramalan IHSG
                    var labels2 = {!! json_encode($peramalans->pluck('tanggal')) !!};
                    var values2 = {!! json_encode($peramalans->pluck('nilai_peramalan')) !!};

                    // Ambil nilai minimum dari kedua array
                    var minYValue = Math.min(...values1, ...values2);

                    // Ambil 5 data terakhir
                    var lastIndex = Math.max(values1.length - 5, 0);
                    var last5Labels = labels1.slice(lastIndex);
                    var last5Values1 = values1.slice(lastIndex);
                    var last5Values2 = values2.slice(lastIndex);

                    // Data untuk grafik perbandingan
                    var comparisonData = [];
                    for (var i = 0; i < last5Values1.length; i++) {
                        comparisonData.push({
                            x: last5Labels[i],
                            y1: last5Values1[i],
                            y2: last5Values2[i]
                        });
                    }

                    var comparisonChart = new Chart(ctx3, {
                        type: 'line',
                        data: {
                            datasets: [{
                                label: 'Nilai Aktual IHSG',
                                data: comparisonData.map(item => ({
                                    x: item.x,
                                    y: parseFloat(item.y1.toFixed(
                                        2)) // Limit to two decimal places
                                })),
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                fill: false
                            }, {
                                label: 'Nilai Peramalan IHSG',
                                data: comparisonData.map(item => ({
                                    x: item.x,
                                    y: parseFloat(item.y2.toFixed(
                                        2)) // Limit to two decimal places
                                })),
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                fill: false
                            }]
                        },
                        options: {
                            scales: {
                                xAxes: [{
                                    type: 'time',
                                    time: {
                                        unit: 'day',
                                        displayFormats: {
                                            day: 'DD-MM-YYYY'
                                        }
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: false,
                                        min: minYValue
                                    }
                                }]
                            },
                            tooltips: {
                                mode: 'index',
                                intersect: false
                            }
                        }
                    });

                    var lineChart = new Chart(ctx1, {
                        type: 'line',
                        data: {
                            labels: labels1,
                            datasets: [{
                                label: 'Nilai Aktual IHSG',
                                data: values1.map(value => parseFloat(value.toFixed(
                                    2))), // Limit to two decimal places
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                fill: false
                            }]
                        },
                        options: {
                            scales: {
                                xAxes: [{
                                    type: 'time',
                                    time: {
                                        unit: 'day',
                                        displayFormats: {
                                            day: 'DD-MM-YYYY'
                                        }
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: false,
                                        min: minYValue
                                    }
                                }]
                            }
                        }
                    });

                    var peramalanChart = new Chart(ctx2, {
                        type: 'line',
                        data: {
                            labels: labels2,
                            datasets: [{
                                label: 'Nilai Peramalan IHSG',
                                data: values2.map(value => parseFloat(value.toFixed(
                                    2))), // Limit to two decimal places
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                fill: false
                            }]
                        },
                        options: {
                            scales: {
                                xAxes: [{
                                    type: 'time',
                                    time: {
                                        unit: 'day',
                                        displayFormats: {
                                            day: 'DD-MM-YYYY'
                                        }
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: false,
                                        min: minYValue
                                    }
                                }]
                            }
                        }
                    });
                });
            </script>
            <!-- #/ container -->
        </div>
        <!--**********************************
            Content body end
        ***********************************-->

        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer2">
            <div class="copyright2">
                <p>Designed & Developed by Irvi Ariefatul Julia Putri 2024</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <script src="{{ asset('quixlab/plugins/common/common.min.js') }}"></script>
    <script src="{{ asset('quixlab/js/custom.min.js') }}"></script>
    <script src="{{ asset('quixlab/js/settings.js') }}"></script>
    <script src="{{ asset('quixlab/js/gleek.js') }}"></script>
    <script src="{{ asset('quixlab/js/styleSwitcher.js') }}"></script>

    <!-- Chartjs -->
    <script src="{{ asset('quixlab/./plugins/chart.js/Chart.bundle.min.js') }}"></script>
    <!-- Circle progress -->
    <script src="{{ asset('quixlab/./plugins/circle-progress/circle-progress.min.js') }}"></script>
    <!-- Datamap -->
    <script src="{{ asset('quixlab/./plugins/d3v3/index.js') }}"></script>
    <script src="{{ asset('quixlab/./plugins/topojson/topojson.min.js') }}"></script>
    <script src="{{ asset('quixlab/./plugins/datamaps/datamaps.world.min.js') }}"></script>
    <!-- Morrisjs -->
    <script src="{{ asset('quixlab/./plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('quixlab/./plugins/morris/morris.min.js') }}"></script>
    <!-- ChartistJS -->
    <script src="{{ asset('quixlab/./plugins/chartist/js/chartist.min.js') }}"></script>
    <script src="{{ asset('quixlab/./plugins/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js') }}"></script>

    <script src="{{ asset('quixlab/./plugins/moment/moment.js') }}"></script>
    <script
        src="{{ asset('quixlab/./plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}">
    </script>
    <!-- Date Picker Plugin JavaScript -->
    <script src="{{ asset('quixlab/./plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    <script src="{{ asset('quixlab/./js/dashboard/dashboard-1.js') }}"></script>
    <script src="{{ asset('quixlab/./plugins/tables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('quixlab/./plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('quixlab/./plugins/tables/js/datatable-init/datatable-basic.min.js') }}"></script>

    <script src="{{ asset('quixlab/./js/plugins-init/form-pickers-init.js') }}"></script>

</body>

</html>
