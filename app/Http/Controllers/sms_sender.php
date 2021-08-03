<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class sms_sender extends Controller
{

    public static function send_message ($receptor , $message){
        $token = '382B3459656A50522F6E4C7944554D41345145426C47564F74596B387979546A396B79466C6866377645493D';
        $sendMessage = curl_init();
                       curl_setopt($sendMessage , CURLOPT_URL , 'https://api.kavenegar.com/v1/'.$token.'/verify/lookup.json');
                       curl_setopt($sendMessage , CURLOPT_POST, true);
                       curl_setopt($sendMessage , CURLOPT_POSTFIELDS , [
                           'token' => $message ,
                           'receptor' => $receptor,
                           'sender' => '10002000202010' ,
                           'template' => 'verify'
                       ]);
                       curl_setopt($sendMessage , CURLOPT_RETURNTRANSFER , true);
                       curl_exec($sendMessage);
                       curl_close($sendMessage);
//$token = '382B3459656A50522F6E4C7944554D41345145426C47564F74596B387979546A396B79466C6866377645493D';
//        $sendMessage = curl_init();
//                       curl_setopt($sendMessage , CURLOPT_URL , 'https://api.kavenegar.com/v1/'.$token.'/sms/send.json');
//                       curl_setopt($sendMessage , CURLOPT_POST, true);
//                       curl_setopt($sendMessage , CURLOPT_POSTFIELDS , [
//                           'message' => $message ,
//                           'receptor' => $receptor
//                       ]);
//                       curl_setopt($sendMessage , CURLOPT_RETURNTRANSFER , true);
//                       curl_exec($sendMessage);
//                       curl_close($sendMessage);

        /*
        $sendMessage = curl_init();
        curl_setopt($sendMessage , CURLOPT_URL , 'https://rest.payamak-panel.com/api/SendSMS/SendSMS');
        curl_setopt($sendMessage , CURLOPT_POST, true);
        curl_setopt($sendMessage , CURLOPT_HTTPHEADER , [
            'Content-Type: Application/xml'
        ]);
        curl_setopt($sendMessage , CURLOPT_POSTFIELDS , [
            'text'     => $message ,
            'to'       => $receptor ,
            'password' => '57mfhg4' ,
            'username' => '09129421466' ,
            'from' => '50001060315584'
        ]);
//        curl_setopt($sendMessage , CURLOPT_RETURNTRANSFER , true);
        curl_exec($sendMessage);
        curl_close($sendMessage);*/

    }

}
