<?php
namespace App\Controllers;

use App\Lib\Request;
use App\Lib\Response;
use App\Services\BalanceService;

class EventController {
    private $balanceService;

    public function __construct() {
        $this->balanceService = new BalanceService();
    }

    public function managerAccount(Request $req, Response $res) {
        $result = $this->balanceService->executeOperation($req->params);

        if (!$result) {
            $res->status(404)->toJSON(0);
        } else {
            $res->status(201)->toJSON($result);
        }
    }
    
}