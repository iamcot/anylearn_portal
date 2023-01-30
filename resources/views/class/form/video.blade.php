@inject('lesson', 'App\Services\VideoServices')
@if (isset($chapter))
    @foreach ($chapter as $chap)
        <div class="mb-3 card">
            <h5 class="card-header">
                <div class="g-0 justify-content-between row">
                    <div class="col-sm-auto">
                        <h6 class="text-success fs--1 mb-0">Chương {{ $chap->chapter_no }}</h6>
                        <h5 class="text-success fw-bold mb-0">{{ $chap->title }}</h5>
                    </div>
                    <div class="mb-3 mb-md-0 col-md-auto">
                        <div class="g-3 gy-md-0 h-100 align-items-center row">
                            <button type="button" onclick="addid({{ $chap->chapter_no }})" class="btn btn-success btn-sm"
                                data-bs-toggle="modal" data-bs-target="#exampleModal2">Thêm
                                bài mới</button>
                        </div>
                    </div>
                </div>
            </h5>
            <div class="p-0 card-body">
                <div class="table-responsive scrollbar">
                    <table class="fs--1 text-end mb-0 table table-borderless">
                        <tbody>
                            <tr class="btn-reveal-trigger bg-light">
                                <td class="align-middle white-space-nowrap text-start">
                                    <div class="mb-3 card">
                                        <div class="p-0 card-body">
                                            <div data-simplebar="init">
                                                <div class="simplebar-wrapper" style="margin: 0px;">
                                                    <div class="simplebar-height-auto-observer-wrapper">
                                                        <div class="simplebar-height-auto-observer"></div>
                                                    </div>
                                                    <div class="simplebar-mask">
                                                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                                            <div class="simplebar-content-wrapper" tabindex="0"
                                                                role="region" aria-label="scrollable content"
                                                                style="height: auto;">
                                                                <div class="simplebar-content" style="padding: 0px;">
                                                                    <table role="table"
                                                                        class="fs--1 mb-0 overflow-hidden table table-sm table-striped">
                                                                        <tbody>
                                                                            @foreach ($lesson->LessoninChapter($chap->chapter_no) as $les)
                                                                            @if ($chap->item_id == $les->item_id)
                                                                                <tr class="align-middle white-space-nowrap"
                                                                                    role="row">
                                                                                    <td role="cell"><a><strong>Bài
                                                                                                {{ $les->lesson_no }}:</strong></a>
                                                                                        <strong>{{ $les->title }}</strong>

                                                                                        <p class="fs--1 text-900 mb-0"
                                                                                            style="vertical-align: inherit;">
                                                                                            {{ $les->description }}
                                                                                        </p>
                                                                                    </td>
                                                                                </tr>
                                                                                @endif
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    @if ($lesson->LessoninChapter($chap->chapter_no) == '[]')
                                        <div class="d-flex align-items-center position-relative gap-3">
                                            <div>
                                                <h6 class="fs--2 text-black" style="vertical-align: inherit;">
                                                    Bạn chưa có bài nào cho chương này</h6>
                                                <p class="fs--1 text-900 mb-0" style="vertical-align: inherit;">
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    @if ($chapter == '[]')
        <div class="mb-1 card">
            <h5 class="card-header">
                <div class="g-0 justify-content-between row">
                    <div class="col-sm-auto">
                        <h4 class="text-primary fw-bold mb-0"> Bạn chưa có bất kì chương học video nào</h4>
                    </div>
                </div>
            </h5>
            <div class="text-center py-2 card-footer">
                <a class="btn btn-link btn-sm" style="vertical-align: inherit; text-decoration:none">
                    <h6 class="fs-0 text-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">+Thêm Chương
                        Mới
                    </h6>
                </a>
            </div>
        </div>
    @else
        <div class="text-center py-2 card-footer">
            <a class="btn btn-link btn-sm" style="vertical-align: inherit; text-decoration:none">
                <h6 class="fs-0 text-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">+Thêm Chương Mới
                </h6>
            </a>
        </div>
    @endif
