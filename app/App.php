<?php
namespace App;
use App\Lib\Router;

class App {
    public static function run() {

        require_once __DIR__ . '/../routes/api.php';

    }
}