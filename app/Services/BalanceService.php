<?php

namespace App\Services;

use App\Model\Account;
use App\Repositories\AccountRepository;

class BalanceService {

    private const TYPE_DEPOSIT = 'deposit';
    private const TYPE_WITHDRAW = 'withdraw';
    private const TYPE_TRANSFER = 'transfer';
    
    private const LABEL_DESTINATION = 'destination';
    private const LABEL_ORIGIN = 'origin';
    private $accountRepository;

    public function __construct() {
        $this->accountRepository = new AccountRepository();
    }

    public function executeOperation($params) {
        switch($params['type']) {
            case self::TYPE_DEPOSIT:
                $account = new Account($params[self::LABEL_DESTINATION], $params['amount']);
                $result = $this->addOrUpdateAccounts($account, self::TYPE_DEPOSIT);
                $resultDestination = [self::LABEL_DESTINATION => $result];
                return $resultDestination;
            case self::TYPE_WITHDRAW:
                $account = new Account($params[self::LABEL_ORIGIN], $params['amount']);
                $result = $this->addOrUpdateAccounts($account, self::TYPE_WITHDRAW);
                if (!$result) return false;
                $resultOrigin = [self::LABEL_ORIGIN => $result];
                return $resultOrigin;
            case self::TYPE_TRANSFER:
                $accountOrigin = new Account($params[self::LABEL_ORIGIN], $params['amount']);
                $resultOrigin = $this->addOrUpdateAccounts($accountOrigin, self::TYPE_WITHDRAW);
                if (!$resultOrigin) return false;

                $accountDestination = new Account($params[self::LABEL_DESTINATION], $params['amount']);
                $resultDestination = $this->addOrUpdateAccounts($accountDestination, self::TYPE_DEPOSIT, true);
                if (!$resultDestination) return false;

                $resultTransfer = [self::LABEL_ORIGIN => $resultOrigin, self::LABEL_DESTINATION => $resultDestination];
                return $resultTransfer;
            default:
                var_dump('DEU TUDO ERRADO');
        }
    }

    private function addOrUpdateAccounts(Account $account, $type, $transfer = false) {
        $accounts = $this->accountRepository->getAll();
        $found = false;
        if (count($accounts) > 0) {
            foreach ($accounts as $key => $value) {
                if ($value['id'] === $account->getId()) {
                    if ($type === self::TYPE_DEPOSIT)
                        $newBalance = $value["balance"] + $account->getBalance();
                    else if ($type === self::TYPE_WITHDRAW) 
                        $newBalance = $value["balance"] - $account->getBalance();

                    $account->setBalance($newBalance);
                    $accounts[$key] = $account->toArray();
                    $found = true;
                    break;
                }
            }

            if (!$found && $transfer) {
                $found = true;
                $accounts[] = $account->toArray();
            }
        } else {
            $found = true;
            $accounts[] = $account->toArray();
        }

        if ($found) {
            $this->accountRepository->save($accounts);
            return $account->toArray();
        }
        return false;
    }
}