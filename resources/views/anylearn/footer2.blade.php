@inject('itemServ', 'App\Services\ItemServices')
<footer class="bg-white text-light " style="padding: 0;">
    <div class="container py-5">
        <div class="row">
            <div class="col-sm-3">
                <h4><a href="/articles" class="text-decoration-none text-black pb-2">TRUNG TÂM HỖ TRỢ</a> </h4>
                <ul class="list-unstyled pt-2">
                    @foreach ($itemServ->footertopKnowledge() as $knowledge)
                        <li><a class="text-secondary text-decoration-none  "
                                href="{{ route('helpcenter.knowledge', ['id' => $knowledge->id, 'url' => $knowledge->url]) }}">{{ $knowledge->title }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-sm-3">
                <h4 class="text-decoration-none text-black pb-2">ĐIỀU KHOẢN & CHÍNH SÁCH</h4>
                <ul class="list-unstyled">
                    <li><a href="/guide?p=guide_toc" class="text-secondary text-decoration-none">Điều Khoản Sử Dụng</a>
                    </li>
                    <li><a href="/privacy" class="text-secondary text-decoration-none">Chính Sách Bảo Mật</a></li>
                </ul>
                <h4 class=" text-decoration-none text-black" id="download-app">TẢI ỨNG DỤNG anyLEARN</h4>
                <div class="app-links">
                    <a target="_blank" href="https://play.google.com/store/apps/details?id=vn.anylearn&hl=vi&gl=US"><img
                            src="/cdn/img/ggplay.png" class="img-fluid" width="900" height="auto"></a>
                    <a target="_blank" href="https://apps.apple.com/vn/app/anylearn/id1518155412"><img
                            src="/cdn/img/appstore.png" class="img-fluid" width="1000"></a>
                </div>
            </div>
            <div class="col-sm-3">
                <h4 class="text-decoration-none text-black pb-2">TIN TỨC</h4>
                <ul class="list-unstyled">
                    @foreach ($itemServ->footerNews() as $news)
                        {{-- <p><a data-spm="footer-news.{{ $news->id }}" href="{{ $itemServ->articleUrl($news->id) }}" class="text-secondary text-decoration-none"><li>{{ $news->title }}</li></a></p> --}}
                        <li><a href="{{ $itemServ->articleUrl($news->id) }}"
                                class="text-secondary text-decoration-none">{{ $news->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-sm-3">
                <h4 class="text-decoration-none text-black pb-2">THANH TOÁN</h4>
                <div class=" d-flex justify-content-between">
                        <img src="/cdn/img/Visa.png" width="20%" class="img-fluid">
                        <img src="/cdn/img/mastercard.png" width="20%" class="img-fluid">

                        <img src="/cdn/img/onepay.png" width="20%" class="img-fluid">
                        <img src="/cdn/img/MoMoLogo.png" width="20%" class="img-fluid">

                </div>


                <h4 class="mt-2 text-decoration-none text-black pb-2">THEO DÕI CHÚNG TÔI</h4>
                <div class="social-links">
                    <a target="_blank" href="https://www.youtube.com/channel/UCam71id1lM8tZuMfjy2DDRw"><img
                            src="/cdn/img/youtube.png" alt=""></a>
                    <a target="_blank" href="https://www.facebook.com/anylearnhockhonggioihan"><img
                            src="/cdn/img/facebook.png" alt=""></a>
                    <a target="_blank" href="https://zalo.me/0374900344"><img src="/cdn/img/zalo.png"
                            alt=""></a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid bg-secondary text-center py-3">
        <p class="m-0">CÔNG TY CỔ PHẦN ĐẦU TƯ VÀ GIÁO DỤC anyLEARN</p>
        <p class="m-0">Giấy CNĐKDN: 0316363793 cấp lần đầu ngày 02/07/2020</p>
        <p class="m-0">Cấp bởi: Phòng đăng ký kinh doanh - Sở Kế hoạch Đầu tư thành phố Hồ Chí Minh.</p>
        <p class="m-0">Địa chỉ: 161/4 Nguyễn Văn Thủ, Phường Đa Kao, Quận 1, Thành phố Hồ Chí Minh</p>
    </div>
</footer>
