<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class MmRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/outgoing-transfer-notes/{branchId}/:sync';
    protected $table = 'tema_mm_document';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $stocks = $this->getStocks();
        $stocksCount = count($stocks);
        $resultCount = 0;
        $items = [];

        if ($stocksCount) {
            $i = 1;
            foreach ($stocks as $stock) {
                echo "\nNr stocku $stock ----> $i/$stocksCount";
                $i++;

                $res = [];
                $url = str_replace('{branchId}', $stock, $this->endpoint);
                do {
                    $nextTimestamp = '';
                    if (!empty($res['lastTimestamp']))
                        $nextTimestamp = '?timestamp=' . urlencode($res['lastTimestamp']);

                    $res = $this->fetchApiResult($url . $nextTimestamp);
                    if (empty($res))
                        continue;

                    $this->collectItems($res['items'], $items);
                    $this->fetchResult = array_merge($this->fetchResult, $res['items']);
                    $resultCount += count($res['items']);

                    if (count($this->fetchResult) >= $this->fetchLimit) {
                        $this->save();
                        $this->fetchResult = [];
                        $this->relatedRepositories['items']->saveItems($items);
                        $items = [];

                        gc_collect_cycles();
                    }

                } while ($res['fetchNext']);

                $this->save();
                $this->fetchResult = [];
                $this->relatedRepositories['items']->saveItems($items);
                unset($items);

                gc_collect_cycles();
            }
        } else 
            throw new \Exception("Nie żadnych jednostek organizacyjnych. Najpierw uruchom komendę pobierającą listę jednostek [tema:stock]");
        
        return [
            'fetched' => $resultCount,
        ];
    }

    private function getStocks()
    {
        $q = "SELECT stock_id FROM tema_stock WHERE category = 'workshop' AND source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    private function collectItems(array &$res, array &$items)
    {
        foreach ($res as $i => $r) {
            foreach ($r['items'] as $item) {
                $item['doc_id'] = $r['id'];
                array_push($items, $item);
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
    }
}
