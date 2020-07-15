@inject('dashServ','App\Services\DashboardServices')

@extends('layout')
@section('body')
<div class="row">
    @include('dashboard.count_box', ['title' => 'Thành viên', 'data' => $dashServ->userCount('member'),
    'icon' => 'fa-users', 'color' => 'success' ])
    @include('dashboard.count_box', ['title' => 'Trường học', 'data' => $dashServ->userCount('school'),
    'icon' => 'fa-university', 'color' => 'warning'])
    @include('dashboard.count_box', ['title' => 'Giảng Viên', 'data' => $dashServ->userCount('teacher'),
    'icon' => 'fa-chalkboard-teacher', 'color' => 'info'])
    @include('dashboard.count_box', ['title' => 'Khóa học', 'data' => $dashServ->itemCount(),
    'icon' => 'fa-fire', 'color' => 'danger'])
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Người dùng đăng ký mới</h6>
            </div>
            <div class="card-body p-0" style="min-height: 300px;">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Top User</h6>
            </div>
            <div class="card-body p-0" style="min-height: 300px;">
                <table class="table">
                    <tbody>
                        @foreach($topUsers as $user)
                        <tr>
                            <th>{{ $user->name }}</th>
                            <td>{{ $user->reg_num }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Top Khóa học</h6>
            </div>
            <div class="card-body p-0" style="min-height: 300px;">
            <table class="table">
                    <tbody>
                        @foreach($topItems as $item)
                        <tr>
                            <th>{{ $item->title }}</th>
                            <td>{{ $item->reg_num }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>
@endsection
@section('jscript')
<script src="/cdn/vendor/chart.js/Chart.min.js"></script>
<script>
    var chartData = JSON.parse("{{ $newUserChartData }}".replace(/&quot;/g, '"'));
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData['labels'],
            datasets: [{
                label: "User",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: chartData['data'],
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'Week'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: true
                    },
                    ticks: {
                        maxTicksLimit: 12
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return value;
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + tooltipItem.yLabel;
                    }
                }
            }
        }
    });
</script>
@endsection