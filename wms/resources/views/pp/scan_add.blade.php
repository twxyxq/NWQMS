@extends('layouts.scan')


@section('scan-info')
	@include('conn/datatables')
@endsection

@push('scripts')
<script type="text/javascript">
	function ajax_post_success(data){
		//alert_flavr(data.msg);
		$("#example").DataTable().draw(false);
	}
</script>
@endpush