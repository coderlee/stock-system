@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">币种名称</label>
            <div class="layui-input-block">
                <input type="text" name="name" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->name}}">
            </div>
        </div>
        <!--
        <div class="layui-form-item">
            <label class="layui-form-label">token</label>
            <div class="layui-input-block">
                <input type="text" name="token" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->token}}">
            </div>
        </div>
        -->
        <!--
        <div class="layui-form-item">
            <label class="layui-form-label">收币地址</label>
            <div class="layui-input-block">
                <input type="text" name="get_address" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->get_address}}">
            </div>
        </div>
        -->
        <div class="layui-form-item">
            <label class="layui-form-label">排序</label>
            <div class="layui-input-block">
                <input type="number" class="layui-input" id="end_time" name="sort" value="{{$result->sort}}" placeholder="排序为升序">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">最小提币数量</label>
            <div class="layui-input-block">
                <input type="number" class="layui-input" id="end_time" name="min_number" value="{{$result->min_number}}" placeholder="">
            </div>
        </div>
         <div class="layui-form-item">
            <label class="layui-form-label">提币费率</label>
            <div class="layui-input-block">
                <input type="number" class="layui-input" id="end_time" name="rate" value="{{$result->rate}}" placeholder="">
            </div>
        </div>
        <div class="layui-form-item">
                <label class="layui-form-label">是否法币</label>
                <div class="layui-input-block">
                    <input type="radio" name="is_legal" value="1" title="是" @if($result->is_legal ==1) checked @endif >
                    <input type="radio" name="is_legal" value="0" title="否" @if($result->is_legal ==0) checked @endif>
                </div>
        </div>
        <div class="layui-form-item">
                <label class="layui-form-label">基于币种</label>
                <div class="layui-input-block">
                    <input type="radio" name="type" value="btc" title="基于BTC" @if($result->type =='btc') checked @endif >
                    <input type="radio" name="type" value="eth" title="基于ETH" @if($result->type =='eth') checked @endif>
                    <input type="radio" name="type" value="erc20" title="基于erc20" @if($result->type =='erc20') checked @endif>
                </div>
        </div>
         <div class="layui-form-item">
                <label class="layui-form-label">是否合约币</label>
                <div class="layui-input-block">
                    <input type="radio" name="is_lever" value="1" title="是" @if($result->is_lever ==1) checked @endif >
                    <input type="radio" name="is_lever" value="0" title="否" @if($result->is_lever ==0) checked @endif>
                </div>
        </div>
        <div class="layui-form-item">
                <label class="layui-form-label">是否撮合交易</label>
                <div class="layui-input-block">
                    <input type="radio" name="is_match" value="1" title="是" @if($result->is_match ==1) checked @endif >
                    <input type="radio" name="is_match" value="0" title="否" @if($result->is_match ==0) checked @endif>
                </div>
        </div>
        <div class="layui-form-item">
                <label class="layui-form-label">总账号</label>
                <div class="layui-input-block">
                <input type="text" name="total_account"  autocomplete="off" placeholder="" class="layui-input" value="{{$result->total_account}}">
                </div>
        </div>
        <div class="layui-form-item">
                <label class="layui-form-label">私钥</label>
                <div class="layui-input-block">
                <input type="text" name="key"  autocomplete="off" placeholder="" class="layui-input" value="{{$result->key}}">
                </div>
        </div>
        <div class="layui-form-item">
                <label class="layui-form-label">合约地址</label>
                <div class="layui-input-block">
                <input type="text" name="contract_address"  autocomplete="off" placeholder="" class="layui-input" value="{{$result->contract_address}}">
                </div>
        </div>
        <!-- <div class="layui-form-item">
                <label class="layui-form-label">总地址</label>
                <div class="layui-input-block">
                <input type="text" name="total_address" lay-verify="required" autocomplete="off" placeholder="" class="layui-input" value="{{$result->total_address}}">
                </div>
        </div> -->
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">币种 logo</label>
            <div class="layui-input-block">
                <button class="layui-btn" type="button" id="upload_test">选择图片</button>
                <br>
                <img src="@if(!empty($result->logo)){{$result->logo}}@endif" id="img_thumbnail" class="thumbnail" style="display: @if(!empty($result->logo)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">
                <input type="hidden" name="logo" id="thumbnail" value="@if(!empty($result->logo)){{$result->logo}}@endif">
            </div>
        </div>
         <div class="layui-form-item">
                <label class="layui-form-label">充值地址</label>
                <div class="layui-input-block">
                <input type="text" name="charge_address"  autocomplete="off" placeholder="" class="layui-input" value="{{$result->charge_address}}">
                </div>
        </div>
		<div class="layui-form-item layui-form-text">
            <label class="layui-form-label">账户二维码</label>
            <div class="layui-input-block">
                <button class="layui-btn" type="button" id="upload_img">选择图片</button>
                <br>
                <img src="@if(!empty($result->qr_code)){{$result->qr_code}}@endif" id="img_thumbnail2" class="thumbnail" style="display: @if(!empty($result->qr_code)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">
                <input type="hidden" name="qr_code" id="thumbnail2" value="@if(!empty($result->qr_code)){{$result->qr_code}}@endif">
            </div>
        </div>


        <input type="hidden" name="id" value="{{$result->id}}">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>

@endsection

@section('scripts')
    <script>
        layui.use('upload', function(){
            var upload = layui.upload;

            //执行实例
            var uploadInst = upload.render({
                elem: '#upload_test' //绑定元素
                ,url: '{{URL("api/upload")}}' //上传接口
                ,done: function(res){
                    //上传完毕回调
                    if (res.type == "ok"){
                        $("#thumbnail").val(res.message)
                        $("#img_thumbnail").show()
                        $("#img_thumbnail").attr("src",res.message)
                    } else{
                        alert(res.message)
                    }
                }
                ,error: function(){
                    //请求异常回调
                }
            });
        });
        layui.use('upload', function(){
            var upload = layui.upload;

            //执行实例
            var uploadInst = upload.render({
                elem: '#upload_img' //绑定元素
                ,url: '{{URL("api/upload")}}' //上传接口
                ,done: function(res){
                    //上传完毕回调
                    if (res.type == "ok"){
                        $("#thumbnail2").val(res.message)
                        $("#img_thumbnail2").show()
                        $("#img_thumbnail2").attr("src",res.message)
                    } else{
                        alert(res.message)
                    }
                }
                ,error: function(){
                    //请求异常回调
                }
            });
        });


        layui.use(['form','laydate'],function () {
            var form = layui.form
                ,$ = layui.jquery
                ,laydate = layui.laydate
                ,index = parent.layer.getFrameIndex(window.name);
            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                $.ajax({
                    url:'{{url('admin/currency_add')}}'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
                        if(res.type=='error'){
                            layer.msg(res.message);
                        }else{
                            parent.layer.close(index);
                            parent.window.location.reload();
                        }
                    }
                });
                return false;
            });
        });
    </script>

@endsection