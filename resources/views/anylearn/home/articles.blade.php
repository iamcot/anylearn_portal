@if(!empty($data))
<section class="carousel3 mt-3">
    <div class="row mx-auto my-auto justify-content-center">
        <div id="{{ $carouselId }}" class="carousel slide" data-bs-interval="false">
            <a class="carousel-control-next bg-gradient" href="#{{ $carouselId }}" role="button" data-bs-slide="next">
                <span class="carousel-control-icon rounded-circle border"><i class="fas fa-2x fa-angle-right text-secondary"></i></span>
            </a>
            <div class="carousel-inner" role="listbox">
                <h5 class="m-2 fw-bold text-uppercase">{{ $title }}</h5>
                @foreach($data as $article)
                <div class="carousel-item  {{ $loop->index == 0 ? 'active' : '' }}">
                    <div class="col-md-4">
                        <div class="card border-0">
                            <div class="card-img">
                                <img src="{{ $article->image }}" class="img-fluid">
                                <h6 class="mt-1 fw-bold">{{ $article->title }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        var items = document.querySelectorAll('#{{ $carouselId }} .carousel-item')
        items.forEach((el) => {
            var minPerSlide = screen.width <= 768 ? 1 : 3;
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
@endif