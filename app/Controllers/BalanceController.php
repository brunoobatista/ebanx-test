<?php

namespace App\Controllers;

use App\Lib\Request;
use App\Lib\Response;
use App\Repositories\AccountRepository;
use App\Repositories\BalanceRepository;

class BalanceController {

    private $accountRepository;

    public function __construct() {
        $this->accountRepository = new AccountRepository();
    }

    public function getById(Request $req, Response $res) {
        $balance = $this->accountRepository->getBalanceByAccountId($req->params['account_id']);

        if (!$balance) {
            $res->status(404)->toJSON(0);
        } else {
            $res->status(200)->toJSON($balance);
        }
    }
}