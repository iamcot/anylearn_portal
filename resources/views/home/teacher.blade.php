<section class="team_section big_padding text-center " id="team">
    <div class="container">
        <div class="col-xs-12">
            <h2 class="default_section_heading text-center font-600 text-blue">
                Ban cố vấn và đội ngũ chuyên gia
            </h2>
            <hr class="default_divider default_divider_blue default_divider_big">
            <ul class="teacher_list">
                @foreach(config('home_teachers', []) as $teacher)
                <li class="team_detail">
                    <div class="team_detail_inner">
                        <img src="{{ $teacher['avatar'] }}" alt="team_img">
                    </div>
                </li>
                @endforeach

            </ul>
        </div>
    </div>
</section>
<!-- Half Section -->
<section class="half_section_main half_section_right bg_grey">
    <div class="half_section_picture hidden-sm hidden-xs"></div>
    <div class="container container_big">
        <div class="row">
            <div class="col-md-6">
                <div class="side_section_text big_padding">
                    <!-- <p class="default_small_heading raleway blue_color font_200">Đội ngũ giảng dạy</p> -->
                    <h3 class="default_section_heading raleway text-blue font_300">Chọn học cùng <span class="text-green">any</span><span class="text-blue">LEARN</span> luôn đảm bảo, vì:</h3>
                    <hr class="default_divider default_divider_blue " style="margin-left: 0;">
                    <p class="default_text_light default_text open_sans">
                    </p>
                    <div class="progress_bar_outer_div">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="progress_outer">
                                    <h3 class="small_heading_navy raleway default_small_heading navy_blue h3_pink font_400">Khóa học được chọn lọc</h3>
                                    <div class="progress progress_bar bg_white">
                                        <div class="progress-bar bg_pink" style="width: 100%;" role="progressbar" aria-valuenow="84" aria-valuemin="0" aria-valuemax="100">
                                            <span class="bg_navy">100%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="progress_outer">
                                    <h3 class="small_heading_navy default_small_heading navy_blue raleway font_400">Tư vấn bởi chuyên gia</h3>
                                    <div class="progress progress_bar bg_white">
                                        <div class="progress-bar bg_pink" style="width: 100%;" role="progressbar" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100">
                                            <span class="bg_navy">100%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="progress_outer">
                                    <h3 class="small_heading_navy default_small_heading navy_blue raleway font_400">Đánh giá tốt từ học viên</h3>
                                    <div class="progress progress_bar bg_white">
                                        <div class="progress-bar bg_pink" style="width: 100%;" role="progressbar" aria-valuenow="63" aria-valuemin="0" aria-valuemax="100">
                                            <span class="bg_navy">100%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="progress_outer">
                                    <h3 class="small_heading_navy default_small_heading navy_blue raleway font_400">Bạn bè tin cậy giới thiệu</h3>
                                    <div class="progress progress_bar bg_white">
                                        <div class="progress-bar bg_pink" style="width: 100%;" role="progressbar" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100">
                                            <span class="bg_navy">100%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- half Section Ended -->