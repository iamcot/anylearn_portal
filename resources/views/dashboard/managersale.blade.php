@inject('dashServ', 'App\Services\DashboardServices')
@extends('layout')
<style>
    .my-custom-scrollbar {
        position: relative;
        height: 300px;
        overflow: auto;
    }

    .table-wrapper-scroll-y {
        display: block;
    }
</style>
@section('body')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h2><strong class="my-4 text-primary fw-bold">Quản lý SALE</strong></h2>
                    </div>
                    <div class="col-md-8">
                        <form class="form-check-inline mt-3">
                            <input type="radio" class="form-check-input mx-2" name="filter" id="radio1" value="time">
                            Theo
                            thời gian
                            <input type="radio" class="form-check-input mx-2" name="filter" id="radio2"
                                value="seller"> Theo người
                            bán
                            <input type="radio" class="form-check-input mx-2" name="filter" id="radio3"
                                value="product"> Theo hàng
                            hóa
                            <input type="radio" class="form-check-input mx-2" name="filter" id="radio4"
                                value="buyer"> Theo khách hàng
                        </form>

                    </div>
                </div>
                <!-- Filter -->
                <div class="my-3">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="date" class="form-control mx-1" name="start_date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control mx-1" name="end_date">
                        </div>
                        <button type="button" class="btn btn-outline-primary mx-1">Tuần</button>
                        <button type="button" class="btn btn-outline-primary mx-1">Tháng</button>
                        <button type="button" class="btn btn-outline-primary mx-1">Quý</button>
                        <button type="button" class="btn btn-primary mx-1">Xem biểu đồ</button>
                    </div>

                </div>
                <!-- Nội dung -->
                <div class="row">
                    {{--
                    <div class="col-lg-3 col-md-6 ">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="card-title">Số khách hàng</h6>
                                <h6 class="card-subtitle fw-bold mb-2 text-white">50</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 ">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="card-title">Tổng số đơn</h6>
                                <h6 class="card-subtitle fw-bold mb-2 text-white">100</h6>
                            </div>
                        </div>
                    </div> --}}
                    @include('dashboard.count_box', [
                        'title' => 'Tiền bán hàng',
                        'data' => number_format($dashServ->itemCount(false), 0, ',', '.'),
                        'icon' => 'fa-dollar-sign',
                        'color' => 'primary',
                    ])
                    @include('dashboard.count_box', [
                        'title' => 'Số khách hàng',
                        'data' => number_format($dashServ->gmv(false), 0, ',', '.'),
                        'icon' => 'fa-users',
                        'color' => 'primary',
                    ])
                    @include('dashboard.count_box', [
                        'title' => 'Số Đơn hàng',
                        'data' => number_format($dashServ->userCount('member', false), 0, ',', '.'),
                        'icon' => 'fa-shopping-cart',
                        'color' => 'success',
                    ])

                    @include('dashboard.count_box', [
                        'title' => 'Tổng số đơn',
                        'data' => number_format($dashServ->userCount('teacher'), 0, ',', '.'),
                        'icon' => 'fa-shopping-cart',
                        'color' => 'success',
                    ])
                </div>
                {{-- <div class="table-wrapper-scroll-y my-custom-scrollbar"> --}}
                <!-- Bảng hiển thị thông tin -->
                <table class="table table-bordered table-striped my-3" id="my-table">
                    <thead>
                        <tr>
                            <th scope="col">Người bán</th>
                            <th scope="col">Tên khóa học</th>
                            <th scope="col">Số tiền</th>
                            <th scope="col">Tên khách hàng</th>
                            <th scope="col">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @foreach ($data as $row)
                            <tr>
                                <td scope="row">{{ $row->seller_name }}</td>
                                <td width=40%>{{ $row->title }}</td>
                                <td>{{ number_format($row->unit_price) }}</td>
                                <td>{{ $row->buyer_name }}</td>
                                <td>{{ $row->created_at }}</td>
                            </tr>
                        @endforeach --}}
                    </tbody>
                </table>
            </div>
            {{-- </div> --}}
        </div>


    </div>
@endsection
@section('jscript')
    @parent
    <script>
        $(function() {
            var data = <?php echo json_encode($data); ?>;
            var tbody = $("#my-table tbody");

            function renderTable(data) {
                // Xóa hết các phần tử trong tbody
                tbody.empty();

                // Sắp xếp dữ liệu theo trường created_at
                data.sort(function(a, b) {
                    return new Date(b.created_at) - new Date(a.created_at);
                });

                // Tạo lại nội dung bảng HTML từ dữ liệu đã được sắp xếp
                $.each(data, function(index, row) {
                    var tr = $("<tr>");
                    tr.append($("<td>").text(row.seller_name));
                    tr.append($("<td width='40%'>").text(row.title));
                    tr.append($("<td>").text(row.unit_price));
                    tr.append($("<td>").text(row.buyer_name));
                    tr.append($("<td>").text(row.created_at));
                    tbody.append(tr);
                });
            }

            // Xử lý sự kiện khi checkbox được click
            $("input[name='filter']").click(function() {
                var filterValue = $(this).val();

                if (filterValue === "time") {
                    // Sắp xếp theo thời gian
                    renderTable(data);
                } else if (filterValue === "seller") {
                    // Sắp xếp theo người bán
                    data.sort(function(a, b) {
                        return a.seller_name.localeCompare(b.seller_name);
                    });
                    renderTable(data);
                } else if (filterValue === "product") {
                    // Sắp xếp theo hàng hóa
                    data.sort(function(a, b) {
                        return a.title.localeCompare(b.title);
                    });
                    renderTable(data);
                } else if (filterValue === "buyer") {
                    // Sắp xếp theo đối tác
                    data.sort(function(a, b) {
                        return a.buyer_name.localeCompare(b.buyer_name);
                    });
                    renderTable(data);
                }
            });
        });


        $(function() {
            var data = <?php echo json_encode($data); ?>;
            // Chuyển đổi dữ liệu sang định dạng JSON
            // Tạo nội dung bảng HTML từ dữ liệu đã có
            var tbody = $("#my-table tbody");
            $.each(data, function(index, row) {
                var tr = $("<tr>");
                tr.append($("<td>").text(row.seller_name));
                tr.append($("<td width = '40%'>").text(row.title));
                tr.append($("<td>").text(row.unit_price));
                tr.append($("<td>").text(row.buyer_name));
                tr.append($("<td>").text(row.created_at));
                tbody.append(tr);
            });
        });
    </script>
@endsection
