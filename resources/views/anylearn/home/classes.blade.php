<section class="carousel4">
    <div class="row mx-auto my-auto justify-content-center">
        <div id="{{ $carouselId }}" class="carousel slide" data-bs-interval="false">
            <a class="carousel-control-next bg-gradient" href="#{{ $carouselId }}" role="button" data-bs-slide="next">
                <span class="carousel-control-icon rounded-circle border"><i class="fas fa-2x fa-angle-right text-secondary"></i></span>
            </a>
            <div class="carousel-inner" role="listbox">
                <h5 class="m-2 fw-bold text-uppercase">{{ $title }}</h5>
                <div class="carousel-item active">
                    <div class="col-md-3  d-flex align-items-stretch">
                        <div class="card border-0 shadow-sm">
                            <div class="card-img ">
                                <img src="/cdn/anylearn/img/class.png" class="img-fluid">
                                <div class="mt-1 fw-bold p-1"><span class="badge bg-danger "><i class="fas fa-fire"></i> HOT</span> Hội thảo nghề làm cha mẹ</div>
                                <div class="p-1"><span class="bg-success badge mr-1">-25%</span>
                                <span class="text-secondary text-decoration-line-through mr-1">{{ number_format(200000, 0, ',', '.') }}</span>
                                <span class="text-success fw-bold">{{ number_format(150000, 0, ',', '.') }}</span></div>
                                <div class="p-1">@include('anylearn.widget.rating', ['score' =>3.5])</div>
                                <div class="text-center mb-2">
                                    <a href="" class="btn btn-success rounded-pill border-0 w-75">CHI TIẾT</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="col-md-3 d-flex align-items-stretch">
                        <div class="card border-0  shadow-sm">
                            <div class="card-img">
                                <img src="/cdn/anylearn/img/class.png" class="img-fluid">
                                <h6 class="mt-1 fw-bold">2Hội thảo nghề làm cha mẹ</h6>
                                <div><span class="bg-success badge mr-1">-25%</span>
                                <span class="text-secondary text-decoration-line-through mr-1">{{ number_format(200000, 0, ',', '.') }}</span>
                                <span class="text-success fw-bold">{{ number_format(150000, 0, ',', '.') }}</span></div>
                                <div>@include('anylearn.widget.rating', ['score' => 4])</div>
                                <div class="text-center mb-2 class-button w-100">
                                    <a href="" class="btn btn-success rounded-pill border-0 w-75">CHI TIẾT</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="col-md-3">
                        <div class="card border-0 d-flex align-items-stretch">
                            <div class="card-img">
                                <img src="/cdn/anylearn/img/class.png" class="img-fluid">
                                <h6 class="mt-1 fw-bold">3Hội thảo nghề làm cha mẹ</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="col-md-3">
                        <div class="card border-0">
                            <div class="card-img">
                                <img src="/cdn/anylearn/img/class.png" class="img-fluid">
                            </div>
                            <h6 class="mt-1 fw-bold">4Hội thảo nghề làm cha mẹ</h6>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="col-md-3">
                        <div class="card border-0">
                            <div class="card-img">
                                <img src="/cdn/anylearn/img/class.png" class="img-fluid">
                            </div>
                            <h6 class="mt-1 fw-bold">5Hội thảo nghề làm cha mẹ</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var items = document.querySelectorAll('#{{ $carouselId }} .carousel-item')
        console.log(items);
        items.forEach((el) => {
            const minPerSlide = 5;
            let next = el.nextElementSibling
            for (var i = 1; i < minPerSlide; i++) {
                if (!next) {
                    next = items[0]
                }
                let cloneChild = next.cloneNode(true)
                el.appendChild(cloneChild.children[0])
                next = next.nextElementSibling
            }
        })
    </script>
</section>