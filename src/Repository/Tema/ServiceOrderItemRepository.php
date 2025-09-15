<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceOrderItemRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/repair-orders/{branchId}/{repairOrderId}';
    protected $table = 'tema_service_order_item';

    public function saveItems(array $items)
    {
        $this->clearDataArrays();
        foreach ($items as $item) {
            $item = array_merge($item, $item['taxRate']);
            unset($item['taxRate']);
            array_push($this->fetchResult, $item);
        }
        unset($items);
        $resCount = count($this->fetchResult);
        $this->removeItems();
        $this->save();
        $this->clearDataArrays();

        gc_collect_cycles();

        return [
            'fetched' => $resCount,
        ];
    }

    private function removeItems()
    {
        $docIds = array_unique(array_column($this->fetchResult, 'doc_id'));
        if (empty($docIds))
            return;

        $docIds = "'" . implode("','", $docIds) . "'";
        $q = "DELETE FROM $this->table WHERE source = :source AND doc_id IN ($docIds)";
        $this->db->executeStatement($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'type' => ['sourceField' => 'type', 'type' => ParameterType::STRING],
            'product_id' => ['sourceField' => 'productId', 'type' => ParameterType::STRING],
            'product_code' => ['sourceField' => 'productCode', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'quantity' => ['sourceField' => 'quantity', 'type' => ParameterType::STRING],
            'net_price' => ['sourceField' => 'netPrice', 'type' => ParameterType::STRING],
            'tax_rate' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'is_exempt' => ['sourceField' => 'isExempt', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'gdn_id' => ['sourceField' => 'gdnId', 'type' => ParameterType::STRING],
            'gdn_name' => ['sourceField' => 'gdnName', 'type' => ParameterType::STRING],
            'invoice_names' => ['sourceField' => 'invoiceNames', 'type' => ParameterType::STRING, 'format' => ['json' => true]],
            'repair_order_item_mechanics' => ['sourceField' => 'repairOrderItemMechanics', 'type' => ParameterType::STRING, 'format' => ['json' => true]],
            'package_items' => ['sourceField' => 'packageItems', 'type' => ParameterType::STRING, 'format' => ['json' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
