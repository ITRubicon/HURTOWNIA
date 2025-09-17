<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceInvoiceItemRepository extends IApiRepository
{
    private string $endpoint = '';
    protected $table = 'tema_service_invoice_item';

    public function saveItems(array $items)
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

        return [
            'fetched' => $resCount,
        ];
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
            'gross_price' => ['sourceField' => 'grossPrice', 'type' => ParameterType::STRING],
            'tax_rate' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'is_exempt' => ['sourceField' => 'isExempt', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
