@if(!empty($data) && count($data) > 0)
<section class="carousel3 mt-3">
    <div class="mx-auto my-auto justify-content-center">
        <div id="{{ $carouselId }}">
            <h5 class="m-2 fw-bold text-uppercase">{{ $title }}</h5>
            <div class="owl-carousel owl-theme" role="listbox">
                @foreach($data as $article)
                <div class="p-1">
                    <div class="card border-0">
                        <div class="card-img">
                            <img src="{{ $article->image }}" class="img-fluid">
                            <h6 class="mt-1 fw-bold">{{ $article->title }}</h6>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif