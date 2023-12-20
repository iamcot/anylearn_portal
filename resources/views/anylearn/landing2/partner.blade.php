<section class="mt-5">
    <div class="container">
        <h1 class="text-black">
            @lang('Đối tác của anyLEARN')
        </h1>
        <div class="text-center">
            <ul class="school-logo">
                @foreach(config('home_schools', []) as $school)
                <li class="col-xs-4 col-sm-3 col-md-2 col-lg-1">
                    <img src="{{ $school['image'] }}" alt="">
                </li>
                @endforeach
            </ul>
            <a href="/ref/anylearn?r=school" id="partner" 
                class="btn border rounded rounded-pill border-primary text-primary fs-6 fw-bold ps-4 pe-4">
                @lang('ĐĂNG KÝ ĐỐI TÁC')
            </a>
        </div>
    </div>
</section>
<section>
    <script>
        if (screen.width < 900) {
            document.getElementById("partner").classList.remove("float-end");
        } else{
            document.getElementById("partner").classList.add("float-end");
        }
    </script>
</section>
