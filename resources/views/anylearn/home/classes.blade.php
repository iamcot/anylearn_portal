@inject('itemServ','App\Services\ItemServices')
<section class="carousel4">
    <div class="row mx-auto my-auto justify-content-center">
        <div id="{{ $carouselId }}" class="carousel slide" data-bs-interval="false">
            <a class="carousel-control-next bg-gradient" href="#{{ $carouselId }}" role="button" data-bs-slide="next">
                <span class="carousel-control-icon rounded-circle border"><i class="fas fa-2x fa-angle-right text-secondary"></i></span>
            </a>
            <div class="carousel-inner" role="listbox">
                <h5 class="m-2 fw-bold text-uppercase">{{ $title }}</h5>
                @foreach($data as $class)
                <div class="carousel-item  {{ $loop->index == 0 ? 'active' : '' }}">
                    <div class="col-xs-6 col-md-3  d-flex align-items-stretch">
                        <div class="card border-0 shadow-sm">
                            <div class="card-img">
                                <div class="imagebox">
                                <img src="{{ $class->image }}" class="img-fluid">
                                </div>
                                <div class="class-title mt-1 fw-bold p-1">@if($class->is_hot) <span class="badge bg-danger "><i class="fas fa-fire"></i> HOT</span> @endif {{ $class->title }}</div>
                                <div class="p-1">
                                    @if($class->org_price > 0)
                                    <span class="bg-success badge mr-1">-{{ number_format((($class->org_price - $class->price) / $class->org_price) * 100, 0,".",",") }}%</span>
                                    <span class="text-secondary text-decoration-line-through mr-1">{{ number_format($class->org_price, 0, ',', '.') }}</span>
                                    @endif
                                    <span class="text-success fw-bold">{{ number_format($class->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="p-1">@include('anylearn.widget.rating', ['score' => $class->rating ?? 0])</div>
                                <div class="text-center mb-2">
                                    <a href="{{ $itemServ->classUrl($class->id) }}" class="btn btn-success rounded-pill border-0 w-75">CHI TIẾT</a>
                                </div>
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
            var minPerSlide = screen.width <= 768 ? 2 : 5;
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