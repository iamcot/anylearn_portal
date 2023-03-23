@inject('itemServ', 'App\Services\ItemServices')
<footer>
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <h4>TRUNG TÂM HỖ TRỢ</h4>
                <ul>
                    <ul class="row">
                        @foreach ($itemServ->footertopKnowledge() as $knowledge)
                            <li class="col-xs-12 col-md-12"><a class=" text-black"
                                    href="{{ route('helpcenter.knowledge', ['id' => $knowledge->id, 'url' => $knowledge->url]) }}">{{ $knowledge->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                    {{-- <a href="/helpcenter">
                        <li>Trung Tâm Trợ Giúp </li>
                    </a>
                    <a href="/helpcenter/5/anypoint-la-gi.html">
                        <li>anyPOINT là gì?</li>
                    </a> --}}
                </ul>
                <h4>THEO DÕI CHÚNG TÔI</h4>
                <div class="social-links">
                    <a target="_blank" href="https://www.youtube.com/channel/UCam71id1lM8tZuMfjy2DDRw"><img
                            src="/cdn/img/youtube.png" alt=""></a>
                    <a target="_blank" href="https://www.facebook.com/anylearnhockhonggioihan"><img
                            src="/cdn/img/facebook.png" alt=""></a>
                    <a target="_blank" href="https://zalo.me/0374900344"><img src="/cdn/img/zalo.png"
                            alt=""></a>
                </div>
            </div>
            <div class="col-sm-3">
                <h4>ĐIỀU KHOẢN & CHÍNH SÁCH</h4>
                <ul>
                    <a href="/guide?p=guide_toc">
                        <li>Điều Khoản Sử Dụng</li>
                    </a>
                    <a href="/privacy">
                        <li>Chính Sách Bảo Mật</li>
                    </a>
                </ul>
                <h4>
                    <p>TẢI ỨNG DỤNG anyLEARN</p>
                </h4>
                <div class="app-links">
                    <a target="_blank" href="https://play.google.com/store/apps/details?id=vn.anylearn&hl=vi&gl=US"><img
                            src="/cdn/img/ggplay.png" class="img-fluid" width="40%"></a>
                    <a target="_blank" href="https://apps.apple.com/vn/app/anylearn/id1518155412"><img
                            src="/cdn/img/appstore.png" class="img-fluid" width="45%"></a>
                </div>
            </div>
            <div class="col-sm-3">
                <h4>TIN TỨC</h4>
                <ul class="list-unstyled text-secondary">
                    @foreach($itemServ->footerNews() as $news)
                    <p><a data-spm="footer-news.{{ $news->id }}" href="{{ $itemServ->articleUrl($news->id) }}" class="text-secondary text-decoration-none"><li>{{ $news->title }}</li></a></p>
                    @endforeach
                </ul>
            </div>
            <div class="col-sm-3">
                <h4>THANH TOÁN</h4>
                <img src="/cdn/img/Visa.png" width="20%" class="img-fluid">
                <img src="/cdn/img/mastercard.png" width="20%" class="img-fluid">
                <img src="/cdn/img/onepay.png" width="20%" class="img-fluid">
            </div>
            {{-- <div class="col-sm-3 mt-2">

            </div>
            <div class="col-sm-3 mt-2">

            </div> --}}

        </div>
    </div>
</footer>
