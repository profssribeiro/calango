<?php

class Session
{

    public static function start(){
        session_start();
    }

    public static function setValue($variavel, $valor)
    {
        $_SESSION[$variavel] = $valor;
    }

    public static function getValue($variavel)
    {
        if (isset($_SESSION[$variavel]))
        {
            return $_SESSION[$variavel];
        }else{
            return false;
        }
    }

    public static function destroy()
    {
        $_SESSION = array();
        session_destroy();
    }
}

