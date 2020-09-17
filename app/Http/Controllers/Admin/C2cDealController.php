<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\C2cDeal;
use App\Currency;

class C2cDealController extends Controller
{
    public function index()
    {

        // $currency = Currency::where('is_legal', 1)->orderBy('id', 'desc')->get();//获取法币
        //return view('admin.legal.deal', ['currency' => $currency]);
        return view('admin.c2c.deal');
    }

    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $account_number = $request->get('account_number', '');
        $seller_number = $request->get('seller_number', '');
        $type = $request->get('type', '');
        // $currency_id = $request->get('currency_id', 0);
        $result = new C2cDeal();
        if (!empty($account_number)) {
            $result = $result->whereHas('user', function ($query) use ($account_number) {
                $query->where('account_number', 'like', '%' . $account_number . '%');
            });
        }
        if (!empty($seller_number)) {

            $result = $result->whereHas('seller', function ($query) use ($seller_number) {
                $query->where('account_number', 'like', '%' . $seller_number . '%');
            });
        }

        if (!empty($type)) {
            $result = $result->whereHas('legalDealSend', function ($query) use ($type) {
                $query->where('type', $type);
            });

        }
        // if (!empty($currency_id)) {
        //     $result = $result->whereHas('legalDealSend', function ($query) use ($currency_id) {
        //         $query->where('currency_id', $currency_id);
        //     });
        // }

        $result = $result->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($result);
    }

}