<?php

namespace App\Http\Router;

use App\Exceptions\NotFoundException;


class Router
{
 
    private $url;
    private $routes;

    public function __construct(string $url)
    {
        $this->url = trim($url, '/');
    }

    public function get(string $path, string $action)
    {
        $this->routes['GET'][] = new Route($path, $action);
    }


    public function post(string $path, string $action)
    {
        $this->routes['POST'][] = new Route($path, $action);
    }

    public function run()
    {
        $superGlobals = [
            'GET' => $_GET,
            'POST' => $_POST
        ];

        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->matches($this->url)) {
                return $route->execute($superGlobals);
            }
        }

        throw new NotFoundException('404 - Page not found');
    }
}