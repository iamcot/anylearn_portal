<nav aria-label="breadcrumb">
  <ol class="breadcrumb bg-white">
    <li class="breadcrumb-item"><a href="/"><i class="fa fa-home"></i></a></li>
    @for($i = 0; $i < count($breadcrumb); $i++)
        @if ($i == count($breadcrumb) - 1)
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb[$i]['text'] }}</li>
        @else
            <li class="breadcrumb-item active"><a href="{{ $breadcrumb[$i]['url'] }}">{{ $breadcrumb[$i]['text'] }}</a></li>
        @endif
    @endfor
  </ol>
</nav>