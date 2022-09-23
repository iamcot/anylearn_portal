<section class="mt-5 mb-5">
    <div class="container">
        <div id="carouselExampleInterval" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($teachers as $teacher)
                    <div class="carousel-item <?php if($teachers[0] == $teacher) echo'active' ?>" data-bs-interval="3000">
                        <div class="row">
                            
                            <div class="col-sm-7">
                                <div style="position: relative;"
                                    class="review-bcv-wrapper bg-success rounded p-2 text-white text-end">
                                    <div class="review-bcv mt-5">
                                        <img src="{{ $teacher->image }}" class="d-none d-lg-block img-cus imagebox bcv"
                                            alt="...">
                                            {{-- <div class="img-responsive-wrap">
                                                <a href="yourlink" class="img-inner" style="background-image: url('{{ $teacher->image }}');"><img alt="Image Alt" title="Your Title" src=""></a>
                                            </div> --}}
                                        <div class="vector">
                                            <img src="./cdn/anylearn/img/Vector.png" class="d-none d-lg-block img-fluid"
                                                alt="...">
                                        </div>
                                    </div>
                                    <div class="fw-bold fs-1 text-start p-5 pb-3 text-title mb-0"> Ban cố vấn & <br> chuyên gia</div>
                                    
                                    <div class="content-bcv text-start">
                                        {{-- <img src="{{ $teacher->image }}" class="d-block d-lg-none img-cus2 bcv mt-0"> --}}
                                        <p class="text-white mr-2 card-body">
                                        <h5>{{ $teacher->name }}</h5>{{ substr($teacher->introduce,0,222) }}</p>    
                                    </div>
                                    <a href="/ref/anylearn?r=teacher" style="position: absolute; left:50px; bottom:15px"
                                            class="border rounded rounded-pill border-white btn btn-outline-success fw-bold text-white">ĐĂNG
                                            KÝ LÀM CHUYÊN GIA <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
