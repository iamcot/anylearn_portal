@inject('itemServ', 'App\Services\ItemServices')
<section class="carousel4">
    <div class="mxauto myauto justifycontentcenter">
        {{-- {{ <div id="{{ $carouselId }}">
            <h2 class="m2 fwbold textuppercase">{{ __($title) }}</h2>
            <div class="owlcarousel owltheme" >

                @foreach ($data as $class)
                <a class="p1 classBox" href="{{ $itemServ>classUrl($class>id) }}">
                        <div class="cardimg">
                            <div class="imagebox">
                                <img src="{{ $class>image }}" class="imgfluid">
                            </div>
                            <div class="classtitle mt1 fwbold p1 textsuccess">@if ($class > is_hot) <span class="badge bgdanger "><i class="fas fafire"></i> HOT</span> @endif {{ $class>title }}</div>
                            <div class="p1">
                                <span class="textdanger fwbold">{{ number_format($class>price, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="">@include('anylearn.widget.rating', ['score' => $class>rating ?? 0])</div>
                            <div class="classprice ps1 pe1">
                                <span class=" textsecondary">{{ $class>short_content }}</span>
                            </div>
                            <div class="p2 textcenter mb2">
                                <button class="btn btnwhite roundedpill shadow border0 w75 textsuccess fwbold">@lang('CHI TIẾT')</button>
                            </div>
                        </div>
                @endforeach
            </div>
        </div> }} --}}
    </div>
</section>
