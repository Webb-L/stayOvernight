<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        $title = "首页";
        return view('index', ['title' => $title]);
    }

    public function image(Request $request)
    {
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $allow_origin = [
            config('app.url'),
        ];
        if (in_array($origin, $allow_origin)) {
            $request->header('Access-Control-Allow-Origin', $origin);
            $request->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
            $request->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
            $request->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
            $request->header('Access-Control-Allow-Credentials', 'true');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        echo curl_exec($ch);
        curl_close($ch);
    }
}
