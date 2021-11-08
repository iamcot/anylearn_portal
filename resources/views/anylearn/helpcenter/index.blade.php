@inject('userServ','App\Services\UserServices')
@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.layout')

@section('body')
<ul class="nav nav-tabs" id="classtab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link text-secondary {{ session('tab', 'done') == 'done' ? 'active' : '' }}" id="done-tab" data-bs-toggle="tab" data-bs-target="#done" type="button" role="tab" aria-controls="done" aria-selected="true">anyMEMBER</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-secondary {{ session('tab', 'done') == 'open' ? 'active' : '' }}" id="open-tab" data-bs-toggle="tab" data-bs-target="#open" type="button" role="tab" aria-controls="open" aria-selected="true">anyCENTER</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-secondary {{ session('tab', 'done') == 'fav' ? 'active' : '' }}" id="fav-tab" data-bs-toggle="tab" data-bs-target="#fav" type="button" role="tab" aria-controls="fav" aria-selected="true">anyPROFESSOR</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-secondary {{ session('tab', 'done') == 'checkout' ? 'active' : '' }}" id="checkout-tab" data-bs-toggle="tab" data-bs-target="#checkout" type="button" role="tab" aria-controls="checkout" aria-selected="true">Thanh toán</button>
    </li>
</ul>

<div class="tab-content border border-top-0 mb-5 shadow bg-white" id="myTabContent">
    <div class="tab-pane fade {{ session('tab', 'done') == 'done' ? 'show active' : '' }} p-2" id="done" role="tabpanel" aria-labelledby="done-tab">
        {!! $member->value ?? "" !!}
    </div>
    <div class="tab-pane fade {{ session('tab', 'done') == 'open' ? 'show active' : '' }} p-2" id="open" role="tabpanel" aria-labelledby="open-tab">
        {!! $school->value ?? "" !!}
    </div>
    <div class="tab-pane fade {{ session('tab', 'done') == 'fav' ? 'show active' : '' }} p-2" id="fav" role="tabpanel" aria-labelledby="fav-tab">
        {!! $teacher->value ?? "" !!}
    </div>
    <div class="tab-pane fade {{ session('tab', 'done') == 'checkout' ? 'show active' : '' }} p-2" id="checkout" role="tabpanel" aria-labelledby="checkout-tab">
        {!! $checkout->value ?? "" !!}
    </div>
</div>
@endsection