@extends('admin._layoutNew')

@section('page-head')
<style>
    .hide {
        display: none;
    }
</style>
@endsection

@section('page-content')
    <form class="layui-form" action="">
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <div class="layui-form-item">
                        <label class="layui-form-label">用户账号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="account_number" id="account_number" lay-verify="required" autocomplete="off" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">老师名字</label>
                        <div class="layui-input-inline">
                            <input type="text" name="teacher_name" id="teacher_name" lay-verify="required" autocomplete="off" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">累计收益率</label>
                        <div class="layui-input-inline">
                            <input type="text" name="total_profit_rate" id="total_profit_rate" lay-verify="required" autocomplete="off" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">近3周交易勝率</label>
                        <div class="layui-input-inline">
                            <input type="text" name="three_week_profit" id="three_week_profit" lay-verify="required" autocomplete="off" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">累計跟隨人數</label>
                        <div class="layui-input-inline">
                            <input type="text" name="total_follower" id="total_follower" lay-verify="required" autocomplete="off" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">总跟单人数</label>
                        <div class="layui-input-inline">
                            <input type="text" name="trade_count" id="trade_count" lay-verify="required" autocomplete="off" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">总交易量</label>
                        <div class="layui-input-inline">
                            <input type="text" name="total_day" id="total_day" lay-verify="required" autocomplete="off" placeholder="" class="layui-input">
                        </div>
                    </div>
                </div>
            </div>
       
        <input id="currency_id" type="hidden" name="id" >
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
    layui.use(['upload', 'form', 'laydate', 'element', 'layer'], function () {
        var upload = layui.upload 
            ,form = layui.form
            ,layer = layui.layer
            ,$ = layui.$
            ,laydate = layui.laydate
            ,index = parent.layer.getFrameIndex(window.name)
            ,element = layui.element
            var list=sessionStorage.getItem('hdgd');
            var currency_id='';
            if(list){
                list=JSON.parse(list);
                currency_id=list.id;
                $("#teacher_name").val(list.teacher_name)
                $("#account_number").val(list.account_number)
                $("#total_profit_rate").val(list.total_profit_rate)
                $("#three_week_profit").val(list.three_week_profit)
                $("#total_follower").val(list.total_follower)
                $("#total_day").val(list.total_day)
                $("#trade_count").val(list.trade_count)
            }
        
        //监听提交
        form.on('submit(demo1)', function(data){
            
            var data = data.field;
            if(currency_id){
                data.id=currency_id;
            }
            $.ajax({
                url:'{{url('admin/users/gd_user/add')}}'
                ,type:'post'
                ,dataType:'json'
                ,data: data
                ,success: function(res) {
                    layer.msg(res.message, {
                        time: 2000
                        ,end: function () {
                            if(res.type == 'ok') {
                                parent.layer.close(index);
                                parent.window.location.reload();
                            }
                        }
                    });
                    
                }
            });
            return false;
        });
        form.on('checkbox(microtrade)', function (data) {
            if (data.elem.checked) {
                $('#micro_trade_fee').removeClass('hide');
            } else {
                $('#micro_trade_fee').addClass('hide');
            }
        });
       
        //获取验证码
        $('#get_code').click(function () {
            var that_btn = $(this);
            $.ajax({
                url: '/admin/safe/verificationcode'
                ,type: 'GET'
                ,success: function (res) {
                    if (res.type == 'ok') {
                        that_btn.attr('disabled', true);
                        that_btn.toggleClass('layui-btn-disabled');
                    }
                    layer.msg(res.message, {
                        time: 3000
                    });
                }
                ,error: function () {
                    layer.msg('网络错误');
                }
            });
        });
        // 设置转出地址
        $('#set_out_address').click(function () {
            parent.layui.layer.open({
                title: '设置转出地址'
                ,type: 2
                ,content: '/admin/currency/set_out_address/' + currency_id
                ,area: ['490px', '350px']
            });
        });
        // 设置转入地址
        $('#set_in_address').click(function () {
            parent.layui.layer.open({
                title: '设置转入地址'
                ,type: 2
                ,content: '/admin/currency/set_in_address/' + currency_id
                ,area: ['490px', '250px']
            });
        });
        
    });
</script>

@endsection