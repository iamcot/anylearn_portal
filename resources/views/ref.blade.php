<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags For Seo + Page Optimization -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="anyLEARN.vn - Nền tảng kết nối hàng đầu về giáo dục">
    <meta name="author" content="">
    <!-- Insert Favicon Here -->
    <link href="/favicon-16x16.png" rel="icon">
    <!-- Page Title(Name)-->
    <title>anyLEARN - HỌC không giới hạn</title>
    <link href="/cdn/css/sb-admin-2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="/cdn/css/style.css">
</head>

<body data-spy="scroll" data-target=".navbar" data-offset="50" id="body" class="only_portfolio_variation">

    <section class="pricing_section big_padding bg_grey" id="pricing_table">
        <div class="container">
            <div class="pricing_table_section">
                <h2 class="default_section_heading text-center ">
                    Đăng ký và tải ngay ứng dụng <span class="text-success">any</span><span class="text-primary">LEARN</span>
                </h2>
                <hr class="default_divider default_divider_blue default_divider_big">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-12">
                                <div id="en" class="tripi__wrapper" style="display: block;">
                                    @if($user)
                                    <div class="tripi__header">
                                        <p class="tripi__title">
                                            <b class="username">{{ $user->name }}</b> @lang('mời bạn tham gia cộng đồng <b>anyLEARN</b>').
                                        </p>
                                        <p class="tripi__title">@lang('Hoàn thành 3 bước sau để trở thành bạn bè của') <b class="username">{{ $user->name }}</b>
                                        </p>
                                    </div>
                                    <div class="tripi__stepper">
                                        <div class="tripi__ele-stepper">
                                            <div class="tripi__ele tripi__ele-cirle {{ isset($isReg) ? 'green': ''}}">1</div>
                                            <div class="tripi__ele tripi__ele-title">@lang('Kiểm tra đã có mã <b>:refcode </b> từ thành viên :username', ['refcode' => $user->refcode, 'username' => $user->name])</div>
                                        </div>
                                        <div class="tripi__ele-stepper">
                                            <div class="tripi__ele tripi__ele-cirle {{ isset($isReg) ? 'green': ''}}">2</div>
                                            <div class="tripi__ele tripi__ele-title">Điền thông tin của bạn và đăng ký tài khoản</div>
                                        </div>
                                        <div class="tripi__ele-stepper">
                                            <div class="tripi__ele tripi__ele-cirle">3</div>
                                            <div class="tripi__ele tripi__ele-title">Tải app anyLEARN về máy, đăng nhập lại bằng SĐT và mật khẩu của bạn
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        @if(!isset($isReg))
                        @include('auth.register_ref')
                        @else
                        <div class="row">
                            <p>Bạn vừa hoàn thành đăng ký tài khoản trên anyLEARN, hãy tải ứng dụng về máy và bắt đầu trải nghiệm!</p>
                            <div class="col-md-6" style="padding: 30px;">
                                <a href="https://apps.apple.com/vn/app/anylearn/id1518155412">
                                    <img src="/cdn/onepage/images/ios.png" style="width:100%" alt="">
                                </a>
                            </div>
                            <div class="col-md-6" style="padding: 30px;">
                                <a href="https://play.google.com/store/apps/details?id=vn.anylearn">
                                    <img src="/cdn/onepage/images/android.png" style="width:100%" alt="">
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="/cdn/vendor/jquery/jquery.min.js"></script>
    <script src="/cdn/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/cdn/vendor/jquery-easing/jquery.easing.min.js"></script>
    @yield('jscript')
</body>

</html>