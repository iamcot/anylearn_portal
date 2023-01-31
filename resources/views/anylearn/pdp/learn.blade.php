@inject('videoServ', 'App\Services\VideoServices')
@inject('itemServ', 'App\Services\ItemServices')
@extends('anylearn.layout')

@section('body')
    <div class="row">
        <div class="mb-4 mb-lg-4 col-lg-7">
            <div class="position-relative h-sm-100 overflow-hidden">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $link }}"
                    title="YouTube video player" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen></iframe>
                <h6 class="fw-semi-bold text-400">Một khóa học đến từ <a class="link-info"
                        href="{{ route('classes', ['role' => 'school', 'id' => $videoServ->getTeacher($itemId)->id]) }}">{{ $videoServ->getTeacher($itemId)->name }}</a>
                </h6>
                <h2 class="fw-bold text-black">{{ $videoServ->getOneLessonItem($idvideo)->title }}</h2>
                <p class="text-black fw-semi-bold fs--1">
                    @include('pdp.rat', ['score' => 5])
                </p>
            </div>
        </div>
        @if ($videoServ->checkOrder($itemId) == 0)
            <div class="col-lg-5">
                <div class="mb-3 card">
                    <div class="bg-light d-none d-lg-block mb-0 card-header">
                        <h5 class="mb-0">
                            <font style="vertical-align: inherit;">
                                <font class="fw-bold" style="vertical-align: inherit;">Đăng kí khóa học ngay</font>
                            </font>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="order-md-1 order-lg-0 col-lg-12 col-md-7">
                                <h2 class="fw-medium d-flex align-items-center">
                                    <font style="vertical-align: inherit;"></font>
                                    <font style="vertical-align: inherit;">
                                        <font style="vertical-align: inherit;">{{ $videoServ->getOneItem($itemId)->price }}
                                        </font>
                                        <font style="vertical-align: inherit;">VND</font>
                                    </font> <del class="ms-2 fs--1 text-500">
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">VND </font>
                                        </font>
                                        <font style="vertical-align: inherit;">
                                            <font style="vertical-align: inherit;">
                                                {{ $videoServ->getOneItem($itemId)->org_price }}</font>
                                        </font>
                                    </del>
                                </h2>
                                <div class="flex-fill pt-2"><a @if (auth()->check()) id="add2cart-action" @endif
                                        class="border-0 btn btn-success form-control rounded-pill"
                                        href="{{ $itemServ->classUrl($itemId) }}">@lang('Đăng ký học')</a>
                                </div>

                            </div>
                            <div class="col-lg-12 col-md-5">
                                <hr class="border-top border-dashed d-md-none d-lg-block">
                                <h6 class="fw-bold">
                                    <font style="vertical-align: inherit;">
                                        <font style="vertical-align: inherit;">Nội dung khóa học</font>
                                    </font>
                                </h6>
                                <p>{{ substr($videoServ->getOneItem($itemId)->short_content, 0, 265) }}...</p>
                            </div>
                        </div>

                        {{-- <h6 class="fw-bold text-end">
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><a href="#"> Chia sẻ với bạn bè <i class="fas fa-share"></i></a></font>
                        </font>
                    </h6> --}}
                    </div>
                </div>
            </div>
        @else
            <div class="mb-lg-4 col-lg-5">
                <div class="accordion" id="accordionExample">
                    @foreach ($chapter as $chap)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $chap->chapter_no }}" aria-expanded="true"
                                    aria-controls="collapse{{ $chap->chapter_no }}">
                                    Chương {{ $chap->chapter_no }}
                                </button>
                            </h2>
                            <div id="collapse{{ $chap->chapter_no }}" class="accordion-collapse collapse "
                                aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="">
                                    <div class="tab-content">
                                        <table class="fs--1 text-end table" style="margin-bottom: 0em;">
                                            <tbody>
                                                @foreach ($videoServ->LessoninChapter($chap->chapter_no) as $les)
                                                @if ($chap->item_id == $les->item_id)
                                                    <form action="" method="get">
                                                        <tr class="btn-reveal-trigger bg-light">
                                                            <td class="align-middle white-space-nowrap text-start">
                                                                <div class="d-flex row">
                                                                    <div class="col-md-2">
                                                                        <h6 class="text-success fs--1 mt-1">Bài
                                                                            {{ $les->lesson_no }} &nbsp;
                                                                        </h6>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <h6 class="fs--2 text-black mt-1"
                                                                            style="vertical-align: inherit;">
                                                                            {{ $les->title }}</h6>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="hidden" name="idvideo"
                                                                            value="{{ $les->id }}">
                                                                        <button type="submit" name="action"
                                                                            value="learnfree"
                                                                            class="float-end btn btn-outline-primary btn-sm">Học</button>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </form>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div class="mb-3 card">
        <div class="card-header">
            <div class="align-items-center row">
                <div class="col">
                    <h5 class="mb-0">
                        <font style="vertical-align: inherit;">
                            <font class="fw-bold" style="vertical-align: inherit;">Được cung cấp bởi</font>
                        </font>
                    </h5>
                </div>

            </div>
        </div>
        <div class="bg-light card-body">
            <div class="g-4 text-center text-md-start row">
                {{-- <div class="col-md-auto">
                    <div class="avatar avatar-4xl "><img class="rounded-circle "
                            src="{{ $videoServ->getTeacher($itemId)->image }}" alt=""></div>
                </div> --}}
                <div class="col">
                    <h5 class="mb-2"><a
                            href="{{ route('classes', ['role' => 'school', 'id' => $videoServ->getTeacher($itemId)->id]) }}">
                            <font style="vertical-align: inherit;">
                                <font style="vertical-align: inherit;">{{ $videoServ->getTeacher($itemId)->name }}</font>
                            </font>
                        </a></h5>
                    <h6 class="fs--1 text-800 fw-normal mb-3">
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;">{{ $videoServ->getTeacher($itemId)->introduce }}</font>
                            {{-- <font style="vertical-align: inherit;">Nhà văn truyện tranh chuyên nghiệp</font> --}}
                        </font>
                    </h6>
                    <p class="fs--1 text-700">
                        {{ $videoServ->getTeacher($itemId)->full_content }}
                    </p>

                </div>
            </div>
        </div>
        <div class="text-end py-2 card-footer"><a role="button" tabindex="0"
                href="{{ route('classes', ['role' => 'school', 'id' => $videoServ->getTeacher($itemId)->id]) }}"
                class="fw-medium btn btn-link btn-sm">
                <font style="vertical-align: inherit;">
                    <a href="{{ route('classes', ['role' => 'school', 'id' => $videoServ->getTeacher($itemId)->id]) }}"
                        class="link-info">Xem tất cả các khóa
                        học</a>
                </font>
            </a></div>
    </div>
@endsection
