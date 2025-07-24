<?php
namespace App\core;

use App\core\Singleton;


class Session extends Singleton{

    private function __construct(){
        if(session_status()===PHP_SESSION_NONE){
            session_start();
        }
    }

    public static function set($key, $data){
        $_SESSION[$key] = $data;

    }

    public static function get($key){
        return $_SESSION[$key]?? null;

    }

    public static function unset($key){
        unset($_SESSION[$key]);
    }

    public static function isset($key){
        return isset($_SESSION[$key]);

    }

    public static function destroy(){
        session_unset();
        session_destroy();
        static::removeInstance();

    }

   
    
}