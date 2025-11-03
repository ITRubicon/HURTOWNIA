<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class MmItemRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/outgoing-transfer-notes/{branchId}?documentId={documentId}';
    protected $table = 'tema_mm_document_item';

    public function saveItems(array $items): int
    {
        $this->clearDataArrays();
        
        foreach ($items as $item) {

            if (isset($item['unit'])) {
                $item['unit_id'] = $item['unit']['id'];
                $item['unit_name'] = $item['unit']['name'];
            }

            $item = array_merge($item, $item['taxRate']);

            unset($item['taxRate'], $item['unit']);
            array_push($this->fetchResult, $item);
        }
        unset($items);
        $resCount = count($this->fetchResult);
        $this->save();
        $this->clearDataArrays();

        

        return $resCount;
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'product_id' => ['sourceField' => 'productId', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'quantity' => ['sourceField' => 'quantity', 'type' => ParameterType::STRING],
            'net_price' => ['sourceField' => 'netPrice', 'type' => ParameterType::STRING],
            'purchase_price' => ['sourceField' => 'purchasePrice', 'type' => ParameterType::STRING],
            'percentage_share' => ['sourceField' => 'percentageShare', 'type' => ParameterType::INTEGER],
            'unit_id' => ['sourceField' => 'unit_id', 'type' => ParameterType::INTEGER],
            'unit' => ['sourceField' => 'unit_name', 'type' => ParameterType::STRING],
            'tax_rate' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'is_exempt' => ['sourceField' => 'isExempt', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
