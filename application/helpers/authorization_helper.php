<?php

class AUTHORIZATION
{
    public static function validateTimestamp($tokenNo)
    {       
        $CI =& get_instance();
        $token = self::validateToken($tokenNo);
       
//        echo date('Y-m-d h:i:s');
//        echo date('Y-m-d h:i:s',$token->timestamp);
        
        if ($token != false && (now() - $token->timestamp < ($CI->config->item('token_timeout') * 60))) {
            $retu['payload'] = $token->payload;
            $retu['status'] = 'true';
        }else{
            $retu['status'] = 'false';
            $retu['message'] = 'Token Time out.....';
        }
        return $retu;
    }

    public static function validateToken($token)
    {
        $CI =& get_instance();
        return JWT::decode($token, $CI->config->item('jwt_key'));
    }

    public static function generateToken($data)
    {
        $CI =& get_instance();
        return JWT::encode($data, $CI->config->item('jwt_key'));
    }

}