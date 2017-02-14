@extends('layouts.page')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">焊口导入</div>

                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/wj') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="excelinput" value="1">
                        <div class="form-group{{ $errors->has('excelfile') ? ' has-error' : '' }}">
                            <label for="code" class="col-md-3 control-label">选择文件：</label>

                            <div class="col-md-6">
                                <input id="excelfile" type="file" class="form-control" name="excelfile" value="{{ old('excelfile') }}" required autofocus>

                                @if ($errors->has('excelfile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('excelfile') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    导入文件
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">导入说明</div>

                <div class="panel-body">
                    <ol id="intr_input">
                        <li>请下载导入模板&nbsp;&nbsp;&nbsp;&nbsp;（<a href="../images/wj/wjmodel.xls" target="_blank">点击下载</a>）&nbsp;&nbsp;&nbsp;&nbsp;将数据填入模板，标题不能改动，最后一列“END”不要填<br><img src="../images/wj/input_end.jpg"></li>
                        <li>如焊口号为空，系统可自动生成焊口号，不需填写，如焊口号与程序规定的编码规则不一致，则需填写<br><img src="../images/wj/input_vcode.jpg"></li>
                        <li>确保除了数据之外的其他地方不要填写任何数据，如果模板错误、数据错误会有相应的提示，请修改</li>
                        <li>如果数据错误过多，将撤销本次导入，请重新确认后导入</li>
                        <li>为了防止错漏，请自行记下须导入的数据数量和名称，以便核对</li>
                        <li>再次提醒：上传前务必检查数据的准确性，以免导入之后修改比较繁琐</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection