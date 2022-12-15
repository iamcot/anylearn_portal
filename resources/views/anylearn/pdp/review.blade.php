<div class="collapse-module pb-4">
    <div class="collapse" id="reviewCollapse">

        <div class="d-flex">
            <div class="flex-grow-1">
                <span class="fs-1 fw-bold">{{ number_format($item->rating, 1, '.', ',') }}</span><span>/5</span>
            </div>
            <div class="pt-3 pe-3 text-end">
                @include('anylearn.widget.rating', ['score' => $item->rating ?? 0])
                <span class="small">{{ count($reviews) }} @lang('đánh giá')</span>
                <br>
                @if (Auth::check())
                    @if ((auth()->user()->role == 'school') | (auth()->user()->role == 'teacher'))
                        <a class="ratingFormClick" href="#"
                            data-class-id="{{ $item->id }}">@lang('Đánh giá khóa học')</a>
                    @endif
                @endif
            </div>

        </div>

        <div>

            <ul class="list-unstyled">
                @foreach ($reviews as $review)
                    <li class="row @if ($loop->index < count($reviews) - 1) border-bottom @endif mb-3">
                        <div class="col-sm-1 col-3 m-2">
                            @if ($review->user_image)
                                <img class="img-fluid border rounded-circle" src="{{ $review->user_image }}"
                                    alt="">
                            @endif
                        </div>
                        <div class="col-9">
                            <p>{{ $review->user_name }}</p>
                            @include('anylearn.widget.rating', ['score' => $review->value ?? 0])
                            <p>{{ $review->extra_value }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-3 row">
                <div class="mb-4 mb-lg-0 col-lg-6">
                    <div>
                        <div class="mb-1">
                            <span class="fs--1" style="display: inline-block; direction: ltr;">*****</span>
                            <span class="ms-3 text-dark fw-semi-bold"> Admin</span>
                        </div>
                        <p class="fs--1 mb-2 text-600"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (count($reviews) > 2)
        <div class="text-center">
            <button class="ps-4 pe-4 border-0 btn btn-white rounded-pill shadow fw-bold" type="button"
                data-bs-toggle="collapse" data-bs-target="#reviewCollapse" aria-expanded="false"
                aria-controls="reviewCollapse">
        </div>
    @endif
</div>

@include('dialog.rating')
@section('jscript')
    @parent
    <script>
        $('.ratingFormClick').click(function() {
            var classId = $(this).data("class-id");
            $("#rating_class_id").val(classId);
            $('#ratingFormModal').modal('show');
        });
    </script>
@endsection
