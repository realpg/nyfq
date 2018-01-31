<?php

namespace App\Http\Controllers\admin;

use App\Components\OrderManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ordersController extends Controller
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
        $orders = OrderManager::getAllOrders($data);
//        $admin = $request->session()->get('admin');
//        if(!array_key_exists('search',$data)){
//            $data['search']="";
//        }
//        $comments = OrderManager::get($data);
//        $param=array(
//            'admin'=>$admin,
//            'datas'=>$comments
//        );
        return $orders;
    }
}
