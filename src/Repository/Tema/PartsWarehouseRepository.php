<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class PartsWarehouseRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/warehouse/{stockId}/:sync';
    protected $table = 'tema_warehouse';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $resCount = 0;
        $res = [];
        $stocks = $this->getStocks();

        foreach ($stocks as $stock) {
            $url = str_replace('{stockId}', $stock, $this->endpoint);
            do {
                $nextTimestamp = '';
                if (!empty($res['lastTimestamp']))
                    $nextTimestamp = '?timestamp=' . urlencode($res['lastTimestamp']);

                $res = $this->fetchApiResult($url . $nextTimestamp);
                if (empty($res))
                    continue;

                $this->fetchResult = array_merge($this->fetchResult, $res['items']);
                $resCount += count($res['items']);
            } while ($res['fetchNext']);
            
            $this->addStockId($stock);
            $this->save();
            $this->fetchResult = [];
        }

        return [
            'fetched' => $resCount,
        ];
    }

    private function addStockId($stock)
    {
        $itemsCount = count($this->fetchResult);
        for ($i=0; $i < $itemsCount; $i++) { 
            $this->fetchResult[$i]['stock_id'] = $stock;
        }
    }

    private function getStocks()
    {
        $q = "SELECT stock_id FROM tema_stock WHERE name LIKE '%Magazyn%' AND source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'row_id' => ['sourceField' => 'rowId', 'type' => ParameterType::INTEGER],
            'tema_code' => ['sourceField' => 'temaCode', 'type' => ParameterType::INTEGER],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'quantity' => ['sourceField' => 'quantity', 'type' => ParameterType::STRING],
            'admission_date' => ['sourceField' => 'admissionDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'value' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'delivery_note' => ['sourceField' => 'deliveryNote', 'type' => ParameterType::STRING],
            'stock_id' => ['sourceField' => 'stock_id', 'type' => ParameterType::INTEGER],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
