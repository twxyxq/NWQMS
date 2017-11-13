@extends('layouts.page_detail')

@define $user = \App\user::find($id);

@push('style')
<style type="text/css">
	.auth_item[auth='1'] {
		background-color: pink;
	}
    .divider {
        height: 1px;
        margin: 9px 0;
        overflow: hidden;
        background-color: #e5e5e5;
    }
</style>
@endpush

@section('panel-body')
<div class="row">
    <div class="col-sm-2">工号：</div>
    <div class="col-sm-2">{{$user->code}}</div>
    <div class="col-sm-2">姓名：</div>
    <div class="col-sm-2">{{$user->name}}</div>
</div>
<div class="row">
	<div class="col-sm-2">权限：</div>
    <div class="col-sm-10">{{$user->auth}}</div>
</div>

<div class="row divider"></div>

<div class="row">

	<div class="col-sm-12"><strong>权限设置</strong></div>

	<div class="col-sm-2">管理：</div>
    <div class="col-sm-10">
    	<span id="super_manager" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{super_manager}')!==false?1:0}}">超级管理员</span>
    	<span id="manager" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{manager}')!==false?1:0}}">管理员</span>
    </div>

	<div class="col-sm-2">微信：</div>
    <div class="col-sm-10">
    	<span id="wechat_manager" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{wechat_manager}')!==false?1:0}}">微信管理员</span>
    	<span id="wechat" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{wechat}')!==false?1:0}}">微信用户</span>
    </div>

	<div class="col-sm-2">焊接：</div>
    <div class="col-sm-10">
    	<span id="weld_syn" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{weld_syn}')!==false?1:0}}">焊接综合</span>
        <span id="weld_manager" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{weld_manager}')!==false?1:0}}">焊接管理</span>
        <span id="weld_qc3" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{weld_qc3}')!==false?1:0}}">焊接qc3</span>
    	<span id="weld_view" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{weld_view}')!==false?1:0}}">焊接访问</span>
    </div>

	<div class="col-sm-2">材料：</div>
    <div class="col-sm-10">
    	<span id="m_syn" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{m_syn}')!==false?1:0}}">焊材综合</span>
    	<span id="m_LOC" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{m_LOC}')!==false?1:0}}">现场焊材库</span>
    	<span id="m_PRE" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{m_PRE}')!==false?1:0}}">准备区焊材库</span>
    	<span id="material_view" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{material_view}')!==false?1:0}}">焊材访问</span>
    </div>

	<div class="col-sm-2">检验：</div>
    <div class="col-sm-10">
    	<span id="exam_syn" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{exam_syn}')!==false?1:0}}">检验综合</span>
        <span id="exam_manager" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{exam_manager}')!==false?1:0}}">检验管理</span>
        <span id="exam_qc3" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{exam_qc3}')!==false?1:0}}">检验qc3</span>
    	<span id="exam_view" class="auth_item btn btn-default btn-small" auth="{{strpos($user->auth,'{exam_view}')!==false?1:0}}">检验访问</span>
    </div>
@if(Auth::user()->user_level > $user->user_level || Auth::user()->user_level == 9)

    <div class="col-sm-12" style="text-align: center;">
        <button class="btn btn-success btn-small" onclick="confirm_auth()" style="margin: 5px 0"> 确 认 </button>
    </div>

    <div class="col-sm-12 divider"></div>

    <div class="col-sm-12"><strong>重置密码</strong></div>

    <div class="col-sm-12">
        <button class="btn btn-info btn-small" onclick="reset_pwd()">重置密码</button> &nbsp; 
        <button class="btn btn-info btn-small" onclick="reset_ch_pwd()">重置和更换随机密码</button> &nbsp; 
        当前随机密码：{{$user->default_key}}
    </div>

    <div class="col-sm-12 divider"></div>

    <div class="col-sm-12"><strong>级别与分组</strong></div>

    <div class="col-sm-12">
        <div class="col-sm-3">级别：
            <select id="user_level" class="form-control input-sm">
                @for($i = Auth::user()->user_level; $i >= 0; $i--)
                    <option value="{{$i}}" {{$user->user_level==$i?"selected":""}}>{{$i}}</option>
                @endfor
            </select>
        </div>
        <div class="col-sm-3">分组：
            <select id="user_org" class="form-control input-sm">
                <option value="N/A" {{$user->user_org=="N/A"?"selected":""}}>N/A</option>
                <option value="焊接" {{$user->user_org=="焊接"?"selected":""}}>焊接</option>
                <option value="检验" {{$user->user_org=="检验"?"selected":""}}>检验</option>
                <option value="管道" {{$user->user_org=="管道"?"selected":""}}>管道</option>
                <option value="电仪" {{$user->user_org=="电仪"?"selected":""}}>电仪</option>
                <option value="已禁用" {{$user->user_org=="已禁用"?"selected":""}}>已禁用</option>
            </select>
        </div>
    </div>
    <div class="col-sm-12">
        <button class="btn btn-success btn-small" onclick="confirm_level_and_org()" style="margin: 5px 0"> 确 认 </button>
    </div>

    @if(Auth::user()->user_level == 9)
        <div class="col-sm-12 divider"></div>

        <div class="col-sm-12">
            <a class="btn btn-warning btn-small" href="/panel/user_login?id={{$id}}" style="margin: 5px 0">用此用户登录</a>
        </div>
    @endif
@endif

</div>
@endsection


@if(Auth::user()->user_level > $user->user_level || Auth::user()->user_level == 9)
    @push('scripts')
    <script type="text/javascript">

    	$(".auth_item").on("click",function(){
            var o = $(this);
    		if (o.attr("auth") == 1) {
    			o.attr("auth",0);
    		} else {
    			if ($.inArray(o.attr("id"),["super_manager","manager","wechat_manager"]) >= 0 && {{Auth::user()->user_level}} != 9) {
                    alert_flavr("您不能授予该权限，请联系管理员");
                } else {
                    o.attr("auth",1);
                }
    		}
    	});

    	function confirm_auth(){
    		var auth = "";
    		$(".auth_item[auth='1']").each(function(){
    			auth += "{"+$(this).attr("id")+"}";
    		});
    		ajax_post("/panel/user_auth_post",{"auth":auth,"id":{{$id}}},function(data){
    			if (data.suc == 1) {
    				location.reload();
    			} else {
    				alert_flavr(data.msg);
    			}
    		});
    	}

        function reset_pwd(){
            if (confirm("确认重置？")) {
                ajax_post("/panel/reset_pwd",{"id":{{$id}}},function(data){
                    if (data.suc == 1) {
                        alert_flavr(data.msg,function(){
                            location.reload();
                        });
                    } else {
                        alert_flavr(data.msg);
                    }
                });
            }
        }

        function reset_ch_pwd(){
            if (confirm("确认重置？")) {
                ajax_post("/panel/reset_pwd",{"ch":1,"id":{{$id}}},function(data){
                    if (data.suc == 1) {
                        alert_flavr(data.msg,function(){
                            location.reload();
                        });
                    } else {
                        alert_flavr(data.msg);
                    }
                });
            }
        }
        function confirm_level_and_org(){
            if (confirm("确认修改？")) {
                ajax_post("/panel/level_and_org_post",{"id":{{$id}},"level":$("#user_level").val(),"org":$("#user_org").val()},function(data){
                    if (data.suc == 1) {
                        alert_flavr(data.msg,function(){
                            location.reload();
                        });
                    } else {
                        alert_flavr(data.msg);
                    }
                });
            }
        }
    </script>
    @endpush
@endif