<div class="mb-3 mb-lg-0 card">
    <h5 class="bg-light card-header">Thêm Phụ Phí</h5>
    <form action="" method="get">
        <input type="hidden" name="tab" value="extrafee">
        <div class="card-body"><label class="form-label">Các Phụ Phí<span class="text-danger">*</span></label>
            <ul class="list-unstyled mb-0">
                @foreach ($extra as $item)
                    <li>
                        <div class="d-flex align-items-center py-3 border-top border-300 row">
                            <p class="fs--1 mb-0 me-6 col-md-7">{{ $item->title }}</p>
                            <strong class="col-md-3">Số tiền: {{ number_format($item->price) }}₫</strong>
                            <div class="col-md-2">
                                <button
                                    type="button"class="icon-item icon-item-sm rounded-3 fs--2 btn btn-default btn-sm"
                                    onclick="update({{ $item->id }},'{{ $item->title }}',{{ $item->price }})"><i
                                        class="fas fa-pen"></i></button>
                                <input type="hidden" name="iddelete" value="{{ $item->id }}">
                                <button type="button"
                                    class="icon-item icon-item-sm rounded-3 fs--2 btn btn-default btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="delete" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-danger" id="exampleModalLabel">Bạn đang yêu
                                                    cầu xóa!!!</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Bạn có chắc muốn xóa khoản phụ phí này?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" name="action" value="deleteextrafee"
                                                    class="btn btn-primary">Xóa</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <button type="submit" name="action" value="deleteextrafee"
                                    class="icon-item icon-item-sm rounded-3 fs--2 btn btn-default btn-sm"><i
                                        class="fas fa-trash"></i></button> --}}
                            </div>

                        </div>
                    </li>
                @endforeach
            </ul>
    </form>
    <form action="" method="get">
        <div class="position-relative mb-4">
            <div class="input-group">
                <input type="hidden" name="tab" value="extrafee">
                <input type="hidden" name="idextrafee" value="" id="idextrafee">
                <input name="titleextrafee" id="titleextrafee" required="" placeholder="Thêm phụ phí" type="text"
                    class="pe-4 form-control" value="">
                <input name="priceextrafee" id="priceextrafee" required="" placeholder="Số tiền phụ phí"
                    type="number" class="pe-4 form-control" min="0" value="">
                <div class="input-group-append">
                    <button class="btn btn-success" name="action" value="addextrafee" type="submit">Lưu</button>
                </div>
            </div>
        </div>
    </form>
</div>

</div>
<script>
    function update(id, title, price) {
        console.log(id, title, price);
        document.getElementById('idextrafee').value = id;
        document.getElementById('titleextrafee').value = title;
        document.getElementById('priceextrafee').value = price;
        // window.scrollTo(0, document.body.scrollHeight);
    }
</script>
