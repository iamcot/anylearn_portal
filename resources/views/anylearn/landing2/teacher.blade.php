<section class="mt-5 mb-5">
    <div class="container">
        <div id="carouselExampleInterval" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($teachers as $teacher)
                    <div class="carousel-item <?php if($teachers[0] == $teacher) echo'active' ?>" data-bs-interval="3000">
                        <div class="row">
                            <div class="col-sm-6">
                                <div style="position: relative;"
                                    class="d-none d-lg-block review-bcv-wrapper bg-success rounded p-2 text-white text-end">
                                    <div class="review-bcv mt-5">
                                        <img src="{{ $teacher->image }}" class="d-block img-cus imagebox bcv"
                                            alt="...">
                                            {{-- <div class="img-responsive-wrap">
                                                <a href="yourlink" class="img-inner" style="background-image: url('{{ $teacher->image }}');"><img alt="Image Alt" title="Your Title" src=""></a>
                                            </div> --}}
                                        <div class="vector">
                                            <img src="./cdn/anylearn/img/Vector.png" class="d-block img-fluid"
                                                alt="...">
                                        </div>

                                    </div>

                                    <div class="fw-bold text-start p-5 text-title"> Ban cố vấn & <br> chuyên gia</div>
                                    <div class="content-bcv text-start ">
                                        <p class="text-white">
                                        <h5>{{ $teacher->name }}</h5>{{ substr($teacher->introduce,0,120) }}</p>
                                        <a href="/ref/anylearn?role=teacher"
                                            class="btn border rounded rounded-pill border-white btn btn-outline-success fs-6 fw-bold ps-4 pe-4 float-end mb-5 text-white">ĐĂNG
                                            KÝ LÀM CHUYÊN GIA <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
