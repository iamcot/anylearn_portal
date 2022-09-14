<section class="mt-5 mb-5">
    <div class="container">
        <h1 class="text-black">
            Ban cố vấn và chuyên gia
            <a href="/ref/anylearn?role=teacher" class="btn border rounded rounded-pill border-primary text-primary fs-6 fw-bold ps-4 pe-4 float-end">ĐĂNG KÝ LÀM CHUYÊN GIA</a>
        </h1>
        <div class="owl-carousel owl-theme">
            @foreach($teachers as $teacher)
            <div class="">
                <div class="p-5 imagebox">
                    <img src="{{ $teacher->image }}" class="rounded-circle">
                </div>
                <p class="fw-bold">{{ $teacher->name }}</p>
                <p class="text-black-50">{{ $teacher->introduce }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>