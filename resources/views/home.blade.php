<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.onepage_header')
</head>

<body data-spy="scroll" data-target=".navbar" data-offset="50" id="body" class="only_portfolio_variation">
    @include('layout.fixed_menu')
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

        @include('home.school')

        @include('home.teacher')

        @include('home.course')

        @include('home.download')

        @include('home.feedback')

        @include('home.contact')

        @include('layout.footer')
    </section>
    @include('layout.onepage_script')

</body>

</html>