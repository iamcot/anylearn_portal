@inject('transServ','App\Services\TransactionService')
@extends('layout')
@section('rightFixedTop')
<form>
    <a href="#" class="btn btn-success btn-sm finExpendClick" data-expend-id="">Thêm</a>
</form>
@endsection
@section('body')

<div class="card shadow">
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="">
                <tr>
                    <th class="text-center" width="10%" scope="col">#ID</th>
                    <th class="text-center">User (SDT)</th>
                    <th class="text-center">Loại</th>
                    <th class="text-center">Số tiền</th>
                    <th class="text-center">Nội dung</th>
                    <th class="text-center">Ngày</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($transaction))
                @foreach($transaction as $row)
                <tr>
                    <th class="text-center" scope="row">{{ $row->id }}
                        <a href="#" class="finExpendClick" data-expend-id="{{ $row->id }}" data-title="{{ $row->content }}" data-date="{{ $row->created_at }}" data-ref_user_id="{{ $row->ref_user_id }}" data-amount="{{ $row->amount }}" data-type="{{ $row->type }}" data-pay_method="{{ $row->pay_method }}" data-comment="{{ $row->pay_info }}"><i class="fa fa-edit"></i></a>

                    </th>
                    <td class="text-center" scope="row">@if(!empty($row->user)) {{ $row->user->name }} ({{ $row->user->phone }}) @endif</td>
                    <td class="text-center" scope="row">{{ $row->type }}</td>
                    <td class="text-center" scope="row">{{ number_format($row->amount) }}</td>
                    <td class="text-center" scope="row">{{ $row->content }}</td>
                    <td class="text-center">{{ date('d/m/y', strtotime($row->created_at)) }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

    </div>
    <div class="card-footer">
        <div>{{ $transaction->links() }}</div>
    </div>
</div>


@endsection
@include('dialog.fin_expendatures')
@section('jscript')
@parent
<script>
    $('.finExpendClick').click(function() {
        var expendId = $(this).data("expend-id");
        $("#expendid").val(expendId);
        if (expendId > 0) {
            $("#form_title").val($(this).data("title"));
            $("#form_ref_user_id").val($(this).data("ref_user_id"));
            $("#form_type").val($(this).data("type"));
            if ($(this).data("pay_method") == 'cash') {
                $("#pay_method_cash").prop("checked", true);
            } else {
                $("#pay_method_atm").prop("checked", true);
            }
            $("#form_amount").val($(this).data("amount"));
            $("#form_comment").val($(this).data("comment"));
        } else {
            $("#form_title").val("");
            $("#form_ref_user_id").val("");
            $("#form_type").val("");
            $("#pay_method_cash").prop("checked", true);
            $("#form_amount").val("");
            $("#form_comment").val("");
        }
        $('#finExpendaturesModal').modal('show');
    });
</script>
@endsection