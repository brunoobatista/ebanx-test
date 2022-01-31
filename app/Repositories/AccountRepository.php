<?php

namespace App\Repositories;

use App\Model\Account;

include __DIR__ . '/../../store/dataset.php';

class AccountRepository {
    private $dataset;

    public function __construct() {
        $this->dataset = $this->loadData();
    }

    public function getBalanceByAccountId($account_id) {
        $account = $this->getAccountById($account_id);
        if ($account)
            return $account['balance'];
        return false;
    }

    public function getAccountById($account_id) {
        $accounts = $this->dataset;
        $acc = false;
        foreach ($accounts as $account) {
            if ($account['id'] === $account_id) {
                $acc = $account;
                break;
            }
        }
        return $acc;
    }

    public function getAll() {
        return $this->dataset;
    }

    public function save($accounts) {
        $this->saveData($accounts);
    }

    public function depositAmount(Account $account) {
        $accounts = $this->dataset;

        $found = false;
        if (count($accounts) > 0) {
            foreach ($accounts as $key => $value) {
                if ($value['id'] === $account->getId()) {
                    $newBalance = $account->getBalance() + $value['balance'];
                    $account->setBalance($newBalance);
                    $accounts[$key] = $account->toArray();
                    $found = true;
                    break;
                }
            }
        } else {
            $found = true;
            $accounts[] = $account->toArray();
        }

        if ($found) {
            $this->saveData($accounts);
            return [
                "destination" => $account->toArray()
            ];
        }
        return false;

    }

    private function loadData() {
        $path = __DIR__ . '/../../store/db.json';
        return json_decode(file_get_contents($path), true);
    }
    
    private function saveData($dataset) {
        $path = __DIR__ . '/../../store/db.json';
        $this->dataset = $dataset;
        file_put_contents($path, json_encode($dataset));
    }
}