@inject('itemServ', 'App\Services\ItemServices')
<footer>
    <div class="container p-5">
        <div class="row">
            <div class="col-md-4">
                <p><img src="/cdn/anylearn/img/logo-color.svg" class="img-fluid" alt=""></p>
            </div>
            <div class="col-md-3 d-md-block d-none">
                <h6 class="fw-bold">THÔNG TIN HỖ TRỢ</h6>
            </div>
            <div class="col-md-5 d-md-block d-none">
                <h6 class="fw-bold">TIN TỨC MỚI NHẤT</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <ul class="list-unstyled text-secondary">
                    <li><i class="fas fa-briefcase text-success m-2"></i>MSKD: 0316363793</li>
                    <li><i class="fas fa-envelope text-success m-2"></i>info@anylearn.vn</li>
                    <li><i class="fas fa-phone text-success m-2"></i>+84 37 490 0344</li>
                    <li><i class="fas fa-map-marker-alt text-success m-2"></i>352/28A Nguyễn Tất Thành, P.13, Q.4,<br> TP Hồ Chí Minh, Việt Nam
                    </li>
                </ul>
            </div>
           
            <div class="col-md-3">
                <h6 class="fw-bold d-md-none d-sm-block">THÔNG TIN HỖ TRỢ</h6>
                <ul class="list-unstyled text-secondary">
                    <li><p><a href="/helpcenter" class="text-secondary text-decoration-none">Trung tâm hỗ trợ</a></p></li>
                    <li><p><a href="/privacy" class="text-secondary text-decoration-none">Chính sách</a></p></li>
                    <li><p><a href="/guide?p=guide_toc" class="text-secondary text-decoration-none">Điều khoản</a></p></li>
                    <li><p><a href="https://anylearn.vn/info" class="text-secondary text-decoration-none">Giới thiệu</a></p></li>
                </ul>
            </div>
            <div class="col-md-5">
                <h6 class="fw-bold d-md-none d-sm-block">TIN TỨC MỚI NHẤT</h6>
                <ul class="list-unstyled text-secondary">
                    @foreach($itemServ->footerNews() as $news)
                    <li><p><a href="{{ $itemServ->articleUrl($news->id) }}" class="text-secondary text-decoration-none">{{ $news->title }}</a></p></li>
                    @endforeach
                </ul>
                <ul class="list-unstyled list-inline">
                    <li class="list-inline-item"><a href="https://www.facebook.com/anylearnhockhonggioihan"><i class="fab fa-2x fa-facebook"></i></a></li>
                    <li class="list-inline-item"><a href="https://www.youtube.com/channel/UCam71id1lM8tZuMfjy2DDRw"><i class="fab fa-2x fa-youtube text-danger"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>