<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CarOrderItemRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/vehicle-orders/{branchId}/{orderId}';
    protected $table = 'tema_car_order_item';

    public function saveItems(array $items): int
    {
        $this->clearDataArrays();
        
        foreach ($items as $item) {
            $tempItem = array_merge($item, $item['taxRate']);
            unset($tempItem['taxRate']);
            array_push($this->fetchResult, $tempItem);
        }
        $this->save();
        $resCount = count($this->fetchResult);
        $this->clearDataArrays();

        return $resCount;
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'net_price' => ['sourceField' => 'netPrice', 'type' => ParameterType::STRING],
            'gross_price' => ['sourceField' => 'grossPrice', 'type' => ParameterType::STRING],
            'tax_rate' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'is_exempt' => ['sourceField' => 'isExempt', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
