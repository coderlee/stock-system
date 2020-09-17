<?php

namespace App\Http\Middleware;

use App\Users;
use Closure;
use App\UserReal;
class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_id = Users::getUserId();
        $user_real = UserReal::where('user_id',$user_id)->first();
        if(empty($user_real)){
            return response()->json(['type' => '998', 'message' => '请实名认证']);
        }
        if ($user_real->review_status != 2){

            return response()->json(['type' => 'error', 'message' => '您的实名认证还未通过！']);
        }
        return $next($request);
    }
}
