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
                                value="product"> Theo khóa học
                            <input type="radio" class="form-check-input mx-2" name="filter" id="radio4"
                                value="buyer"> Theo khách hàng
                            <input type="radio" class="form-check-input mx-2" name="filter" id="radio5"
                                value="price"> Theo giá tiền
                        </form>

                    </div>
                </div>
                <!-- Filter -->
                <div class="my-3">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="date" class="form-control mx-1" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control mx-1" name="end_date" id="end_date">
                        </div>
                        <button type="button" name="week" id="week"
                            class="btn btn-outline-primary mx-1">Tuần</button>
                        <button type="button" name="month" id="month"
                            class="btn btn-outline-primary mx-1">Tháng</button>
                        <button type="button" name="quarter" id="quarter"
                            class="btn btn-outline-primary mx-1">Quý</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-warning mx-1">Xóa Bộ Lọc</a>
                        <button type="button" class="btn btn-primary mx-1">Xem biểu đồ</button>
                    </div>

                </div>
                <!-- Nội dung -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tiền bán hàng
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <strong id="total_sales"></strong>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Số khách hàng
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <strong id="total_customer"></strong>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Số Đơn hàng
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <strong id="total_order"></strong>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng số đơn
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <strong id="total"></strong>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            var originalData = <?php echo json_encode($data); ?>;
            var data = <?php echo json_encode($data); ?>;
            var tbody = $("#my-table tbody");

            function renderTable(data) {
                // Xóa hết các phần tử trong tbody
                tbody.empty();
                const buyerCount = new Map();
                // Tạo lại nội dung bảng HTML từ dữ liệu đã được sắp xếp
                var total_sales = 0;
                var order = 0;

                $.each(data, function(index, row) {
                    var tr = $("<tr>");
                    tr.append($("<td>").text(row.seller_name));
                    tr.append($("<td width='40%'>").text(row.title));
                    var unit_price = parseFloat(row.unit_price);
                    tr.append($("<td>").text(unit_price.toLocaleString('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    })));
                    tr.append($("<td>").text(row.buyer_name));
                    tr.append($("<td>").text(row.created_at));
                    tbody.append(tr);

                    if (buyerCount.has(row.buyer_name)) {
                        buyerCount.set(row.buyer_name, buyerCount.get(row.buyer_name) + 1);
                    } else {
                        buyerCount.set(row.buyer_name, 1);
                    }
                    // Cập nhật tổng tiền bán hàng
                    order++;
                    total_sales += unit_price;
                });
                document.getElementById('total_sales').innerHTML = total_sales.toLocaleString('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                });
                var total_customer = 0;
                // Hiển thị số lần xuất hiện của mỗi buyer_name
                buyerCount.forEach((count, buyerName) => {
                    total_customer++;
                });
                document.getElementById('total_customer').innerHTML = total_customer;
                document.getElementById('total_order').innerHTML = order;
                document.getElementById('total').innerHTML = order;
            }
            // Xử lý sự kiện khi checkbox được click
            $(document).on("click", "input[name='filter']", function() {
                var filterValue = $(this).val();
                filterAndApplyFilters(null, filterValue);
            });

            // Lọc dữ liệu theo khoảng thời gian và sử dụng bộ lọc khác
            $(document).on('change', "input[name='start_date'],input[name='end_date']", function() {
                filterAndApplyFilters(null, null);
            });


            $(document).on("click", "button[name=week]", function() {
                filterAndApplyFilters("week", null);
            });

            $(document).on("click", "button[name=month]", function() {
                filterAndApplyFilters("month", null);
            });

            $(document).on("click", "button[name=quarter]", function() {
                filterAndApplyFilters("quarter", null);
            });

            function filterAndApplyFilters(timeRange, filterValue) {
                var startDate, endDate;
                if (timeRange !== null) {
                    // Tính toán startDate và endDate dựa trên timeRange
                    if (timeRange === "week") {
                        startDate = new Date();
                        startDate.setHours(0, 0, 0, 0);
                        startDate.setDate(startDate.getDate() - startDate.getDay());
                        endDate = new Date();
                        endDate.setHours(23, 59, 59, 999);
                        endDate.setDate(startDate.getDate() + 6);
                        const startDateStr = startDate.toISOString().split('T')[0];
                        const endDateStr = endDate.toISOString().split('T')[0];
                        document.querySelector("input[name='start_date']").setAttribute("value", startDateStr);
                        document.querySelector("input[name='end_date']").setAttribute("value", endDateStr);

                    } else if (timeRange === "month") {
                        startDate = new Date();
                        startDate.setDate(1);
                        startDate.setHours(0, 0, 0, 0);
                        endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0);
                        endDate.setHours(23, 59, 59, 999);
                        const startDateStr = startDate.toISOString().split('T')[0];
                        const endDateStr = endDate.toISOString().split('T')[0];
                        document.querySelector("input[name='start_date']").setAttribute("value", startDateStr);
                        document.querySelector("input[name='end_date']").setAttribute("value", endDateStr);
                    } else if (timeRange === "quarter") {
                        var currentDate = new Date();
                        var currentQuarter = Math.floor((currentDate.getMonth() / 3));
                        startDate = new Date(currentDate.getFullYear(), currentQuarter * 3, 1);
                        startDate.setHours(0, 0, 0, 0);
                        endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 3, 0);
                        endDate.setHours(23, 59, 59, 999);
                        const startDateStr = startDate.toISOString().split('T')[0];
                        const endDateStr = endDate.toISOString().split('T')[0];
                        document.querySelector("input[name='start_date']").setAttribute("value", startDateStr);
                        document.querySelector("input[name='end_date']").setAttribute("value", endDateStr);
                    }

                } else {
                    startDate = new Date(document.getElementById('start_date').value).getTime();
                    endDate = new Date(document.getElementById('end_date').value).getTime();
                }
                // Lọc dữ liệu theo khoảng thời gian này
                var filteredData = data.filter(function(row) {
                    var rowDate = new Date(row.created_at);
                    return rowDate >= startDate && rowDate <= endDate;
                });
                // Áp dụng bộ lọc khác nếu có
                if (filterValue === "time") {
                    filteredData.sort(function(a, b) {
                        return new Date(b.created_at) - new Date(a.created_at);
                    });
                } else if (filterValue === "seller") {
                    filteredData.sort(function(a, b) {
                        return a.seller_name.localeCompare(b.seller_name);
                    });
                } else if (filterValue === "product") {
                    filteredData.sort(function(a, b) {
                        return a.title.localeCompare(b.title);
                    });
                } else if (filterValue === "buyer") {
                    filteredData.sort(function(a, b) {
                        return a.buyer_name.localeCompare(b.buyer_name);
                    });
                } else if (filterValue === "price") {
                    filteredData.sort(function(a, b) {
                        return a.unit_price - b.unit_price;
                    });
                }

                renderTable(filteredData);
            }
            renderTable(data);
        });
    </script>
@endsection
