<div class="collapse-module pb-4">
    <div class="collapse" id="reviewCollapse">

        <div class="d-flex">
            <div class="flex-grow-1">
                <span class="fs-1 fw-bold">{{ number_format($item->rating, 1, '.', ',') }}</span><span>/5</span>
            </div>
            <div class="pt-3 pe-3 text-end">
                @include('anylearn.widget.rating', ['score' => $item->rating ?? 0])
                <span class="small">{{ count($reviews) }} đánh giá</span>
            </div>

        </div>
        <div>
            <ul class="list-unstyled">
                @foreach($reviews as $review)
                <li class="d-flex @if($loop->index < count($reviews) - 1)  border-bottom @endif mb-3">
                    <div class="">
                        @if ($review->user_image)
                        <div>
                            <img src="{{ $review->user_image }}" alt="">
                        </div>
                        @endif
                    </div>
                    <div class="">
                        <p>{{ $review->user_name }}</p>
                        @include('anylearn.widget.rating', ['score' => $review->value ?? 0])
                        <p>{{ $review->extra_value }}</p>
                    </div>

                </li>
                @endforeach
            </ul>

        </div>
    </div>
    @if(count($reviews) > 2)
    <div class="text-center">
        <button class="ps-4 pe-4 border-0 btn btn-white rounded-pill shadow fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#reviewCollapse" aria-expanded="false" aria-controls="reviewCollapse">
    </div>
    @endif
</div>