@endif
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title h4">
                        <h5 class="text-primary fw-bold mb-0">Thêm chương mới</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="mb-3 form-floating">
                            <input type="number" id="floatingInput" name="chapterno" class="form-control" min=0>
                            <label for="floatingInput">Chương số</label>
                        </div>
                        <div class="mb-3 form-floating">
                            <input type="text" id="floatingInput2" name="chaptitle" class="form-control">
                            <label for="floatingInput2">Tiêu đề</label>
                        </div>
                        <div class="form-floating">
                            <textarea id="floatingTextarea2" name="chapdes" class="form-control" style="height: 100px;"></textarea><label for="floatingTextarea2">Giới Thiệu ngắn</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="mb-1 btn btn-success" name="action" value="createChapter">Thêm
                        mới</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2-->
<div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title h4">
                    <h5 class="text-primary fw-bold mb-0">Thêm bài mới</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <input type="hidden" class="form-control" id="idchaplklesson" name="idchaplklesson">
                    <div class="mb-3 form-floating">
                        <div>
                            <select aria-label="Default select example" class="form-select" name="typelesson"
                                id="select" onchange="getval(this)">
                                <option><label for="floatingInput">Loại bài học</label></option>
                                {{-- <option value="file">File</option> --}}
                                <option value="youtube">Youtube</option>
                                {{-- <option value="stream">Stream Video</option> --}}
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 form-floating d-none" id="ytb">
                        <input type="text" class="form-control" min=0 name="youtube">
                        <label for="floatingInput">Youtube link</label>
                    </div>
                    <div class="mb-3 form-floating d-none" id="file">
                        <input type="file" class="form-control" name="file" min=0>
                        <label for="floatingInput">File</label>
                    </div>
                    <div class="mb-3 form-floating d-none" id="stream">
                        <input type="text" class="form-control" min=0 name="stream">
                        <label for="floatingInput">Stream Link</label>
                    </div>
                    {{-- <div class="mb-3 form-floating">
                        <input type="text" class="form-control">
                        <label for="floatingInput2">Đường dẫn bài học</label>
                    </div> --}}
                    <div class="mb-3 form-floating">
                        <input type="number" class="form-control" name="lessonno">
                        <label for="floatingInput2">Bài học số</label>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" name="lessonname">
                        <label for="floatingInput2">Tên bài</label>
                    </div>
                    <div class="mb-3 form-floating">
                        <textarea name="lessondes" class="form-control" style="height: 100px;"></textarea><label for="floatingTextarea2">Giới Thiệu</label>
                    </div>
                    <div>
                        <select aria-label="Default select example" class="form-select" name="is_free">
                            {{-- <option><label for="floatingInput">Miễn Phí/Thu Phí</label></option> --}}
                            <option value="1">Miễn Phí</option>
                            <option value="2">Thu Phí</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="action" value="createLesson" class="mb-1 btn btn-success">Thêm
                    mới</button>
            </div>
        </div>
    </div>
</div>
<script src="/cdn/anylearn/bootstrap-5.1.1/js/bootstrap.bundle.min.js"></script>
<script src="/cdn/anylearn/jquery-3.6.0.min.js"></script>
<script src="/cdn/anylearn/owl.carousel.min.js"></script>
<script async src="/cdn/js/anylog.js"></script>
<script>
    function getval(sel) {
        if (sel.value == "file") {
            document.getElementById("file").classList.remove("d-none");
            document.getElementById("ytb").classList.add("d-none");
            document.getElementById("stream").classList.add("d-none");
        } else if (sel.value == "youtube") {
            document.getElementById("ytb").classList.remove("d-none");
            document.getElementById("stream").classList.add("d-none");
            document.getElementById("file").classList.add("d-none");

        } else if (sel.value == "stream") {
            document.getElementById("stream").classList.remove("d-none");
            document.getElementById("file").classList.add("d-none");
            document.getElementById("ytb").classList.add("d-none");
        } else {
            document.getElementById("file").classList.add("d-none");
            document.getElementById("ytb").classList.add("d-none");
            document.getElementById("stream").classList.add("d-none");
        }
    }

    function addid(id) {
        document.getElementById("idchaplklesson").value = id;
    }
</script>
