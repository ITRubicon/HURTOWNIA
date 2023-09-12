<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class WarehouseRepository extends IApiRepository
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

                $this->fetchResult = $res['items'];
                $this->save();
                $this->fetchResult = [];
                $resCount += count($res['items']);
            } while ($res['fetchNext']);
        }

        return [
            'fetched' => $resCount,
        ];
    }

    private function getStocks()
    {
        $q = "SELECT stock_id FROM tema_stock WHERE name LIKE '%części%' AND source = :source";
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
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
