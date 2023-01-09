<div class="mb-3 card">
    <h5 class="card-header">
        <div class="g-0 justify-content-between row">
            <div class="col-sm-auto">
                <h6 class="text-primary fs--1 mb-0">Chương 1</h6>
                <h4 class="text-primary fw-bold mb-0">Học Không Giới Hạn</h4>
            </div>
            <div class="mb-3 mb-md-0 col-md-auto">
                <div class="g-3 gy-md-0 h-100 align-items-center row">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal2">Thêm
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
                            <div class="d-flex align-items-center position-relative gap-3">
                                <div>
                                    <h6 class="text-primary fs--1 mb-0">Bài</h6>
                                    <h3 class="text-primary fw-bold ml-1 mb-0">1</h3>
                                </div>
                                <div>
                                    <h6 class="fs--2 text-primary" style="vertical-align: inherit;">Giới thiệu</h6>
                                    <p class="fs--1 text-900 mb-0" style="vertical-align: inherit;">Đây là thông tin
                                        giới thiệu về anylearn tập đoàn giáo dục thương mại điện tử hàng đầu việt nam
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center py-2 card-footer">
        <a class="btn btn-link btn-sm" style="vertical-align: inherit; text-decoration:none">
            <h6 class="fs-0 text-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">+Thêm Chương Mới</h6>
        </a>
    </div>
</div>

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
                    <button type="submit" class="mb-1 btn btn-outline-success" name="action" value="createChapter">Thêm mới</button>
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

                    <div class="mb-3 form-floating">
                        <div>
                            <select aria-label="Default select example" class="form-select" id="select" onclick="select()">
                                <option><label for="floatingInput">Loại bài học</label></option>
                                <option value="file">File</option>
                                <option value="youtube">Youtube</option>
                                <option value="stream">Stream Video</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control" min=0>
                        <label for="floatingInput">Youtube thời gian bắt đầu</label>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control">
                        <label for="floatingInput2">Đường dẫn bài học</label>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="number" class="form-control">
                        <label for="floatingInput2">Bài học số</label>
                    </div>
                    <div class="mb-3 form-floating">
                        <input type="text" class="form-control">
                        <label for="floatingInput2">Tên bài</label>
                    </div>
                    <div class="mb-3 form-floating">
                        <textarea class="form-control" style="height: 100px;"></textarea><label for="floatingTextarea2">Giới Thiệu</label>
                    </div>
                    <div>
                        <select aria-label="Default select example" class="form-select">
                            <option><label for="floatingInput">Miễn Phí/Thu Phí</label></option>
                            <option value="1">Miễn Phí</option>
                            <option value="2">Thu Phí</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="action" name="createLesson" class="mb-1 btn btn-outline-success">Thêm mới</button>
            </div>
        </div>
    </div>
</div>
