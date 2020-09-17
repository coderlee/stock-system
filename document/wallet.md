**钱包详情**  
请求地址:*/api/wallet/detail*  
请求类型:*POST*  
请求参数
|参数|名称|是否必填|类型|说明|
|-|-|-|-|-|
|currency|币种ID|是|integer||
|type|类型|是|string|legal:法币,change:交易币,lever:合约|
返回格式：*json*  
```
{
    "type": "ok",
    "message": {
        "id": 745543,
        "currency": 1,
        "change_balance": "0.00000000",
        "lock_change_balance": "0.00000000",
        "currency_name": "TCC",
        "is_legal": 1,
        "is_lever": 1
    }
}
```