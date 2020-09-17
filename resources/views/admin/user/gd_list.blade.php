@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <div style="margin-top: 10px;width: 100%;margin-left: 10px;">
        

        <form class="layui-form layui-form-pane layui-inline" action="">

            <!--<div class="layui-inline" style="margin-left: 50px;">-->
            <!--    <label class="layui-form-label">用户名</label>-->
            <!--    <div class="layui-input-inline">-->
            <!--        <input type="text" name="account_number" autocomplete="off" class="layui-input">-->
            <!--    </div>-->
            <!--</div>-->
            <!--<div class="layui-inline">-->
            <!--    <div class="layui-input-inline">-->
            <!--        <button class="layui-btn" lay-submit="" lay-filter="mobile_search"><i class="layui-icon">&#xe615;</i></button>-->
            <!--    </div>-->
            <!--</div>-->
            



        </form>
         <button class="layui-btn layui-btn-normal" id="add">添加跟单信息</button>
       

    <script type="text/html" id="switchTpl">
        <input type="checkbox" name="is_recommend" value="@{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="sexDemo" @{{ d.is_recommend == 1 ? 'checked' : '' }}>
    </script>

    <table id="demo" lay-filter="test"></table>
    <script type="text/html" id="barDemo">
    
    <a class="layui-btn layui-btn-xs" lay-event="edit">修改</a>
    
    </script>
    <script type="text/html" id="statustml">
        @{{d.status==1 ? '<span class="layui-badge layui-bg-green">'+'申请提币'+'</span>' : '' }}
       

    </script>
@endsection

@section('scripts')
    <script>

        layui.use(['table','form'], function(){
            var table = layui.table;
            var $ = layui.jquery;
            var form = layui.form;
            //第一个实例
            table.render({
                elem: '#demo'
                ,url: "{{url('admin/users/gd_user/list')}}" //数据接口
                ,page: true //开启分页
                ,id:'mobileSearch'
                ,cols: [[ //表头
                    {field: 'id', title: 'ID', width:80, sort: true}
                    ,{field: 'account_number', title: '用户账号', width:100}
                    ,{field: 'teacher_name', title: '老师名字', Width:100}
                    ,{field: 'total_profit_rate', title: '累计收益率', width:100}
                    ,{field: 'three_week_profit', title: '近3周交易勝率', minWidth:110}
                    ,{field: 'total_follower', title: '累計跟隨人數', minWidth:80}
                    ,{field: 'trade_count', title: '总跟单人数', minWidth:110}
                    ,{field: 'total_day', title: '总交易量', minWidth:110}
                    ,{title:'操作',minWidth:120,toolbar: '#barDemo'}

                ]]
            });
            //监听热卖操作
            // form.on('switch(sexDemo)', function(obj){
            //     var id = this.value;
            //     $.ajax({
            //         url:'{{url('admin/product_hot')}}',
            //         type:'post',
            //         dataType:'json',
            //         data:{id:id},
            //         success:function (res) {
            //             if(res.error != 0){
            //                 layer.msg(res.msg);
            //             }
            //         }
            //     });
            // });

            table.on('tool(test)', function(obj){
                var data = obj.data;
                if(obj.event === 'del'){
                    
                } else if(obj.event === 'edit'){
                    sessionStorage.setItem('hdgd',JSON.stringify(data));
                    layer_show('编辑','{{url('/admin/users/gd_user/edit')}}',430,500);
                } else if(obj.event === 'back'){
                    
                }
            });
            $("#add").click(function(){
                sessionStorage.setItem('hdgd','');
                layer_show('添加','{{url('/admin/users/gd_user/edit')}}',430,500);
            })
            //监听提交
            form.on('submit(mobile_search)', function(data){
                var account_number = data.field.account_number;
                table.reload('mobileSearch',{
                    where:{account_number:account_number},
                    page: {curr: 1}         //重新从第一页开始
                });
                return false;
            });

        });
    </script>

@endsection