<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ftp_upload extends Controller
{
    public static function ftp_upload_cdn_one($file , $newFileName){
        $errors = [];

        $ftpUpload = [
            "ftp_username"  => "pz12573" ,
            "ftp_server"    => "130.185.79.126" ,
            "port"          => "21" ,
            "password"      => "RM3PGjkj"
        ];

        // connect and login to FTP server
        $ftp_conn = ftp_connect($ftpUpload["ftp_server"])
            or
        $errors["upload"][] = "couldn't connect to CDN server.";
        $login = ftp_login($ftp_conn, $ftpUpload["ftp_username"], $ftpUpload["password"]);

        // upload file
        ftp_mkdir($ftp_conn , "domains/pz12573.parspack.net/public_html/".$newFileName);
        if (
            ftp_put(
                $ftp_conn,
                "domains/pz12573.parspack.net/public_html/".$newFileName . "/" . $file,
                base_path() ."/private/".$newFileName."/".$file,
                FTP_ASCII
            )
        ){
            /**
             *
             * FILE UPLOADED SUCCESSFULLY
             *
             */
            return true;
        } else {
            /**
             *
             * IF UPLOAD WASN'T SUCCESSFULLY
             *
             */
            $errors["upload"][] = "upload wasn't successfully.";
        }

        /**
         *
         * SHOW ERRORS IF THERE ARE
         *
         */
        if(
            count($errors) >= 1
        ){
            /**
             *
             * ERROR
             *
             */
//            return response()->json([
//                "massage" => "upload wasn't successfully" ,
//                "errors" => $errors
//            ])->setStatusCode("401");
            return false;

        }


        // close connection
        ftp_close($ftp_conn);

    }




}
