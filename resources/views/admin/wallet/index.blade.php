@extends('admin._layoutNew')

@section('page-head')
<style>
.layui-form-label {
    width: unset;
}
.layui-form-item .layui-inline , .layui-form-item .layui-input-inline {
    margin-right: 0px;
}
.percent::after {
    content: '%';
}
.layui-table-total [data-field="reward_qty"] div {
    text-align:right;
}
.layui-table-total div {
    font-weight: bolder;
}
.layui-form-label {
    width: unset;
}
.block {
    border:1px solid #fff;
    height: 100px;
    background: #2caac3;
    color: #fff;
    text-align: center;
}
.block .title {
    padding-top: 20px;
    font-size: 20px;
    font-weight: bold;
}
.block .num-value {
    padding-top: 10px;
    font-size: 16px;
}
.block .block-icon {
    float:left;
    width:50%;
}
.block .block-content {
    float:left;
    width:50%;
}
.block .main-icon {
    margin-top: 20px;
}
.block-icon .main-icon .layui-block-icon {
    font-size:60px;
}
</style>
@endsection

@section('page-content')
<div class="layui-form">
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">币种</label>
            <div class="layui-input-inline" style="width: 120px;">
                <select name="currency">
                    <option value="-1">全部</option>
                    @foreach ($currencies as $key => $value)
                    <option value="{{$value->id}}">{{strtoupper($value->name)}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">账号</label>
            <div class="layui-input-inline" style="width: 120px">
                <input class="layui-input" name="account_number" type="text" value="" placeholder="请输入会员账号">
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">地址</label>
            <div class="layui-input-inline" style="width: 380px">
                <input class="layui-input" name="address" type="text" value="" placeholder="请输入地址">
            </div>
        </div>
        <div class="layui-inline">
            <div class="layui-input-inline" style="width: 60px;">
                <button class="layui-btn" lay-submit="search" lay-filter="search"><i class="layui-icon">&#xe615;</i></button>
            </div>
        </div>
    </div>
</div>
<div class="layui-row">
    <div class="layui-col-md4">
        <div class="block">
            <div class="block-icon">
                <p class="main-icon">
                    <i class="layui-icon layui-icon-rmb layui-block-icon"></i>
                </p>
            </div>
            <div class="block-content" id="legal_total">
                <p class="title">法币总额:</p>
                <p class="num-value"></p>  
            </div>
        </div>
    </div>
    <div class="layui-col-md4">
        <div class="block">
            <div class="block-icon">
                <p class="main-icon">
                    <i class="layui-icon layui-icon-dollar layui-block-icon"></i>
                </p>
            </div>
            <div class="block-content" id="change_total">
                <p class="title">交易币总额:</p>
                <p class="num-value"></p>  
            </div>
        </div>
    </div>
    <div class="layui-col-md4">
        <div class="block">
            <div class="block-icon">
                <p class="main-icon">
                    <i class="layui-icon layui-icon-diamond layui-block-icon"></i>
                </p>
            </div>
            <div class="block-content" id="lever_total">
                <p class="title">杠杆币总额:</p>
                <p class="num-value"></p>  
            </div>
        </div>
    </div>
</div>
<table id="data_table" lay-filter="data_table"></table>
@endsection
@section('scripts')
<script type="text/html" id="toolbar">
    <button class="layui-btn layui-btn-xs" lay-event="update">链上更新</button>
    <button class="layui-btn layui-btn-xs layui-btn-primary" lay-event="transfer">打入手续费</button>
    <button class="layui-btn layui-btn-xs layui-btn-warm" lay-event="collect">余额归拢</button>
</script>
<script>
    layui.use(['table', 'layer', 'form'], function() {
        var table = layui.table
            ,layer = layui.layer
            ,form = layui.form
            ,$ = layui.$
        var data_table = table.render({
            elem: '#data_table'
            ,url: '/admin/wallet/list'
            ,height: 'full-200'
            ,page: true
            ,toolbar: true
            ,totalRow: true
            ,cols: [
                [
                    {field: 'id', title: 'id', width: 70, rowspan: 2}
                    ,{field: 'account_number', title: '账号', width: 120, rowspan: 2}
                    ,{field: 'currency_name', title: '币种', width: 100, totalRowText: '小计', rowspan: 2}
                    ,{field: 'address', title: '地址', width: 380, rowspan: 2}
                    ,{field: 'old_balance', title: '链上余额', width: 150, totalRow: true, rowspan: 2}
                    ,{title: '法币', width: 380, colspan: 2, rowspan: 1, align: "center"}
                    ,{title: '闪兑', width: 380, colspan: 2, rowspan: 1, align: "center"}
                    ,{title: '杠杆币', width: 380, colspan: 2, rowspan: 1, align: "center"}
                    ,{field: 'gl_time_str', title: '归拢时间', width: 170, hide: true, rowspan: 2}
                    ,{field: 'operate', fixed: 'right', title: '操作', width: 260, toolbar: '#toolbar', rowspan: 2}
                ], [
                    {field: 'legal_balance', title: '余额', width: 130, totalRow: true}
                    ,{field: 'lock_legal_balance', title: '冻结', width: 130, totalRow: true}
                    ,{field: 'change_balance', title: '余额', width: 130, totalRow: true}
                    ,{field: 'lock_change_balance', title: '冻结', width: 130, totalRow: true}
                    ,{field: 'lever_balance', title: '余额', width: 130, totalRow: true}
                    ,{field: 'lock_lever_balance', title: '冻结', width: 130, totalRow: true} 
                ]
            ],
            done: function(res, curr, count) {
                var total = res.extra_data.total;
                $('#legal_total p.num-value').text(total.legal_balance);
                $('#change_total p.num-value').text(total.change_balance);
                $('#lever_total p.num-value').text(total.lever_balance);   
            }
        });
        form.on('submit(search)', function (data) {
            data_table.reload({
                where: data.field
                ,page: {
                    curr: 1 //重新从第 1 页开始
                }
            });
            return false;
        });
        table.on('tool(data_table)', function (obj) {
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
            var tr = obj.tr; //获得当前行 tr 的DOM对象
            
            if (layEvent === 'update') {
                layer.confirm('确定要更新链上余额吗?', function (index) {
                    var loading = layer.load(1, {time: 30 * 1000});
                    layer.close(index);
                    $.ajax({
                        url: '/admin/wallet/update_balance'
                        ,type: 'get'
                        ,data: {id: data.id}
                        ,success: function (res) {
                            if(res.type=='error') {
                                layer.msg(res.message);
                            } else {
                                layer.msg(res.message);
                                //parent.layer.close(index);
                            }
                        }
                        ,error: function () {
                            layer.msg('网络错误');
                        }
                        ,complete: function () {
                            layer.close(loading);
                        }
                    });
                });
                
            } else if (layEvent === 'transfer') {

                layer.confirm('确定要打入手续费吗?', function (index) {
                    var loading = layer.load(1, {time: 30 * 1000});
                    layer.close(index);
                    $.ajax({
                        url: '/admin/wallet/transfer_poundage'
                        ,type: 'get'
                        ,data: {id: data.id}
                        ,success: function (res) {
                            if(res.type=='error') {
                                layer.msg(res.message);
                            } else {
                                layer.msg(res.message);
                                //parent.layer.close(index);
                            }
                        }
                        ,error: function () {
                            layer.msg('网络错误');
                        }
                        ,complete: function () {
                            layer.close(loading);
                        }
                    });
                });
                
            } else if (layEvent === 'collect') {

                layer.confirm('确定要归拢链上余额吗?', function (index) {
                    var loading = layer.load(1, {time: 30 * 1000});
                    layer.close(index);
                    $.ajax({
                        url: '/admin/wallet/collect'
                        ,type: 'get'
                        ,data: {id: data.id}
                        ,success: function (res) {
                            if(res.type=='error') {
                                layer.msg(res.message);
                            } else {
                                layer.msg(res.message);
                                //parent.layer.close(index);
                            }
                        }
                        ,error: function () {
                            layer.msg('网络错误');
                        }
                        ,complete: function () {
                            layer.close(loading);
                        }
                    });
                });
            }
        });
    });
</script>
@endsection