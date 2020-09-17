@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')

    <div class="layui-inline layui-row">
        <label class="layui-form-label">用户帐号</label>
        <div class="layui-input-inline">
            <input type="text" name="account" placeholder="用户手机号或邮箱" autocomplete="off" class="layui-input" value="">
        </div>
        <button class="layui-btn btn-search" id="mobile_search" lay-submit lay-filter="mobile_search"> <i class="layui-icon">&#xe615;</i> </button>
    </div>
    <button class="layui-btn layui-btn-normal" onclick="javascrtpt:window.location.href='{{url('/admin/user/csv')}}'">导出用户</button>
  
    
        <table id="userlist" lay-filter="userlist"></table>

        <script type="text/html" id="barDemo">
            <a class="layui-btn layui-btn-xs" lay-event="users_wallet">账户管理</a>
           
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="edit">编辑</a>
            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete">删除</a>
        </script>
      
        <script type="text/html" id="switchTpl">
        <input type="checkbox" name="status" value="@{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="sexDemo" @{{ d.status == 1 ? 'checked' : '' }}>
      </script>
         <script type="text/html" id="switchTpl2">
        <input type="checkbox" name="blacklist" value="@{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="blacklist" @{{ d.is_blacklist == 1 ? 'checked' : '' }}>
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

                        /*$('#add_user').click(function(){layer_show('添加会员', '/admin/user/add');});*/

                        form.on('submit(mobile_search)',function(obj){
                            var account =  $("input[name='account']").val();

                            tbRend("{{url('/admin/user/list')}}?account="+account);
                            return false;
                        });
                        function tbRend(url) {
                            table.render({
                                elem: '#userlist'
                                , url: url
                                , page: true
                                ,limit: 20
                                , cols: [[
                                    { field: 'id', title: 'ID', width: 100}
                                    
                                    , {field:'phone',title:'手机号', width:150}
                                    , {field:'email',title:'邮箱', width:150}
                                   , {field:'card_id',title: '身份证号',width: 180}
                                    , {field:'account_number',title:'交易账号', width:150}
                                    /*, {field:'transactions_num',title:'交易量', width:100}
                                    , {field:'success_rate',title:'成交率', width:100}*/
                                    
                                    
                                    ,{field:'status', title:'是否锁定', width:90, templet: '#switchTpl'}
                                    ,{field:'is_blacklist', title:'加入黑名单', width:90, templet: '#switchTpl2'}
                                    , {field:'time',title:'注册时间', width:200} 
                                    , {fixed: 'right', title: '操作', width: 280, align: 'center', toolbar: '#barDemo'}
                                ]]
                            });
                        }
                        tbRend("{{url('/admin/user/list')}}");

                        //监听锁定操作
                        form.on('switch(sexDemo)', function(obj){
                            var id = this.value;
                            
                            $.ajax({
                                url:'{{url('admin/user/lock')}}',
                                type:'post',
                                dataType:'json',
                                data:{id:id},
                                success:function (res) {
                                    layer.msg(res.message);
                                   
                                }
                            });
                        });

                                //监听加入黑名单
                        form.on('switch(blacklist)', function(obj){
                            var id = this.value;
                            
                            $.ajax({
                                url:'{{url('admin/user/blacklist')}}',
                                type:'post',
                                dataType:'json',
                                data:{id:id},
                                success:function (res) {
                                    layer.msg(res.message);
                                   
                                }
                            });
                        });

                        //监听工具条
                        table.on('tool(userlist)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                            var data = obj.data;
                            var layEvent = obj.event;
                            var tr = obj.tr;
                            if (layEvent === 'delete') { //删除
                                layer.confirm('真的要删除吗？', function (index) {
                                    //向服务端发送删除指令
                                    $.ajax({
                                        url: "{{url('admin/user/del')}}",
                                        type: 'post',
                                        dataType: 'json',
                                        data: {id: data.id},
                                        success: function (res) {
                                            if (res.type == 'ok') {
                                                obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                                layer.close(index);
                                            } else {
                                                layer.close(index);
                                                layer.alert(res.message);
                                            }
                                        }
                                    });
                                });
                            }else if (layEvent === 'edit'){ //编辑
                                layer_show('编辑会员','{{url('admin/user/edit')}}?id='+data.id);
                            }else if (layEvent === 'users_wallet') {
                                var index = layer.open({
                                    title: '账户管理'
                                    , type: 2
                                    , content: '{{url('/admin/user/users_wallet')}}?id=' + data.id
                                    , maxmin: true
                                });
                                layer.full(index);
                            }
                        });
                    });
                }
            </script>

@endsection