@inject('videoServ', 'App\Services\VideoServices')
@foreach ($chapter as $chap)
    <div class="mb-3 card">
        <h5 class="card-header mt-2">
            <div class="g-0 justify-content-between">
                <div class="col-sm-auto">
                    <h6 class="text-success fs--1 mb-0">Chương {{ $chap->chapter_no }}</h6>
                    <h5 class="text-success fw-bold mb-0">{{ $chap->title }}</h5>
                </div>
            </div>
        </h5>
        <div class="d-flex align-items-center px-card py-2 border-bottom border-200">
            <table class="fs--1 text-end mb-0 table table-borderless">
                <tbody>
                    @foreach ($videoServ->lessonItem($item->id) as $les)
                        @if ($chap->id == $les->item_video_chapter_id)
                            <tr class="btn-reveal-trigger bg-light">
                                <td class="align-middle white-space-nowrap text-start">
                                    <div class="d-flex align-items-center gap-3">
                                        <div>
                                            <h6 class="text-black fs--1 mb-0">Bài</h6>
                                            <h3 class="text-black fw-bold ml-1 mb-0">{{ $les->lesson_no }}</h3>
                                        </div>
                                        <div>
                                            <strong>{{ $les->title }}</strong>

                                            <p class="fs--1 text-900 mb-0" style="vertical-align: inherit;">
                                                {{ $les->description }}
                                            </p>
                                        </div>
                                    </div>
                                    @if ($videoServ->checkOrder($item->id) == 0)
                                        <form action="" method="get">
                                            {{ csrf_field() }}
                                            @if ($les->is_free == 1)
                                                <input type="hidden" name="idvideo" value="{{ $les->id }}">
                                                <button type="submit" name="action" value="learnfree"
                                                    class="float-end btn btn-outline-primary btn-sm">Học miễn
                                                    phí</button>
                                            @else
                                                <a href="#"><button type="button" name="action"
                                                        value="learnfree"
                                                        class="float-end btn btn-outline-primary btn-sm">Đăng kí để
                                                        học</button></a>
                                                {{-- <button type="button" class="float-end btn btn-outline-primary btn-sm"><i class="fas fa-fw fa-lock"></i></button> --}}
                                            @endif
                                        </form>
                                    @else
                                        <form action="" method="get">
                                            <input type="hidden" name="idvideo" value="{{ $les->id }}">
                                            <button type="submit" name="action" value="learnfree"
                                                class="float-end btn btn-outline-primary btn-sm">Học</button>
                                        </form>
                                    @endif

                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach
