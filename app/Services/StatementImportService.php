<?php

namespace App\Services;

use App\Models\{TransactionType, Client, AccountName, Statement};
use App\Helpers\CsvHelper;
use App\Helpers\StatementHelper;


class StatementImportService
{
    public static function import($file)
    {
        $rows = CsvHelper::parse($file);
        
        foreach (array_reverse($rows) as $row) {

            $row = StatementHelper::normalize($row);

            $typeId = TransactionType::getOrCreate($row['transaction type']);
            $clientId = Client::getOrCreate($row['client team']);
            $accountId = AccountName::getOrCreate($row['account name']);

            $billingDate = StatementHelper::calculateBillingDate(
                $row['date'],
                $typeId
            );

            Statement::saveStatement(
                $row,
                $typeId,
                $clientId,
                $accountId,
                $billingDate
            );
        }
    }
}