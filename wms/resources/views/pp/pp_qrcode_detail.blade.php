@extends('layouts.app')

@if(isset($_GET["ids"]))
	@define $pp = \App\pp::whereIn("id",multiple_to_array($_GET["ids"]))->get()
@elseif(isset($_GET["n"]))
	@define $pp = \App\pp::orderby("created_at","desc")->limit($_GET["n"])->get()
@else
	@define $pp = \App\pp::all()
@endif

@section('content')
<div class="container"  id="pp_qrcode">
    <table align='center' border="1" valign='middle' width='672' id='qcode_print_show' style='font-size:13px;text-align:center;border-collapse:collapse;table-layout:fixed;word-break:break-all; word-wrap:break-all;border-color: black;'>
    	<tr>
    	@for($i=0;$i < sizeof($pp); $i++)
    		@if($i != 0 && $i%4 == 0)
    		</tr><tr>
    		@endif
    		<td style='border-right:solid 0px;'>
    			{{$pp[$i]->pcode}} {{$pp[$i]->pname}}<br>{{20000000000+PJCODE*1000000+$pp[$i]->id}}
    		</td>
    		<td style='width:59px;border-left:solid 0px;'>
    			{!! QrCode::size(58)->generate(20000000000+PJCODE*1000000+$pp[$i]->id); !!}
    		</td>
    	@endfor
		@while($i%4 != 0)
			<td style='border-right:solid 0px;'></td><td style='width:59px;border-left:solid 0px;'></td>
			@define $i++
		@endwhile
    	</tr>
    </table>
</div>
<div class="container" style="text-align: center;margin-top: 20px">
	<button class="btn btn-default btn-small" onclick="print_object('#pp_qrcode')">打印</button>
</div>
@endsection

@push('scripts')
<script type="text/javascript"></script>
@endpush
