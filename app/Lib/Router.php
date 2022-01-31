<?php
namespace App\Lib;

class Router {
    
    private $handlers = [];
    private $notFoundHandler;
    private const METHOD_POST = 'POST';
    private const METHOD_GET = 'GET';

    public function get($path, $handler) {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], self::METHOD_GET) !== 0) {
            return;
        }
        $this->addHandler(self::METHOD_GET, $path, $handler);
    }

    public function post($path, $handler) {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], self::METHOD_POST) !== 0) {
            return;
        }

        $this->addHandler(self::METHOD_POST, $path, $handler);
    }

    private function addHandler($method, $path, $handler) {
        $this->handlers[$method.$path] = [
            'path'      => $path,
            'method'    => $method,
            'handler'   => $handler
        ];
    }

    public function addNotFoundHandler($handler) {
        $this->notFoundHandler = $handler;
    }

    public function run() {
        $requestUri = parse_url($_SERVER['REQUEST_URI']);
        $requestPath = $requestUri['path'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $handler = $this->handlers[$requestMethod.$requestPath];
        if ($handler) {
            $callback = $handler['handler'];

            if (is_string($callback)) {
                $parts = explode('::', $callback);
                $class = array_shift($parts);
                $obj = new $class;
                $method = array_shift($parts);

                $callback = [$obj, $method];
            }

            if ($requestMethod === self::METHOD_POST) {
                $contentType = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
                if (strcasecmp($contentType, 'application/json') !== 0) {
                    $this->returnNotFound();
                }
                $content = trim(file_get_contents("php://input"));
                $params = json_decode($content, true);
            } else if ($requestMethod === self::METHOD_GET) {
                $querys = explode('&',$requestUri['query']);
                $params = [];
                foreach ($querys as $value) {
                    $arrAux = explode('=', $value);
                    $params = array_merge($params, [$arrAux[0] => $arrAux[1]]);
                }
            } else {
                $this->returnNotFound();
            }

            // $params = $_SERVER['REQUEST_URI'];
            // var_dump($params, '@@@');
            // $params = (stripos($params, "/") !== 0) ? "/" . $params : $params;
            // var_dump($params);
            // $regex = str_replace('/', '\/', $handler['path']);
            // $is_match = preg_match('/^' . ($regex) . '$/', $params, $match, PREG_OFFSET_CAPTURE);
            // if ($is_match) {
            //     array_shift($match);
            //     $params = array_map(function ($param) {
            //         return $param[0];
            //     }, $match);
            // }
        } else {
            $this->returnNotFound();
        }
        return $this->executeCallback($callback, $params);
    }

    private function executeCallback($callback, $params = []) {
        return call_user_func_array($callback, [
            new Request($params), new Response()
        ]);
    }

    private function returnNotFound() {
        header('HTTP/1.0 404 Not Found');
        if (!empty($this->notFoundHandler)) {
            $callback = $this->notFoundHandler;
        }
        $params=[];
        return $this->executeCallback($callback, $params);
    }
}