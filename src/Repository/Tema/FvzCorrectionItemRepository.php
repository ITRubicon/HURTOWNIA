<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class FvzCorrectionItemRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/purchase-invoice-corrections/{branchId}/{correctionId}';
    protected $table = 'tema_fvz_correction_document_item';

    public function saveItems(array $items): int
    {
        $this->clearDataArrays();
        
        foreach ($items as $item) {
            $item = array_merge($item, $item['taxRate']);
            unset($item['taxRate']);
            array_push($this->fetchResult, $item);
        }
        unset($items);
        $this->removeOld();
        $resCount = count($this->fetchResult);
        $this->save();
        $this->clearDataArrays();     

        return $resCount;
    }

    private function removeOld()
    {
        $docIds = array_unique(array_map(function($item) { return $item['doc_id']; }, $this->fetchResult));
        if (count($docIds)) {
            $placeholders = implode(',', array_fill(0, count($docIds), '?'));
            $this->db->executeStatement("DELETE FROM $this->table WHERE source = ? AND doc_id IN ($placeholders)", array_merge([$this->source->getName()], $docIds), array_merge([ParameterType::STRING], array_fill(0, count($docIds), ParameterType::STRING)));
        }
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
            'corrected_price_difference' => ['sourceField' => 'correctedPriceDifference', 'type' => ParameterType::STRING],
            'tax_rate' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'is_exempt' => ['sourceField' => 'isExempt', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'car_id' => ['sourceField' => 'vehicleId', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'income_type' => ['sourceField' => 'incomeType', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
