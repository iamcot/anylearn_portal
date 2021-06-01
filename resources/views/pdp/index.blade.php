<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ $data['item']->title }}</title>
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
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <img style="width: 100%;" src="{{ $data['item']->image }}" />
                    </div>
                    <div class="col-lg-8 col-md-6">
                        <h2 class="text-blue">{{ $data['item']->title }}</h2>
                        <div>
                            @include('pdp.rating', ['score' => $data['item']->rating])
                        </div>
                        <p><i class="fa fa-calendar"></i> Khai giảng: {{ date('d/m/Y', strtotime($data['item']->date_start)) }} {{ $data['num_schedule'] <= 1 ? '' : '(có ' . $data['num_schedule'] . ' buổi học)' }}</p>
                        <p><i class="fa fa-{{ $data['author']->role == 'teacher' ? 'user' : 'university'}}"></i> {{ $data['author']->role == 'teacher' ? 'Giảng viên' : 'Trung tâm' }}: {{ $data['author']->name }}</p>
                        <h3 class="text-orange">{{ number_format($data['item']->price, 0, ',', '.') }}</h3>
                        <div><a id="add2cart-action" class="btn btn-success form-control" href="{{ auth()->check() ? '#' : route('login') }}">Đăng ký học</a></div>

                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-sm-12">
                        <div class="anylearn_content">
                            {!! $data['item']->content !!}
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <hr>
        @include('layout.footer')
    </section>

    @include('dialog.pdpadd2cart', ['class' => $data['item'], 'author' => $data['author'], 'num_schedule' => $data['num_schedule']])
    <script src="/cdn/vendor/jquery/jquery.min.js"></script>
    <script src="/cdn/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="/cdn/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="/cdn/js/sb-admin-2.min.js"></script>
    <script>
        $('.toast').toast('show')
    </script>
    @yield('jscript')
    <script>
        $('#add2cart-action').click(function() {
            $('#pdpAdd2CartModal').modal('show');
        });

        function offVoucher() {
            $("#add2cartvoucher").hide();
            $("input[name=voucher]").val("");
        }

        function onVoucher() {
            $("#add2cartvoucher").show();
        }
    </script>
</body>

</html>