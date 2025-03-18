<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CarOrderRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/vehicle-orders/{branchId}/:sync';
    protected $table = 'tema_car_order';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $stocks = $this->getStocks();
        $stocksCount = count($stocks);
        $resultCount = 0;
        $res = [];
        $orderItems = [];

        if ($stocksCount) {
            $i = 1;
            foreach ($stocks as $stock) {
                echo "\nNr stocku $stock ----> $i/$stocksCount";
                $i++;

                $url = str_replace('{branchId}', $stock, $this->endpoint);
                do {
                    $nextTimestamp = '';
                    if (!empty($res['lastTimestamp']))
                        $nextTimestamp = '?timestamp=' . urlencode($res['lastTimestamp']);

                    $res = $this->fetchApiResult($url . $nextTimestamp);
                    if (empty($res))
                        continue;

                    $this->collectItems($res['items'], $orderItems);
                    $this->fetchResult = array_merge($this->fetchResult, $res['items']);
                    $resultCount += count($res['items']);

                    if (count($this->fetchResult) >= $this->fetchLimit) {
                        $this->save();
                        $this->fetchResult = [];
                        $this->relatedRepositories['items']->saveItems($orderItems);
                        $orderItems = [];

                        gc_collect_cycles();
                    }
                } while ($res['fetchNext']);
            }
            $this->save();
            $this->fetchResult = [];
            $this->relatedRepositories['items']->saveItems($orderItems);
            unset($orderItems);

            gc_collect_cycles();
        } else 
            throw new \Exception("Nie żadnych jednostek organizacyjnych. Najpierw uruchom komendę pobierającą listę jednostek [tema:stock]");

        return [
            'fetched' => $resultCount,
        ];
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'creation_date' => ['sourceField' => 'creationDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'closing_date' => ['sourceField' => 'closingDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'is_canceled' => ['sourceField' => 'isCanceled', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'customer_id' => ['sourceField' => 'customerId', 'type' => ParameterType::STRING],
            'payer_id' => ['sourceField' => 'payerId', 'type' => ParameterType::STRING],
            'vehicle_id' => ['sourceField' => 'vehicleId', 'type' => ParameterType::INTEGER],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'seller_id' => ['sourceField' => 'sellerId', 'type' => ParameterType::STRING],
            'notes' => ['sourceField' => 'notes', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    private function collectItems(array &$res, array &$orderItems)
    {
        foreach ($res as $i => $r) {
            foreach ($r['items'] as $item) {
                $item['doc_id'] = $r['id'];
                array_push($orderItems, $item);
            }
            unset($res[$i]['items']);
        }
    }

    private function getStocks()
    {
        $q = "SELECT stock_id FROM tema_stock WHERE source = :source AND category LIKE '%Vehicles%'";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }
}
