@extends('layouts.panel_table')

@push('scripts')
<script type="text/javascript">
	
	$("[name='RT'],[name='UT'],[name='PT'],[name='MT'],[name='SA'],[name='HB']").attr("readonly",false);
	
</script>
@endpush