<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.onepage_header')
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
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <img style="width: 100%;" src="{{ $data['item']->image }}" />
                    </div>
                    <div class="col-lg-8 col-md-6">
                        <h2 class="text-blue">{{ $data['item']->title }}</h2>
                        <div>
                            @include('pdp.rating', ['score' => $data['item']->rating])
                        </div>
                        <div><i class="fa fa-calendar"></i> Khai giảng: {{ date('d/m/Y', strtotime($data['item']->date_start)) }} {{ $data['num_schedule'] <= 1 ? '' : '(có ' . $data['num_schedule'] . ' buổi học)' }}</div>
                        <div><i class="fa fa-{{ $data['author']->role == 'teacher' ? 'user' : 'university'}}"></i> {{ $data['author']->role == 'teacher' ? 'Giảng viên' : 'Trung tâm' }}: {{ $data['author']->name }}</div>
                        <h3 class="text-orange">{{ number_format($data['item']->price, 0, ',', '.') }}</h3>
                        <div><button class="btn btn-success form-control">Đăng ký học</button></div>
                        <div class="anylearn_content">
                            {!! $data['item']->content !!}
                        </div>
                    </div>
                </div>

            </div>
        </section>

        @include('layout.footer')
    </section>
    @include('layout.onepage_script')

</body>

</html>