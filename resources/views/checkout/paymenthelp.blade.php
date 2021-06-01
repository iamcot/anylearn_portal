<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Hướng dẫn thanh toán trên anyLEARN</title>
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <!-- Custom fonts for this template-->
    <link href="/cdn/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="/cdn/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/cdn/css/style.css?v{{ env('CDN_VERSION', '1.0.0') }}" rel="stylesheet">
    @yield('morestyle')
</head>

<body data-spy="scroll" data-target=".navbar" data-offset="50" id="body" class="only_portfolio_variation">
    @include('layout.fixed_menu')
    <!-- Parent Section -->
    <section class="page_content_parent_section">
        <!-- Header Section -->
        <header>
            <!-- Navbar Section -->
            @include('../layout.page_nav')
            <!-- /Navbar Section -->
        </header>
        <!-- /Header Section -->
        <section>
            @include('notify', ['notify' => session('notify', '')])
            <div class="container mt-5">
                <h3>Để hoàn thất thanh toán, quý khách vui lòng chuyển khoản theo thông tin sau.</h3>
                <dl class="row">
                    <dt class="col-sm-3">Ngân hàng</dt>
                    <dd class="col-sm-9">{{ $bank['bank_name'] }}</dd>

                    <dt class="col-sm-3">Chi nhánh</dt>
                    <dd class="col-sm-9">
                        {{ $bank['bank_branch'] }}
                    </dd>
                    <dt class="col-sm-3">Số tài khoản</dt>
                    <dd class="col-sm-9">{{ $bank['bank_no'] }}</dd>
                    <dt class="col-sm-3">Người thụ hưởng</dt>
                    <dd class="col-sm-9">{{ $bank['account_name'] }}</dd>
                    <dt class="col-sm-3">Nội dung tin chuyển tiền</dt>
                    <dd class="col-sm-9">{{ $bank['content'] }}</dd>
                </dl>
            </div>
        </section>
        <hr>
        @include('layout.footer')
    </section>


    <script src="/cdn/vendor/jquery/jquery.min.js"></script>
    <script src="/cdn/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/cdn/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="/cdn/js/sb-admin-2.min.js"></script>
    <script>
        $('.toast').toast('show')
    </script>
</body>

</html>