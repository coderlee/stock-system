<?php

namespace App\Http\Controllers\Api;

use App\Currency;
use App\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function lists(Request $request){
        $limit = $request->get('limit',10);
        $currency_id = $request->get('currency_id',0);
        if (empty($currency_id)){
            return $this->error('参数错误');
        }
        $currency = Currency::find($currency_id);
        if (empty($currency)){
            return $this->error('无此币种');
        }
        if (empty($currency->is_legal)){
            return $this->error('该币不是法币');
        }
        $results = Seller::where('currency_id',$currency->id)->orderBy('id','desc')->paginate($limit);
        return $this->pageDate($results);
    }
}
