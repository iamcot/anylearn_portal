@inject('videoServ', 'App\Services\VideoServices')
<div class="d-flex align-items-center px-card py-2 border-bottom border-200">
    <table class="fs--1 text-end mb-0 table table-borderless">
        <tbody>
            <form action="" method="POST">
            @foreach ($videoServ->lessonItem($item->id) as $les)
            {{ csrf_field() }}
                <tr class="btn-reveal-trigger bg-light">
                    <td class="align-middle white-space-nowrap text-start">
                        <div class="d-flex align-items-center position-relative gap-3">
                            <div>
                                <h6 class="text-success fs--1 mb-0">Bài</h6>
                                <h3 class="text-success fw-bold ml-1 mb-0">{{ $les->lesson_no }}</h3>
                            </div>
                            <div>
                                <h6 class="fs--2 text-black" style="vertical-align: inherit;">
                                    {{ $les->title }}</h6>
                                <p class="fs--1 text-900 mb-0" style="vertical-align: inherit;">
                                    {{ $les->description }}
                                </p>
                            </div>
                        </div>
                        @if ($les->is_free ==1)
                            <input type="hidden" name="id" value="{{ $les->id}}">
                            <button type="submit" name="action" value="learnfree" class="float-end btn btn-outline-primary btn-sm">Học miễn phí</button>
                        @else
                        <a href="/learn"><button type="button" name="action" value="learnfree" class="float-end btn btn-outline-primary btn-sm">Đăng kí để học</button></a>
                        {{-- <button type="button" class="float-end btn btn-outline-primary btn-sm"><i class="fas fa-fw fa-lock"></i></button> --}}
                        @endif
                        <hr style="height:1px;border:none;color:#333;background-color:#333;">
                    </td>
                </tr>
            @endforeach
        </form>

        </tbody>
    </table>
</div>
