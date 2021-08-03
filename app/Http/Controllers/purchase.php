<?php
namespace App\Http\Controllers;

class purchase extends Controller {
    public static function create_school_invoice ($order_id , $amount , $name , $phone ){
        $newInvoice = curl_init();
                      curl_setopt($newInvoice , CURLOPT_URL , 'https://api.idpay.ir/v1.1/payment');
                      curl_setopt($newInvoice , CURLOPT_POST , true);
                      curl_setopt($newInvoice , CURLOPT_HTTPHEADER , [
                          'X-API-KEY: da94d654-801b-497a-985c-e385430613ee' ,
                          'Content-Type: application/json'
                      ]);

                      curl_setopt($newInvoice , CURLOPT_POSTFIELDS , json_encode([
                          'order_id' => $order_id ,
                          'amount' =>$amount ,
                          'name' => $name ,
                          'phone' => $phone ,
                          'callback' => 'https://www.khiyal.art/verify'
                      ]));
                      curl_exec($newInvoice);
                      $newInvoice = $newInvoice;
//                      return $newInvoice;
                      var_dump($newInvoice);



    }
}
