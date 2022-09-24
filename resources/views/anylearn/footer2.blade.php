@inject('itemServ', 'App\Services\ItemServices')
<footer>
    <div class="row h-100">
        <div class="col-sm-6 bg-primary pb-5">
            <div class="row mt-5 mb-3">
                <div class="col-sm-5 ps-5 text-center d-none d-lg-block">
                    <p><img src="/cdn/anylearn/img/logo-white.svg" class="img-fluid" alt=""></p>
                    <ul class="list-unstyled list-inline">
                        <li class="list-inline-item"><a class="" href="https://www.facebook.com/anylearnhockhonggioihan"><i class="fab fa-2x fa-facebook-square"></i></a></li>
                        <li class="list-inline-item"><a href="https://www.youtube.com/channel/UCam71id1lM8tZuMfjy2DDRw"><i class="fab fa-2x fa-youtube-square text-danger"></i></a></li>
                    </ul>
                </div>
                <div class="col-sm-5 d-lg-none">
                    <p><img src="/cdn/anylearn/img/logo-white.svg" class="img-fluid" alt=""></p>
                    <ul class="list-unstyled list-inline ps-3">
                        <li class="list-inline-item"><a class="" href="https://www.facebook.com/anylearnhockhonggioihan"><i class="fab fa-2x fa-facebook-square"></i></a></li>
                        <li class="list-inline-item"><a href="https://www.youtube.com/channel/UCam71id1lM8tZuMfjy2DDRw"><i class="fab fa-2x fa-youtube-square text-danger"></i></a></li>
                    </ul>
                </div>
                <div class="col-sm-7 pe-2">
                    <ul class="list-unstyled text-white">
                        <li><i class="fas fa-briefcase text-success m-2"></i>CÔNG TY CỔ PHẦN ĐẦU TƯ VÀ GIÁO DỤC ANYLEARN</li>
                        <li><i class="fas fa-briefcase text-success m-2"></i>MSKD: 0316363793</li>
                        <li><i class="fas fa-envelope text-success m-2"></i>info@anylearn.vn</li>
                        <li><i class="fas fa-phone text-success m-2"></i><a class="text-white" href="tel:0374900344">+84 37 490 0344</a></li>
                        <li><i class="fas fa-map-marker-alt text-success m-2"></i>352/28A Nguyễn Tất Thành, P.13, Q.4, TPHCM
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-sm-6 pb-5" style="background: #FAFAFA;">
            <div class="row mt-3">
                <div class="col-sm-5 ps-5">
                    <div class="fw-bold head_text fs-5">THÔNG TIN</div>
                    <ul class="list-unstyled text-white">
                        <li>
                            <p><a href="/helpcenter" class="text-secondary text-decoration-none">Trung tâm hỗ trợ</a></p>
                        </li>
                        <li>
                            <p><a href="/privacy" class="text-secondary text-decoration-none">Chính sách</a></p>
                        </li>
                        <li>
                            <p><a href="/guide?p=guide_toc" class="text-secondary text-decoration-none">Điều khoản</a></p>
                        </li>
                        <li>
                            <p><a href="https://anylearn.vn/info" class="text-secondary text-decoration-none">Giới thiệu</a></p>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-7 ps-5">
                    <div class="fw-bold head_text fs-5">TIN TỨC</div>
                    <ul class="list-unstyled text-white">
                        @foreach($itemServ->footerNews() as $news)
                        <li>
                            <p><a href="{{ $itemServ->articleUrl($news->id) }}" class="text-secondary text-decoration-none">{{ $news->title }}</a></p>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-md-4">
            <p><img src="/cdn/anylearn/img/logo-color.svg" class="img-fluid" alt=""></p>
        </div>
        <div class="col-md-3 d-md-block d-none head_text">
            <div class="fw-bold">THÔNG TIN HỖ TRỢ</div>
        </div>
        <div class="col-md-5 d-md-block d-none head_text">
            <div class="fw-bold">TIN TỨC MỚI NHẤT</div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <ul class="list-unstyled text-secondary">
                <li><i class="fas fa-briefcase text-success m-2"></i>CÔNG TY CỔ PHẦN ĐẦU TƯ VÀ GIÁO DỤC ANYLEARN</li>
                <li><i class="fas fa-briefcase text-success m-2"></i>MSKD: 0316363793</li>
                <li><i class="fas fa-envelope text-success m-2"></i>info@anylearn.vn</li>
                <li><i class="fas fa-phone text-success m-2"></i><a href="tel:0374900344">+84 37 490 0344</a></li>
                <li><i class="fas fa-map-marker-alt text-success m-2"></i>352/28A Nguyễn Tất Thành, P.13, Q.4,<br> TP Hồ Chí Minh, Việt Nam
                </li>
            </ul>
        </div>

        <div class="col-md-3">
            <div class="fw-bold d-md-none d-sm-block head_text">THÔNG TIN HỖ TRỢ</div>
            <ul class="list-unstyled text-secondary">
                <li>
                    <p><a href="/helpcenter" class="text-secondary text-decoration-none">Trung tâm hỗ trợ</a></p>
                </li>
                <li>
                    <p><a href="/privacy" class="text-secondary text-decoration-none">Chính sách</a></p>
                </li>
                <li>
                    <p><a href="/guide?p=guide_toc" class="text-secondary text-decoration-none">Điều khoản</a></p>
                </li>
                <li>
                    <p><a href="https://anylearn.vn/info" class="text-secondary text-decoration-none">Giới thiệu</a></p>
                </li>
            </ul>
        </div>
        <div class="col-md-5">
            <div class="fw-bold d-md-none d-sm-block head_text">TIN TỨC MỚI NHẤT</div>
            <ul class="list-unstyled text-secondary">
                @foreach($itemServ->footerNews() as $news)
                <li>
                    <p><a href="{{ $itemServ->articleUrl($news->id) }}" class="text-secondary text-decoration-none">{{ $news->title }}</a></p>
                </li>
                @endforeach
            </ul>
            <ul class="list-unstyled list-inline">
                <li class="list-inline-item"><a href="https://www.facebook.com/anylearnhockhonggioihan"><i class="fab fa-2x fa-facebook"></i></a></li>
                <li class="list-inline-item"><a href="https://www.youtube.com/channel/UCam71id1lM8tZuMfjy2DDRw"><i class="fab fa-2x fa-youtube text-danger"></i></a></li>
            </ul>
        </div>
    </div> -->
</footer>
<div id="footer_call"><i class="fas fa-phone text-success m-2 blink_me"></i><a href="tel:0374900344">+84 37 490 0344</a></div>