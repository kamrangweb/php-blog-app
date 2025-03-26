<?php

namespace App\Http;


class Request
{

    public $get;


    public $post;


    public function __construct(array $superGlobals)
    {
        $this->get = $superGlobals['GET'] ?? [];
        $this->post = $superGlobals['POST'] ?? [];
    }
}