@extends('admin._layoutNew')
@section('page-head')

@stop
@section('page-content')
    <header class="larry-personal-tit">
    </header><!-- /header -->
    <div class="larry-personal-body clearfix">
        <form class="layui-form col-lg-5">
            <div class="layui-form-item">
                <label class="layui-form-label">名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" autocomplete="off" class="layui-input" value="@if (isset( $result['name'])){{  $result['name'] }}@endif" placeholder="请输入昵称">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">简介</label>
                <div class="layui-input-block">
                    <input type="text" name="profile" autocomplete="off" class="layui-input" value="@if (isset( $result['profile'])){{  $result['profile'] }}@endif" placeholder="请输入简介">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">详情</label>
                <div class="layui-input-block">
                    <input type="text" name="detail" autocomplete="off" class="layui-input" value="@if (isset( $result['detail'])){{  $result['detail'] }}@endif" placeholder="请输入详情">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">价格</label>
                <div class="layui-input-block">
                    <input type="text" name="price" autocomplete="off" class="layui-input" value="@if (isset( $result['price'])){{  $result['price'] }}@endif" placeholder="请输入价格">
                </div>
            </div>

            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">缩略图</label>
                <div class="layui-input-block">
                    <button class="layui-btn" type="button" id="upload_test">选择图片</button>
                    <br>
                    <img src="@if(!empty($result->thumbnail)){{$result->thumbnail}}@endif" id="img_thumbnail" class="thumbnail" style="display: @if(!empty($result->thumbnail)){{"block"}}@else{{"none"}}@endif;max-width: 200px;height: auto;margin-top: 5px;">
                    <input type="hidden" name="thumbnail" id="thumbnail" value="@if(!empty($result->thumbnail)){{$result->thumbnail}}@endif">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">剩余数量</label>
                <div class="layui-input-block">
                    <input type="text" name="number" autocomplete="off" class="layui-input" value="@if (isset( $result['number'])){{  $result['number'] }}@endif" placeholder="剩余数量">
                </div>
            </div>
            <input type="hidden" name="id" value="@if (isset( $result['id'])){{  $result['id'] }}@endif">
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="ltc_submit">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
@stop
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
    </script>
    <script type="text/javascript">

        layui.use(['form','upload','layer'], function () {
            var layer = layui.layer;
            var form = layui.form;
            var $ = layui.$;
            form.on('submit(ltc_submit)', function (data) {
                var data = data.field;
                $.ajax({
                    url: '/admin/ltc/add',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (res) {
                        layer.alert(res.message);
                        if(res.type == 'ok') {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);
                            parent.window.location.reload();
                        }else{
                            return false;
                        }
                    }
                });
                return false;
            });

        });


    </script>
@stop