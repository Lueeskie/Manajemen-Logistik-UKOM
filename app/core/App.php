<?php

class App
{
    protected $controller = 'HomeController'; // default controller
    protected $method     = 'index';          // default method
    protected $params     = [];               // default parameters

    public function __construct()
    {
        $url = $this->parseUrl();

        // 1. Ngecek CONTROLLER
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';

            if (file_exists(__DIR__ . '/../controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        // Load controller file
        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // 2. Ngechek METHOD
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 3. Ngecek PARAMETERS
        $this->params = $url ? array_values($url) : [];

        // 4. Jalanin CONTROLLER + METHOD + PARAMETER
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode(
                '/',
                filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)
            );
        }

        return [];
    }
}
