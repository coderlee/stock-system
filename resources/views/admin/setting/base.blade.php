@extends('admin._layoutNew')
@section('page-head')

@stop
@section('page-content')

    <div class="larry-personal-body clearfix">
        <form class="layui-form col-lg-5">
            <div class="layui-form-item">
                <!--  <label class="layui-form-label">兑换比例</label>
                 <div class="layui-input-block">
                     <input type="text" name="rate" autocomplete="off" class="layui-input" value="">
                 </div> -->
            </div>
            <div id="all">
                <div>
                    <div class="layui-form-item ecology">
                        <div class="layui-inline">
                            <label class="layui-form-label">邮箱账号</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="1" placeholder="" name="phpMailer_username"
                                       type="text" onkeyup=""
                                       value="@if(isset($setting['phpMailer_username'])){{$setting['phpMailer_username'] ?? ''}}@endif">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">Token密码</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required" placeholder="请输入最大值"
                                       name="phpMailer_password" onkeyup="" type="text"
                                       value="@if(isset($setting['phpMailer_password'])){{$setting['phpMailer_password'] ?? ''}}@endif">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">端口</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required" placeholder="请输入比例"
                                       name="phpMailer_port" type="text"
                                       value="@if(isset($setting['phpMailer_port'])){{$setting['phpMailer_port'] ?? ''}}@endif"><span
                                        style="position: absolute;right: 5px;top: 12px;"></span>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">Host</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required" placeholder="请输入最小值"
                                       name="phpMailer_host" type="text"
                                       value="@if(isset($setting['phpMailer_host'])){{$setting['phpMailer_host'] ?? ''}}@endif">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item ecology">


                    </div>
                </div>
            </div>
            <div id="all">
                <div>
                    <div class="layui-form-item ecology">
                        <div class="layui-inline">
                            <label class="layui-form-label">短信宝</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required" placeholder="用户名"
                                       name="smsBao_username" type="text"
                                       value="@if(isset($setting['smsBao_username'])){{$setting['smsBao_username'] ?? '' }}@endif"><span
                                        style="position: absolute;right: 5px;top: 12px;"></span>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">密码</label>
                            <div class="layui-input-inline">
                                <input class="layui-input" lay-verify="required" placeholder="" name="password"
                                       value="@if(isset($setting['password'])){{$setting['password']  ?? '' }}@endif">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">版本号</label>
                <div class="layui-input-block">
                    <input type="text" name="version" autocomplete="off" class="layui-input"
                           value="@if(isset($setting['version'])){{$setting['version'] ?? ''}}@endif">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">PB汇率</label>
                <div class="layui-input-block">
                    <input type="text" name="ExRate" autocomplete="off" class="layui-input"
                           value="@if(isset($setting['ExRate'])){{$setting['ExRate']}}@endif">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">USDT汇率</label>
                <div class="layui-input-block">
                    <input type="text" name="USDTRate" autocomplete="off" class="layui-input"
                           value="@if(isset($setting['USDTRate'])){{$setting['USDTRate']}}@endif">
                </div>
            </div>

            <!--
            <div class="layui-form-item">
                <label class="layui-form-label">隔夜费</label>
                <div class="layui-input-block">
                    <input type="text" name="overnight_fee" autocomplete="off" class="layui-input"
                           value="@if(isset($setting['overnight_fee'])){{$setting['overnight_fee']}}@endif">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">合约手续费</label>
                <div class="layui-input-block">
                    <input type="text" name="lever_fee" autocomplete="off" class="layui-input"
                           value="@if(isset($setting['lever_fee'])){{$setting['lever_fee']}}@endif">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">点差</label>
                <div class="layui-input-block">
                    <input type="text" name="point_number" autocomplete="off" class="layui-input"
                           value="@if(isset($setting['point_number'])){{$setting['point_number']}}@endif">
                </div>
            </div> -->
            <div class="layui-form-item">
                <label class="layui-form-label">止盈止亏功能</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline">
                        <input type="radio" name="user_set_stopprice" value="1" title="打开" @if (isset($setting['user_set_stopprice'])) {{$setting['user_set_stopprice'] == 1 ? 'checked' : ''}} @endif >
                        <input type="radio" name="user_set_stopprice" value="0" title="关闭" @if (isset($setting['user_set_stopprice'])) {{$setting['user_set_stopprice'] == 0 ? 'checked' : ''}} @else checked @endif >
                    </div>
                    <div class="layui-form-mid layui-word-aux">打开用户将可以针对交易设置止盈止亏价</div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">合约交易委托功能</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline">
                        <input type="radio" name="open_lever_entrust" value="1" title="打开" @if (isset($setting['open_lever_entrust'])) {{$setting['open_lever_entrust'] == 1 ? 'checked' : ''}} @endif >
                        <input type="radio" name="open_lever_entrust" value="0" title="关闭" @if (isset($setting['open_lever_entrust'])) {{$setting['open_lever_entrust'] == 0 ? 'checked' : ''}} @else checked @endif >
                    </div>
                    <div class="layui-form-mid layui-word-aux">打开后前台可以进行合约交易委托,即限价交易</div>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">是否开启充提币功能</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline">
                        <input type="radio" name="is_open_CTbi" value="1" title="打开" @if (isset($setting['is_open_CTbi'])) {{$setting['is_open_CTbi'] == 1 ? 'checked' : ''}} @endif >
                        <input type="radio" name="is_open_CTbi" value="0" title="关闭" @if (isset($setting['is_open_CTbi'])) {{$setting['is_open_CTbi'] == 0 ? 'checked' : ''}} @else checked @endif >
                    </div>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">爆仓风险率指标</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline">
                        <input type="text" name="lever_burst_hazard_rate" class="layui-input" value="{{$setting['lever_burst_hazard_rate'] ?? 0 }}" placeholder="合约交易风险率达到或低于设定值时爆仓">
                    </div>
                    <div class="layui-form-mid layui-word-aux">%</div>
                    <div class="layui-form-mid layui-word-aux">用户的风险率达到或低于该值时触发爆仓</div>
                </div>
            </div>

            <!-- <div class="layui-form-item">
                <label class="layui-form-label">杆杠交易倍数</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline">
                        <input type="text" name="lever_allow_multiple" class="layui-input" value="{{$setting['lever_allow_multiple'] ?? 0 }}" placeholder="合约交易允许倍数，多个请用逗号间隔">
                    </div>
                    <div class="layui-form-mid layui-word-aux">%</div>
                </div>
            </div>  -->
            
            <div class="layui-form-item">
                <label class="layui-form-label">赠送虚拟账户</label>
                <div class="layui-input-block">
                    <input type="text" name="give_virtual_account" autocomplete="off" class="layui-input"
                           value="@if(isset($setting['give_virtual_account'])){{$setting['give_virtual_account']}}@endif">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">头寸</label>
                <div class="layui-input-block">
                    <input type="text" name="lever_position" autocomplete="off" class="layui-input" value="@if(isset($setting['lever_position'])){{$setting['lever_position']}}@endif">
                </div>
            </div>


            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="website_submit">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
@stop
@section('scripts')
    <script type="text/javascript">
        layui.use(['form', 'upload', 'layer'], function () {
            var layer = layui.layer;
            var form = layui.form;
            var $ = layui.$;
            form.on('submit(website_submit)', function (data) {
                var data = data.field;
                $.ajax({
                    url: '/admin/setting/postadd',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (res) {
                        layer.msg(res.message);
                    }
                });
                return false;
            });

        });


    </script>
@stop