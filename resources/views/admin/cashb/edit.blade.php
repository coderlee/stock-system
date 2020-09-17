@extends('admin._layoutNew')

@section('page-head')

@endsection

@section('page-content')
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">提币信息</label>
            <div class="layui-input-block">
               <table class="layui-table">
                <tbody>
                    <tr>
                        <td>
                            账户名：{{$wallet_out->account_number}}
                        </td>
                        <td>
                            币种：{{$wallet_out->currency_name}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            币种类型：基于{{$wallet_out->currency_type}}
                        </td>
                        <td>
                            费率：{{$wallet_out->rate}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            提币数量：{{$wallet_out->number}}
                        </td>
                        <td>
                            实际提币数量：{{$wallet_out->real_number}}
                        </td>
                    </tr>
                    <tr>
                         <td colspan="2">
                            提币地址：{{$wallet_out->address}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            申请时间：{{$wallet_out->create_time}}
                        </td>
                        <td>
                            当前状态：@if($wallet_out->status==1) 提交申请
								     @elseif($wallet_out->status==2) 提币成功
								     @elseif($wallet_out->status==3) 提币失败
								    @else
                                    @endif
                        </td>
                    </tr>

                </tbody>
            </table>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">反馈信息</label>
            <div class="layui-input-block">
               <textarea name="notes" id="" cols="90" rows="10">{{$wallet_out->notes}}</textarea>
            </div>
        </div>
        <input type="hidden" name="id" value="">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" name='id' value='{{$wallet_out->id}}'>
                @if($wallet_out->status==1)
                <button class="layui-btn" lay-submit="" lay-filter="demo1" name='method' value="done">确认提币</button>
                <button class="layui-btn layui-btn-danger" lay-submit="" lay-filter="demo2">退回申请</button>
                @endif
            </div>
        </div>
    </form>

@endsection

@section('scripts')
    <script>


        layui.use(['form','laydate'],function () {
            var form = layui.form
                ,$ = layui.jquery
                ,laydate = layui.laydate
                ,index = parent.layer.getFrameIndex(window.name);
            //监听提交
            form.on('submit(demo1)', function(data){
                var data = data.field;
                $.ajax({
                    url:'{{url('admin/cashb_done')}}'+'?method=done'
                    ,type:'post'
                    ,dataType:'json'
                    ,data : data
                    ,success:function(res){
                        if(res.type=='error'){
                            layer.msg(res.message);
                        }else{
                            layer.msg(res.message);
                            parent.layer.close(index);
                            parent.window.location.reload();
                        }
                    }
                });
                return false;
            });
            form.on('submit(demo2)', function(data){
                var data = data.field;
                $.ajax({
                    url:'{{url('admin/cashb_done')}}'
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