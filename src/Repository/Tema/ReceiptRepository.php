<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ReceiptRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/receipts/{branchId}/:sync';
    protected $table = 'tema_receipt';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $stocks = $this->getStocks();
        $resCount = 0;
        $receiptItems = [];

        foreach ($stocks as $stock) {
            $endpoint = str_replace('{branchId}', $stock, $this->endpoint);

            $res = [];
            do {
                $nextTimestamp = '';
                if (!empty($res['lastTimestamp']))
                    $nextTimestamp = '?timestamp=' . urlencode($res['lastTimestamp']);

                $res = $this->fetchApiResult($endpoint . $nextTimestamp);
                if (empty($res['items']))
                    continue;
                
                $this->collectItems($res['items'], $receiptItems);
                $this->fetchResult = array_merge($this->fetchResult, $res['items']);
                $resCount += count($res['items']);

                if (count($this->fetchResult) >= $this->fetchLimit) {
                    $this->save();
                    $this->fetchResult = [];
                    $this->relatedRepositories['items']->saveItems($receiptItems);
                    $receiptItems = [];

                    
                }

            } while ($res['fetchNext']);
            
            $this->save();
            $this->fetchResult = [];

            $this->relatedRepositories['items']->saveItems($receiptItems);
            $receiptItems = [];
            
            
        }

        return ['fetched' => $resCount];
    }

    private function collectItems(array &$doc, array &$receiptItems)
    {
        foreach ($doc as $d) {
            foreach ($d['items'] as $item) {
                $item['doc_id'] = $d['id'];
                $item['unit'] = $item['unit']['name'] ?? '';
                array_push($receiptItems, $item);
            }
            unset($d['items']);
        }
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
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
            'branch' => ['sourceField' => 'branch', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    private function getStocks()
    {
        $q = "SELECT stock_id FROM tema_stock WHERE source = :source AND category = 'workshop'";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
    }
}
