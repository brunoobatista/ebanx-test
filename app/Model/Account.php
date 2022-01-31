<?php

namespace App\Model;

class Account {
    private $id;
    private $balance;

    public function __construct($id, $balance) {
        $this->id = $id;
        $this->balance = $balance;
    }

    public function setId($id) {
        $this->id = $id;
    }
    public function getId() {
        return $this->id;
    }

    public function setBalance($balance) {
        $this->balance = $balance;
    }
    public function getBalance() {
        return $this->balance;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'balance' => $this->balance
        ];
    }
}