<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use DB;
class ApiController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->nullClass = new \stdClass;
        $this->source = 4;
        $request = new Request();

        $time = Request::input('time');
        $sig = Request::input('sig');
        $token = Request::input('token');
        $action = Route::getCurrentRoute()->getActionName();
        if (!strpos($action, 'postAppDownload') and $sig != md5('sc%7*g' . $time . '@!$%')) {
            echo json_encode(['status' => -1, 'message' => '验证失败']);
            exit();
        }


//        if(!strpos($action, 'postUserLogin') && !strpos($action, 'postRegister') && !strpos($action, 'postVisitRegister')){
//
//
//            if(empty($token)){
//                echo json_encode(['status' => -2, 'message' => '无效token']);
//                exit();
//            }else{
//                $uuid = $this->parseToken($token);
//                $res = DB::table('novel_user')->where('id',$uuid)->first();
//                if(!empty($res->phone)){
//                    $lastlogin = $res->logintime;
//                    if(time()-$lastlogin>60){
//                        echo json_encode(['status' => -2, 'message' => 'token失效']);
//                        exit();
//                    }else{
//                        $data=array(
//                            'logintime'=>time()
//                        );
//                        DB::table('novel_user')->where('id',$uuid)->update($data);
//                    }
//                }else{
//                    $data=array(
//                        'logintime'=>time()
//                    );
//                    DB::table('novel_user')->where('id',$uuid)->update($data);
//                }
//
//
//
//            }
//        }




    }

    public function token($id)
    {
        return $this->encrypt(json_encode(['time' => time(), 'id' => $id]), 'E');
    }

    public function parseToken($str)
    {
        $arr = $this->encrypt($str, 'D');
        $arr = json_decode($arr, true);
        return $arr['id'];
    }

    /*
     * $operation：判断是加密还是解密，E表示加密，D表示解密
     */
    function encrypt($string, $operation, $key = '#ED@!')
    {
        $key = md5($key);
        $key_length = strlen($key);
        $string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);
        $rndkey = $box = array();
        $result = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'D') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }


    public static function sendData($status,$message,$data){
        return json_encode([
            'status'=>$status,
            'message'=>$message,
            'data'=>$data,
            ]
        );
    }
}
