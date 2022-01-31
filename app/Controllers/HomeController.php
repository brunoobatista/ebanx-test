<?php
namespace App\Controllers;

use App\Lib\Request;
use App\Lib\Response;

class HomeController {
    public function index(Request $req, Response $res) {
        $res->toJSON(['teste' => 'Teste de index']);
    }
}