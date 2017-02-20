<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 17-1-6
 * Time: 上午10:17
 */

namespace backend\models;


class Post
{
    public static function post($url, $data){
        $postdata = http_build_query(
            $data
        );

        $opts = [
            'http' =>[
                'method'  => 'POST',
                'header'  => [
                    'Content-type: application/x-www-form-urlencoded',
                    'Authorization:Bearer 876fb770a7a0a6556855af503902f4bc',
                ],
                'content' => $postdata,
            ]
        ];
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}