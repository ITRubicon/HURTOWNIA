<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class FvItemRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/sales-invoices/{branchId}/{invoiceId}';
    protected $table = 'tema_fv_document_item';
    protected $onDuplicateClause = 'ON DUPLICATE KEY UPDATE
        product_id = VALUES(product_id),
        name = VALUES(name),
        quantity = VALUES(quantity),
        net_price = VALUES(net_price),
        purchase_price = VALUES(purchase_price),
        percentage_share = VALUES(percentage_share),
        unit = VALUES(unit),
        tax_rate = VALUES(tax_rate),
        is_exempt = VALUES(is_exempt),
        set_product_id = VALUES(set_product_id),
        set_products = VALUES(set_products),
    ';

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
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'product_id' => ['sourceField' => 'productId', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'quantity' => ['sourceField' => 'quantity', 'type' => ParameterType::STRING],
            'net_price' => ['sourceField' => 'netPrice', 'type' => ParameterType::STRING],
            'purchase_price' => ['sourceField' => 'purchasePrice', 'type' => ParameterType::STRING],
            'percentage_share' => ['sourceField' => 'percentageShare', 'type' => ParameterType::STRING],
            'unit' => ['sourceField' => 'unit', 'type' => ParameterType::STRING],
            'tax_rate' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'is_exempt' => ['sourceField' => 'isExempt', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'set_product_id' => ['sourceField' => 'setProductId', 'type' => ParameterType::STRING],
            'set_products' => ['sourceField' => 'setProducts', 'type' => ParameterType::STRING, 'format' => ['json' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
