@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')

    <button class="layui-btn layui-btn-normal layui-btn-radius" id="add">添加矿机</button>
    <div class="layui-form">
        <table id="ltclist" lay-filter="ltclist"></table>

        <script type="text/html" id="barDemo">
            <a class="layui-btn layui-btn-xs" lay-event="edit">修改</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">删除</a>
        </script>

@endsection

        @section('scripts')
            <script>
                window.onload = function() {
                    document.onkeydown=function(event){
                        var e = event || window.event || arguments.callee.caller.arguments[0];
                        if(e && e.keyCode==13){ // enter 键
                            $('#mobile_search').click();
                        }
                    };
                    layui.use(['element', 'form', 'layer', 'table'], function () {
                        var element = layui.element;
                        var layer = layui.layer;
                        var table = layui.table;
                        var $ = layui.$;
                        var form = layui.form;
                        $('#add').click(function(){layer_show('添加矿机', '/admin/ltc/add');});
                        function tbRend(url) {
                            table.render({
                                elem: '#ltclist'
                                , url: url
                                , page: true
                                ,limit: 20
                                , cols: [[
                                    { field: 'id', title: 'ID', minWidth: 50}
                                    , {field:'name',title: '名称',minWidth: 150}
                                    , {field:'profile',title:'简介', minWidth:150}
                                    , {field:'detail',title:'详情', minWidth:200}
                                    , {field:'level_name',title:'会员等级', minWidth:200}
                                    , {field:'price',title:'价格', minWidth:50}
                                    , {field:'number',title:'剩余数量', minWidth:100}
                                    , {field:'thumbnail',title:'缩略图', minWidth:100}
                                    , {field:'create_time',title:'时间', minWidth:100}
                                    , {fixed: 'right', title: '操作', minWidth: 150, align: 'center', toolbar: '#barDemo'}
                                ]]
                            });
                        }
                        tbRend("{{url('/admin/ltc/list')}}");
                        //监听工具条
                        table.on('tool(ltclist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                            var data = obj.data;
                            var layEvent = obj.event;
                            var tr = obj.tr;
                            if (layEvent === 'delete') { //删除
                                layer.confirm('真的要删除吗？', function (index) {
                                    //向服务端发送删除指令
                                    $.ajax({
                                        url: '{{url('admin/ltc/del')}}',
                                        type: 'post',
                                        dataType: 'json',
                                        data: {id: data.id},
                                        success: function (res) {
                                            if (res.type == 'ok') {
                                                layer.alert(res.message);
                                                obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                                layer.close(index);

                                            } else {
                                                layer.close(index);
                                                layer.alert(res.message);
                                            }
                                        }
                                    });
                                });
                            } else if (layEvent === 'edit') { //编辑
                                var index = layer.open({
                                    title: '修改矿机'
                                    , type: 2
                                    , content: '{{url('/admin/ltc/edit')}}?id=' + data.id
                                    , maxmin: true
                                });
                                layer.full(index);
                            }
                        });
                    });
                }
            </script>

@endsection