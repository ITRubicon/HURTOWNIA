<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceOrderItemRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/repair-orders/{branchId}/{repairOrderId}';
    protected $table = 'tema_service_order_item';
    protected $invoices = [];

    public function saveItems(array $items)
    {
        $this->clearDataArrays();
        
        foreach ($items as $item) {
            $this->collectInvoices($item);
            $item = array_merge($item, $item['taxRate']);
            unset($item['taxRate']);
            array_push($this->fetchResult, $item);
        }
        $this->save();
        $resCount = count($this->fetchResult);
        $this->clearDataArrays();
        
        return [
            'fetched' => $resCount,
            'invoices' => $this->invoices,
        ];
    }

    protected function collectInvoices(array &$item)
    {
        if (empty($item['invoiceNames']))
            return;

        foreach ($item['invoiceNames'] as $i) {
            if (empty($i) || $i === '(zwrot)')
                continue;

            $invoice['doc_id'] = $item['doc_id'];
            $invoice['product_id'] = $item['productId'];
            $invoice['product_code'] = $item['productCode'];
            $invoice['invoice_name'] = $i;
            array_push($this->invoices, $invoice);
        }
        unset($item['invoiceNames']);
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
            'gdn_name' => ['sourceField' => 'gdnName', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
