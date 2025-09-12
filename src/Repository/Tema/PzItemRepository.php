<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class PzItemRepository extends IApiRepository
{
    protected $onDuplicateClause = 'ON DUPLICATE KEY UPDATE
        product_id = VALUES(product_id),
        name = VALUES(name),
        quantity = VALUES(quantity),
        net_price = VALUES(net_price),
        tax_rate = VALUES(tax_rate),
        is_exempt = VALUES(is_exempt)
    ';
    private string $endpoint = '/api/dms/v1/grns/{branchId}/{gdnId}';
    protected $table = 'tema_pz_item';

    public function saveItems(array $items): int
    {
        $this->clearDataArrays();
        
        foreach ($items as $item) {
            $item = array_merge($item, $item['taxRate']);
            unset($item['taxRate']);
            array_push($this->fetchResult, $item);
        }
        unset($items);
        $this->save();
        $resCount = count($this->fetchResult);
        $this->clearDataArrays();

        gc_collect_cycles();

        return $resCount;
    }

    protected function getFieldsParams(): array
    {
        return [
            'pz_id' => ['sourceField' => 'pz_id', 'type' => ParameterType::STRING],
            'product_id' => ['sourceField' => 'productId', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'quantity' => ['sourceField' => 'quantity', 'type' => ParameterType::STRING],
            'net_price' => ['sourceField' => 'netPrice', 'type' => ParameterType::STRING],
            'tax_rate' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'is_exempt' => ['sourceField' => 'isExempt', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
