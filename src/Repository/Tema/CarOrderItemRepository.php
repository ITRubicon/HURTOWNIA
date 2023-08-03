<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CarOrderItemRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/vehicle-orders/{branchId}/{orderId}';
    protected $table = 'tema_car_order';
    private $orderEndpoints = [];
    private $orderItems = [];

    public function saveItems(array $items)
    {
        $this->clearDataArrays();
        $this->fetchResult = $items;
        $this->save();
        $this->clearDataArrays();
    }

    public function fetch(): array
    {
        $this->clearDataArrays();
        $this->getDocumentEndpointList();
        dd($this->orderEndpoints);
        $listCount = count($this->orderEndpoints);

        if ($listCount) {
            echo "\nPobieram zapisy dokumentów";

            $i = 1;
            foreach ($this->orderEndpoints as $de) {
                echo "\nEndpoint ----> $i/$listCount";
                $doc = $this->fetchApiResult($de['getUrl']);
                if (empty($doc))
                    continue;
                    
                array_push($this->fetchResult, $doc);
                
                $this->collectItems($doc);
                $i++;
            }
        }
        return [
            'fetched' => count($this->fetchResult),
            'items' => $this->orderItems
        ];
    }

    private function getDocumentEndpointList()
    {
        $stocks = $this->getStocks();
        $stocksCount = count($stocks);

        if ($stocksCount) {
            echo "\nPobieranie endpointów dla oddziałów";
            $i = 1;

            foreach ($stocks as $stock) {
                echo "\nNr stocku $stock ----> $i/$stocksCount";

                $url = str_replace('{branchId}', $stock, $this->endpoint);
                $res = $this->fetchApiResult($url);
                if (empty($res))
                    continue;

                $this->orderEndpoints = array_merge($this->orderEndpoints, $res);

                $i++;
            }
        }
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'customer_id' => ['sourceField' => 'customerId', 'type' => ParameterType::STRING],
            'issue_date' => ['sourceField' => 'issueDate', 'type' => ParameterType::STRING],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'gross_value' => ['sourceField' => 'grossValue', 'type' => ParameterType::STRING],
            'notes' => ['sourceField' => 'notes', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    private function collectItems(array $doc)
    {
        foreach ($doc['items'] as $item) {
            $item['doc_id'] = $doc['id'];
            array_push($this->orderItems, $item);
        }
    }

    private function getStocks()
    {
        
        $q = "SELECT stock_id FROM tema_stock WHERE source = :source AND category LIKE '%Vehicles%'";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->orderEndpoints = [];
        $this->orderItems = [];
    }
}
