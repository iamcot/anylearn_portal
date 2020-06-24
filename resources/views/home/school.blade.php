<section class="portfolio_section big_padding" style="padding-bottom: 0;" id="work">
    <div class="container">
        <h2 class="default_section_heading text-center text-orange">
            Những đơn vị giáo dục chất lượng hàng đầu
        </h2>
        <hr class="default_divider default_divider_blue default_divider_big">
        <div class="">
            <ul class="school-logo">
                @foreach(config('home_schools', []) as $school)
                <li>
                    <img src="{{ $school['image'] }}" alt="">
                </li>
                @endforeach
            </ul>
        </div>
    </div>

</section>