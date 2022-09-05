
<div class="container">
    <div class="row">
        <div class="panel">
            <div class="cover-photo">
              <div class="fb-timeline-img">
                  <img src="{{ auth()->user()->banner }}" alt="">
              </div>
              
            </div>
            <div class="panel-body">
              <div class="profile-thumb">
                  <img src="{{ auth()->user()->image }}" alt="">
              </div>
              <h2 style="margin-top:5px"><a class="text-black" >{{ Auth::user()->name }}</a></h2>

            </div>
        </div>
  </div>
</div>
<p>
