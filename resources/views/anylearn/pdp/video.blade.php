@inject('videoServ', 'App\Services\VideoServices')
@foreach ($chapter as $chap)
    <div class="">
        <h5 class="card-header">
            <div class="g-0 justify-content-between">
                <div class="col-sm-auto">
                    <h6 class="text-success fs--1 mb-0">Chương {{ $chap->chapter_no }}</h6>
                    <h6 class="text-success fw-bold mb-0">{{ $chap->title }}</h6>
                </div>
            </div>
        </h5>
        <div class="d-flex align-items-center px-card border-200">
            <table class="fs--1 text-end mb-0 table">
                <tbody>
                    @foreach ($videoServ->LessoninChapter($chap->chapter_no) as $les)
                        @if ($chap->item_id == $les->item_id)
                            <tr class="btn-reveal-trigger bg-light">
                                <td class="align-middle white-space-nowrap text-start">
                                    <div class="d-flex align-items-center row">
                                        <div class="col-md-1">
                                            <h6 class="text-black text-center fs--1 mb-0">Bài {{ $les->lesson_no }}</h6>
                                        </div>
                                        <div class="col-md-9">
                                            <strong>{{ $les->title }}</strong>
                                            <p class="fs--1 mb-0" style="vertical-align: inherit;">
                                                {{ $les->description }}
                                            </p>
                                        </div>
                                        <div class="col-md-2">
                                            @if ($videoServ->checkOrder($item->id) == 0)
                                                <form action="" method="get">
                                                    {{ csrf_field() }}
                                                    @if ($les->is_free == 1)
                                                        <input type="hidden" name="idvideo"
                                                            value="{{ $les->id }}">
                                                        <button type="submit" name="action" value="learnfree"
                                                            class="float-end btn btn-outline-primary btn-sm">Học miễn
                                                            phí</button>
                                                    @else
                                                        <a href="#"><button type="button" name="action"
                                                                value="learnfree"
                                                                class="float-end btn btn-outline-primary btn-sm">Đăng kí
                                                                để
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

                                        </div>

                                    </div>

                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach
