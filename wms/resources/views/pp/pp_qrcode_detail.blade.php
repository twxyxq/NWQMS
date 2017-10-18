@extends('layouts.app')

@if(isset($_GET["ids"]))
	@define $pp = \App\pp::whereIn("id",multiple_to_array($_GET["ids"]))->get()
@elseif(isset($_GET["n"]))
	@define $pp = \App\pp::orderby("created_at","desc")->limit($_GET["n"])->get()
@else
	@define $pp = \App\pp::all()
@endif

@section('content')
<div class="container" id="pp_qrcode">
    <table id="qrcode_table" align='center' border="1" valign='middle' width='672' id='qcode_print_show' style='font-size:13px;text-align:center;border-collapse:collapse;table-layout:fixed;word-break:break-all; word-wrap:break-all;border-color: black;'>
    	<tr>
    	@for($i=0;$i < sizeof($pp); $i++)
    		@if($i != 0 && $i%4 == 0)
    		</tr><tr>
    		@endif
    		<td style='border-right:solid 0px;' ondblclick="cp_this({{$i}})" code="text_{{$i}}">
    			{{$pp[$i]->pcode}} {{$pp[$i]->pname}}<br>{{20000000000+PJCODE*1000000+$pp[$i]->id}}
    		</td>
    		<td style='width:59px;border-left:solid 0px;' ondblclick="cp_this({{$i}})" code="code_{{$i}}">
    			{!! QrCode::size(58)->generate(20000000000+PJCODE*1000000+$pp[$i]->id); !!}
    		</td>
    	@endfor
		@while($i%4 != 0)
			<td style='border-right:solid 0px;' code="input_text"></td><td style='width:59px;border-left:solid 0px;' code="input_code"></td>
			@define $i++
		@endwhile
    	</tr>
    </table>
</div>
<div class="container" style="text-align: center;margin-top: 20px">
    <button class="btn btn-default btn-small" onclick="print_object('#pp_qrcode')">打印</button>
</div>
<div class="container" style="text-align: center;margin-top: 20px">
    双击可复制
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    function cp_this(id){
        if ($("[code='input_text']").length == 0) {
            $("#qrcode_table").append("<tr><td style='border-right:solid 0px;' code='input_text'></td><td style='width:59px;border-left:solid 0px;' code='input_code'></td><td style='border-right:solid 0px;' code='input_text'></td><td style='width:59px;border-left:solid 0px;' code='input_code'></td><td style='border-right:solid 0px;' code='input_text'></td><td style='width:59px;border-left:solid 0px;' code='input_code'></td><td style='border-right:solid 0px;' code='input_text'></td><td style='width:59px;border-left:solid 0px;' code='input_code'></td></tr>");
        }
        var index = $("td[code^='text']").length;
        $("[code='input_text']:first").html($("[code='text_"+id+"']").html());
        $("[code='input_text']:first").attr("ondblclick","cp_this("+index+")");
        $("[code='input_text']:first").attr("code","text_"+index);
        $("[code='input_code']:first").html($("[code='code_"+id+"']").html());
        $("[code='input_code']:first").attr("ondblclick","cp_this("+index+")");
        $("[code='input_code']:first").attr("code","code_"+index);
        
    }
</script>
@endpush
