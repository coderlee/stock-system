## 合约买入/卖出
域名:  
http://www.u9coin.cn  
请求地址:  
*/api/lever/submit*  
请求类型:POST  
|参数名|类型|是否必填|参与签名|说明|
|--|--|:-:|:-:|--|
|share|integer|是|--|交易手数|
|multiple|integer|是|--|放大倍数|
|type|integer|是|--|1.买入,2.卖出|
|legal_id|integer|是|--|法币id|
|currency_id|integer|是|--|交易币id|
|status|integer|是|--|0.限价交易,1.市价交易|
|target_price|float|--|--|当status传0时,设置委托价格|
返回结果:json  
* 成功示例: 
```
{
    type: "ok",
    message: "success message",
}
```
* 失败示例:
```
{
    type: "error",
    message: "fails message",
}
```
## 杠杆交易平仓
域名:  
http://www.u9coin.cn  
请求地址:  
*/api/lever/close*  
请求类型:POST  
|参数名|类型|是否必填|参与签名|说明|
|--|--|:-:|:-:|--|
|id|integer|是|--|交易id|
返回结果:json  
* 成功示例: 
```
{
    type: "ok",
    message: "success message",
}
```
* 失败示例:
```
{
    type: "error",
    message: "fails message",
}
```
## 杠杆交易信息
域名:  
http://www.u9coin.cn  
请求地址:  
*/api/lever/deal*  
请求类型:POST  
|参数名|类型|是否必填|参与签名|说明|
|--|--|:-:|:-:|--|
|currency_id|integer|是|--|交易币id|
|legal_id|integer|是|--|法币id|
返回结果:json  
* 成功示例: 
```
{
    type: "ok",
    message: {
        in: [...], //买入
        out: [...], //卖出
        multiple: [...], //允许放大倍数
        last_price: 100.00, //最新价格
        user_lever: 0.00, //我的杠杆币余额
        all_levers: 0.00, //我的杠杆交易总额
        ustd_price: 1.00, //当前法币的usdt价格
        ExRAte: 6.5, //美元况人民币汇率
        lever_transaction: [...] //最近15条杠杆交易记录
    },
}
```
* 失败示例:
```
{
    type: "error",
    message: "fails message",
}
```
## 我的杠杆交易
域名:  
http://www.u9coin.cn  
请求地址:  
*/api/lever/dealall*  
请求类型:POST  
|参数名|类型|是否必填|参与签名|说明|
|--|--|:-:|:-:|--|
|currency_id|integer|是|--|交易币id|
|legal_id|integer|是|--|法币id|
|page|integer|*否*|--|页码,不传默认第1页|
|limit|integer|*否*|--|每页显示条数,不传默认10条|
返回结果:json  
* 成功示例: 
```
{
    type: "ok",
    message: [...] //最近10条杠杆交易记录
}
```
* 失败示例:
```
{
    type: "error",
    message: "fails message",
}
```
## 设置止盈止损价
域名:  
http://www.u9coin.cn  
请求地址:  
*/api/lever/setstop*  
请求类型:POST  
|参数名|类型|是否必填|参与签名|说明|
|--|--|:-:|:-:|--|
|id|integer|是|--|交易id|
|target_profit_price|float|是|--|止盈价|
|stop_loss_price|float|是|--|止亏价|
返回结果:json  
* 成功示例: 
```
{
    type: "ok",
    message: 'success message'
}
```
* 失败示例:
```
{
    type: "error",
    message: "fails message"
}
```
## 我的交易记录
域名:  
http://www.u9coin.cn  
请求地址:  
*/api/lever/my_trade*  
请求类型:GET  
|参数名|类型|是否必填|参与签名|说明|
|--|--|:-:|:-:|--|
|page|integer|否|--|页码|
|limit|integer|否|--|每页显示条数|
|status|integer|否|--|状态:0.挂单中,1.交易中,2.平仓中,3.已平仓,4.已取消|
|currency_id|integer|否|--|交易币id|
|legal_id|integer|否|--|法币id|
返回结果:json  
* 成功示例: 
```
{
    type: "ok",
    message: [
        {
            type: 1, //买卖类型:1.买入,2.卖出
            user_id: 1, //用户id
            currency: 1, //交易id
            legal: 3, //法币id
            origin_price: 10, //原始价格
            price: 10, //开仓价格(点差处理之后)
            update_price: 8, //当前价格
            target_profit_price: 0, //止盈价格
            stop_loss_price: 0, //止亏价格
            share: 1, //手数
            number: 1, //换算后数量
            multiple: 2, //放大倍数
            origin_caution_money: 5, //初始保证金
            caution_money: 5, //当前可用保证金
            fact_profits: 0, //最终盈亏
            trade_fee: 0, //交易手续费,
            overnight: 1, //隔夜费率,百分比
            status: 0, //交易状态:0.交易中,1.平仓中,2.已平仓
            settled: 0, //结算状态:0.未结算,1.已结算
            create_time: 0, //创建时间
            update_time: 0.000000, //价格刷新时间(毫秒级)
            handle_time: 0.000000, //平仓时间(毫秒级)
            complete_time: 0.000000, //完成时间(毫秒级)
        },
        ...
    ]
}
```
* 失败示例:
```
{
    type: "error",
    message: "fails message"
}
```
## 杠杆交易一键平仓
域名:  
http://www.u9coin.cn  
请求地址:  
*/api/lever/batch_close*  
请求类型:POST  
|参数名|类型|是否必填|参与签名|说明|
|--|--|:-:|:-:|--|
|type|integer|是|--|类型:0.全部,1.买入,2.卖出|
|legal_id|integer|否|--|法币id|
|currency_id|integer|否|--|交易币id|
返回结果:json  
* 成功示例: 
```
{
    type: "ok",
    message: "success message",
}
```
* 失败示例:
```
{
    type: "error",
    message: "fails message",
}
```
## 杠杆交易取消委托(撤单)
域名:  
http://www.u9coin.cn  
请求地址:  
*/api/lever/cancel*  
请求类型:POST  
|参数名|类型|是否必填|参与签名|说明|
|--|--|:-:|:-:|--|
|id|integer|是|--|委托交易id,只有状态是挂单中的才能取消|
返回结果:json  
* 成功示例: 
```
{
    type: "ok",
    message: "success message",
}
```
* 失败示例:
```
{
    type: "error",
    message: "fails message",
}
```