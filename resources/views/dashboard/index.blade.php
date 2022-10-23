@inject('dashServ','App\Services\DashboardServices')
@php
$dashServ->init(@request('dateF') ?? date('Y-m-d', strtotime('-30 days')), @request('dateT'));
@endphp

@extends('layout')
@section('rightFixedTop')
<form>
    <div class="d-flex flex-row pt-3">
        <div class="form-group mr-2">
            <input type="date" class="form-control" name="dateF" value="{{ @request('dateF') ?? date('Y-m-d', strtotime('-30 days')) }}" placeholder="Từ">
        </div>
        <div class="form-grou mr-2">
            <input type="date" class="form-control" name="dateT" value="{{ @request('dateT') ?? date('Y-m-d') }}" placeholder="Đến">
        </div>
        <div class="form-group">
            <button class="btn btn-success">Xem</button>
        </div>
    </div>
</form>
@endsection
@section('body')
<div class="row">
@include('dashboard.count_box', ['title' => 'Khóa học trong kỳ', 'data' => $dashServ->itemCount(false),
    'icon' => 'fa-fire', 'color' => 'danger'])
    @include('dashboard.count_box', ['title' => 'Thành viên trong kỳ', 'data' => $dashServ->userCount('member', false),
    'icon' => 'fa-users', 'color' => 'success' ])
    @include('dashboard.count_box', ['title' => 'Doanh thu trong kỳ', 'data' => $dashServ->gmv(false),
    'icon' => 'fa-dollar-sign', 'color' => 'primary' ])
    @include('dashboard.count_box', ['title' => 'Tổng Giảng Viên', 'data' => $dashServ->userCount('teacher'),
    'icon' => 'fa-chalkboard-teacher', 'color' => 'info'])

    @include('dashboard.count_box', ['title' => 'Tổng Khóa học', 'data' => $dashServ->itemCount(),
    'icon' => 'fa-fire', 'color' => 'danger'])
    @include('dashboard.count_box', ['title' => 'Tổng Thành viên', 'data' => $dashServ->userCount('member'),
    'icon' => 'fa-users', 'color' => 'success' ])
    @include('dashboard.count_box', ['title' => 'Tổng Doanh thu', 'data' => $dashServ->gmv(),
    'icon' => 'fa-dollar-sign', 'color' => 'primary' ])
    @include('dashboard.count_box', ['title' => 'Tổng Trường học', 'data' => $dashServ->userCount('school'),
    'icon' => 'fa-university', 'color' => 'info'])

</div>
<div class="row">
    <div class="col-md-6">
        <div class="card border-bottom-primary shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Người dùng đăng ký mới</h6>
            </div>
            <div class="card-body p-0" style="min-height: 300px;">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-bottom-success shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Top GV/Trường được mua</h6>
            </div>
            <div class="card-body p-0" style="min-height: 300px;">
                <table class="table">
                    <tbody>
                        @foreach($dashServ->topUser() as $user)
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
        <div class="card border-bottom-danger shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Top Khóa học được mua</h6>
            </div>
            <div class="card-body p-0" style="min-height: 300px;">
                <table class="table">
                    <tbody>
                        @foreach($dashServ->topItem() as $item)
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
    var chartData = JSON.parse("{{ json_encode($dashServ->userCreatedByDay()) }}".replace(/&quot;/g, '"'));
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData['labels'],
            datasets: [{
                label: "Người dùng mới",
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
                        unit: 'Ngày'
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