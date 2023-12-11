<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class MmRepository extends IApiRepository
{
    // private string $endpoint = '/api/dms/v1/outgoing-transfer-notes/{branchId}?creationDateFrom={dateFrom}&creationDateTo={dateTo}';
    private string $endpoint = '/api/dms/v1/outgoing-transfer-notes/{branchId}';
    protected $table = 'tema_mm_document';
    private $items = [];

    public function fetch(): array
    {
        $this->clearDataArrays();
        $stocks = $this->getStocks();
        $stocksCount = count($stocks);
        $resultCount = 0;

        if ($stocksCount) {
            echo "\nPobieranie endpointów dla oddziałów";
            $i = 1;
            foreach ($stocks as $stock) {
                echo "\nNr stocku $stock ----> $i/$stocksCount";
                $i++;

                $url = str_replace(
                    ['{branchId}', /* '{dateFrom}', '{dateTo}' */],
                    [$stock, /* $this->dateFrom, $this->dateTo */],
                    $this->endpoint
                );
                $this->fetchResult = $this->fetchApiResult($url);
                
                if (empty($this->fetchResult))
                    continue;

                $this->collectItems($this->fetchResult);
                $resultCount += count($this->fetchResult);
                $this->save();
            }
        } else 
            throw new \Exception("Nie żadnych jednostek organizacyjnych. Najpierw uruchom komendę pobierającą listę jednostek [tema:stock]");
        
        return [
            'fetched' => $resultCount,
            'items' => $this->items
        ];
    }

    private function getStocks()
    {
        $q = "SELECT stock_id FROM tema_stock WHERE name LIKE '%części%' AND source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    private function collectItems(array &$res)
    {
        foreach ($res as $i => $r) {
            foreach ($r['items'] as $item) {
                $item['doc_id'] = $r['id'];
                array_push($this->items, $item);
            }
            unset($res[$i]['items']);
        }
    }

    protected function getFieldsParams(): array
    {
        return [
            'row_id' => ['sourceField' => 'rowId', 'type' => ParameterType::INTEGER],
            'doc_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'customer_id' => ['sourceField' => 'customerId', 'type' => ParameterType::STRING],
            'issue_date' => ['sourceField' => 'issueDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'due_date' => ['sourceField' => 'dueDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'gross_value' => ['sourceField' => 'grossValue', 'type' => ParameterType::STRING],
            'currency' => ['sourceField' => 'currency', 'type' => ParameterType::STRING],
            'order_id' => ['sourceField' => 'orderId', 'type' => ParameterType::STRING],
            'order_name' => ['sourceField' => 'orderName', 'type' => ParameterType::STRING],
            'payment_method' => ['sourceField' => 'paymentMethod', 'type' => ParameterType::STRING],
            'operator_code' => ['sourceField' => 'operatorCode', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'notes' => ['sourceField' => 'notes', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->items = [];
    }
}
