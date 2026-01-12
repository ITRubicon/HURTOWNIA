<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ReceiptCorrectionItemRepository extends IApiRepository
{
    private string $endpoint = '';
    protected $table = 'tema_receipt_correction_item';

    public function saveItems(array $items): int
    {
        $this->clearDataArrays();
        
        foreach ($items as $item) {
            $item = array_merge($item, $item['taxRate']);
            unset($item['taxRate']);
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
            'original_quantity' => ['sourceField' => 'originalQuantity', 'type' => ParameterType::STRING],
            'corrected_quantity_difference' => ['sourceField' => 'correctedQuantityDifference', 'type' => ParameterType::STRING],
            'net_price' => ['sourceField' => 'netPrice', 'type' => ParameterType::STRING],
            'original_net_price' => ['sourceField' => 'originalNetPrice', 'type' => ParameterType::STRING],
            'corrected_net_price_difference' => ['sourceField' => 'correctedNetPriceDifference', 'type' => ParameterType::STRING],
            'purchase_price' => ['sourceField' => 'purchasePrice', 'type' => ParameterType::STRING],
            'unit' => ['sourceField' => 'unit', 'type' => ParameterType::STRING],
            'tax_rate' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'is_exempt' => ['sourceField' => 'isExempt', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
