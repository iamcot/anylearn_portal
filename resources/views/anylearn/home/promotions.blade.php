@if(!empty($promotions) && count($promotions) > 0)
<section class="carousel3">
    <div id="{{ $carouselId }}" class="mx-auto my-auto">
        <h5 class="m-2 fw-bold text-uppercase">{{ $title }}</h5>
        <div class="owl-carousel owl-theme">
            @foreach($promotions as $promotion)
            <div class="p-1">
                <div class="card border-0">
                    <div class="card-img">
                        <img src="{{ $promotion->image }}" class="img-fluid">
                        <div class="promotion-tag">{{ $promotion->video }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif