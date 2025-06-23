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

    public function getJsonData()
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        return is_array($data) ? $data : [];
    }
}