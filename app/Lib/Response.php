<?php

namespace App\Lib;

class Response
{
    private $status = 200;

    public function status(int $code)
    {
        $this->status = $code;
        return $this;
    }

    public function noContent() {
        header('Content-Type: application/json');
        echo 'OK';
    }
    
    public function toJSON($data = []) {
        http_response_code($this->status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}