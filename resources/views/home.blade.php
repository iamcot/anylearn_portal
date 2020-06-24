<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Tags For Seo + Page Optimization -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="anyLEARN.vn - HỌC không giới hạn, HỌC cho chính bạn">
    <meta name="author" content="">
    <!-- Insert Favicon Here -->
    <link href="/favicon-16x16.png" rel="icon">
    <!-- Page Title(Name)-->
    <title>anyLEARN - HỌC không giới hạn</title>

    <!-- Bootstrap CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/bootstrap.min.css">
    <!-- Font-Awesome CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/font-awesome.css">
    <!-- Slider Revolution CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/settings.css">
    <!--  Fancy Box CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/jquery.fancybox.css">
    <!-- Circleful CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/jquery.circliful.css">
    <!-- Animate CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/animate.css">
    <!-- Cube Portfolio CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/cubeportfolio.min.css">
    <!-- Owl Carousel CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/owl.carousel.min.css">
    <link rel="stylesheet" href="/cdn/onepage/css/owl.theme.default.min.css">
    <!-- Swiper CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/swiper.min.css">
    <!-- Custom Style CSS File -->
    <link rel="stylesheet" href="/cdn/onepage/css/style.css?v{{ env('CDN_VERSION', '1.0.0') }}">
    <!-- Color StyleSheet CSS File -->
    <link href="/cdn/onepage/css/pink.css" rel="stylesheet" id="color" type="text/css">
</head>
<body data-spy="scroll" data-target=".navbar" data-offset="50" id="body" class="only_portfolio_variation">
    <!-- Loader -->
    <!-- Loader -->
    <div class="loader">
        <div class="loader-inner">
            <div class="spinner">
                <div class="dot1"></div>
                <div class="dot2"></div>
            </div>
        </div>
    </div>
    <!-- Parent Section -->
    <section class="page_content_parent_section">
        <!-- Header Section -->
        <header>
            <!-- Navbar Section -->
            @include('home.navbar')
            <!-- /Navbar Section -->
            <!-- Main Slider Section -->
            @include('home.slider')
            <!-- /Main Slider Section -->
        </header>
        <!-- /Header Section -->
        @include('home.about')

        <!-- Portfolio Section -->
        @include('home.vision')
        <!-- /Portfolio Section -->

       @include('home.teacher')

        <!-- Blog Section -->
       @include('home.school')
        <!-- /Blog Section -->

        <!-- Pricing Table -->
        @include('home.download')
        <!-- /Pricing Table -->

        <!-- Customer Review Slider -->
        @include('home.feedback')
        <!-- /Customer Review Slider -->

        <!-- Google Map Section -->
        <!-- <section id="map"></section> -->
        <!-- /Google Map Section -->

        <!-- Contact Form Section -->
        @include('home.contact')
        <!-- /Contact Form  Section -->

        <!-- Footer Section -->
        <footer class="footer_section big_padding bg_navy">
            <div class="container">
                <div class="footer_detail">
                    
                    <p class="text-center default_text open_sans white_color">&copy; 2020 anyLEARN.vn, HỌC không giới hạn. </p>
                </div>
            </div>
        </footer>
        <!-- /Footer Section -->
    </section>

    <!-- /Parent Section Ended -->

    <!-- jQuery 2.2.0-->
    <script src="/cdn/onepage/js/jquery.js"></script>

    <!-- Google Map Api -->
    <!-- <script src="http://maps.google.com/maps/api/js?key=AIzaSyAOBKD6V47-g_3opmidcmFapb3kSNAR70U" type="text/javascript"></script> -->
    <!-- <script src="/cdn/onepage/js/map.js" type="text/javascript"></script> -->

    <!-- REVOLUTION JS FILES -->
    <script type="text/javascript" src="/cdn/onepage/js/jquery.themepunch.tools.min.js"></script>
    <script type="text/javascript" src="/cdn/onepage/js/jquery.themepunch.revolution.min.js"></script>

    <!-- Addon Revolution -->
    <script type="text/javascript" src="/cdn/onepage/js/revolution.addon.slicey.min.js"></script>

    <!-- SLIDER REVOLUTION 5.0 EXTENSIONS  (Load Extensions only on Local File Systems !  The following part can be removed on Server for On Demand Loading) -->
    <script type="text/javascript" src="/cdn/onepage/js/extensions/revolution.extension.actions.min.js"></script>
    <script type="text/javascript" src="/cdn/onepage/js/extensions/revolution.extension.carousel.min.js"></script>
    <script type="text/javascript" src="/cdn/onepage/js/extensions/revolution.extension.kenburn.min.js"></script>
    <script type="text/javascript" src="/cdn/onepage/js/extensions/revolution.extension.layeranimation.min.js"></script>
    <script type="text/javascript" src="/cdn/onepage/js/extensions/revolution.extension.migration.min.js"></script>
    <script type="text/javascript" src="/cdn/onepage/js/extensions/revolution.extension.navigation.min.js"></script>
    <script type="text/javascript" src="/cdn/onepage/js/extensions/revolution.extension.parallax.min.js"></script>
    <script type="text/javascript" src="/cdn/onepage/js/extensions/revolution.extension.slideanims.min.js"></script>
    <script type="text/javascript" src="/cdn/onepage/js/extensions/revolution.extension.video.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/cdn/onepage/js/bootstrap.min.js"></script>

    <!-- Owl Carousel 2 Core JavaScript -->
    <script src="/cdn/onepage/js/owl.carousel.js"></script>
    <script src="/cdn/onepage/js/owl.animate.js"></script>
    <script src="/cdn/onepage/js/owl.autoheight.js"></script>
    <script src="/cdn/onepage/js/owl.autoplay.js"></script>
    <script src="/cdn/onepage/js/owl.autorefresh.js"></script>
    <script src="/cdn/onepage/js/owl.hash.js"></script>
    <script src="/cdn/onepage/js/owl.lazyload.js"></script>
    <script src="/cdn/onepage/js/owl.navigation.js"></script>
    <script src="/cdn/onepage/js/owl.support.js"></script>
    <script src="/cdn/onepage/js/owl.video.js"></script>

    <!-- Fancy Box Javacript -->
    <script src="/cdn/onepage/js/jquery.fancybox.js"></script>
    <!-- Wow Js -->
    <script src="/cdn/onepage/js/wow.min.js"></script>
    <!-- Appear Js-->
    <script src="/cdn/onepage/js/jquery.appear.js"></script>
    <!-- Countdown Js -->
    <script src="/cdn/onepage/js/jquery.countdown.js"></script>
    <!-- Parallax Js -->
    <script src="/cdn/onepage/js/parallax.min.js"></script>
    <!-- Cube Portfolio Core JavaScript -->
    <script src="/cdn/onepage/js/jquery.cubeportfolio.min.js"></script>
    <!-- Circliful Core JavaScript -->
    <script src="/cdn/onepage/js/jquery.circliful.min.js"></script>
    <!-- Swiper Slider Core JavaScript -->
    <script src="/cdn/onepage/js/swiper.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="/cdn/onepage/js/script.js"></script>

</body>

</html>