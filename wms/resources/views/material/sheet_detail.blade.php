@extends('layouts.app')



@section('content')
<div class="container" id="sheet_detail">
    {!!$sheet!!}
</div>
<div class="container" style="text-align: center;margin-top: 20px">
	<button class="btn btn-default btn-small" onclick="print_object('#sheet_detail')">打印</button>
</div>
@endsection

@push('scripts')
<script type="text/javascript"></script>
@endpush
