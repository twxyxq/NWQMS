@extends('layouts.scan')


@section('scan-info')
	@include('conn/datatables')
@endsection

@section('success-fn')
	alert_flavr(data.msg);
	$("#code_input").val("");
	$("#example").DataTable().draw();
@endsection