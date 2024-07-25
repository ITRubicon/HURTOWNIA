<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceOrderItemPackageItemRepository extends IApiRepository
{
    private string $endpoint = '';
    private $invoices = [];
    private $workerHours = [];
    protected $table = 'tema_service_order_item_package_item';

    public function saveItems(array $items)
    {
        $this->clearDataArrays();
        
        foreach ($items as $item) {
            $item = array_merge($item, $item['taxRate']);
            unset($item['taxRate']);

            $this->collectInvoices($item);
            $this->collectWorkerHours($item);

            array_push($this->fetchResult, $item);
        }
        $resCount = count($this->fetchResult);
        $this->save();
        $this->clearDataArrays();
        
        return [
            'fetched' => $resCount,
            'invoices' => $this->invoices,
            'workerHours' => $this->workerHours,
        ];
    }

    protected function collectInvoices(array &$item)
    {
        if (empty($item['invoiceNames']))
            return;

        foreach ($item['invoiceNames'] as $i) {
            if (empty($i))
                continue;

            $invoice['doc_id'] = $item['doc_id'];
            $invoice['item_product_id'] = $item['productId'];
            $invoice['item_product_code'] = $item['productCode'];
            $invoice['invoice_name'] = $i;
            array_push($this->invoices, $invoice);
        }
        unset($item['invoiceNames']);
    }

    protected function collectWorkerHours(array &$item)
    {
        if (empty($item['repairOrderItemMechanics']))
            return;

        foreach ($item['repairOrderItemMechanics'] as $i) {
            if (empty($i))
                continue;

            $mechanic['doc_id'] = $item['doc_id'];
            $mechanic['item_product_id'] = $item['item_product_id'];
            $mechanic['item_product_code'] = $item['item_product_code'];
            $mechanic['product_id'] = $item['productId'];
            $mechanic['product_code'] = $item['productCode'];
            $mechanic['userId'] = $i['userId'];
            $mechanic['manHour'] = $i['manHour'];
            array_push($this->workerHours, $mechanic);
        }
        unset($item['repairOrderItemMechanics']);
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'item_product_id' => ['sourceField' => 'item_product_id', 'type' => ParameterType::INTEGER],
            'item_product_code' => ['sourceField' => 'item_product_code', 'type' => ParameterType::STRING],
            'type' => ['sourceField' => 'type', 'type' => ParameterType::STRING],
            'product_id' => ['sourceField' => 'productId', 'type' => ParameterType::STRING],
            'product_code' => ['sourceField' => 'productCode', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'quantity' => ['sourceField' => 'quantity', 'type' => ParameterType::STRING],
            'net_price' => ['sourceField' => 'netPrice', 'type' => ParameterType::STRING],
            'tax_rate' => ['sourceField' => 'value', 'type' => ParameterType::STRING],
            'is_exempt' => ['sourceField' => 'isExempt', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'gdn_name' => ['sourceField' => 'gdnName', 'type' => ParameterType::STRING],
            'gdn_id' => ['sourceField' => 'gdnId', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
