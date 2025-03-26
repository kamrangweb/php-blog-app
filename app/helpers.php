<?php

if (! function_exists('url')) {
   
    function url(string $path = ""): string
    {
        return $path !== '/' ? ROOT_URL . $path : ROOT_URL;
    }
}

if (! function_exists('asset')) {
 
    function asset(string $path): string
    {
        return url($path);
    }
}

if (! function_exists('clear_errors')) {
  
    function clear_errors(): void
    {
        unset($_SESSION['errors']);
    }
}

if (! function_exists('clear_session_msg')) {

    function clear_session_msg(): void
    {
        unset($_SESSION['msg']);
    }
}