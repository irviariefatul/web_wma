@extends('layouts.appDashboard')

@section('content')
    @include('diagram')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script>
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
@endsection
