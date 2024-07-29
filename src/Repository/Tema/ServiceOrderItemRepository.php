<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceOrderItemRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/repair-orders/{branchId}/{repairOrderId}';
    protected $table = 'tema_service_order_item';
    protected $invoices = [];
    protected $workerHours = [];
    protected $packageItems = [];

    public function saveItems(array $items)
    {
        $this->clearDataArrays();
        foreach ($items as $item) {
            // $this->collectInvoices($item);
            // $this->collectWorkerHours($item);
            // $this->collectPackageItems($item);
            $item = array_merge($item, $item['taxRate']);
            unset($item['taxRate']);
            array_push($this->fetchResult, $item);
        }
        $resCount = count($this->fetchResult);
        $this->save();
        $this->clearDataArrays();

        return [
            'fetched' => $resCount,
            // 'invoices' => $this->invoices,
            // 'workerHours' => $this->workerHours,
            // 'packageItems' => $this->packageItems,
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
            $invoice['product_id'] = $item['productId'];
            $invoice['product_code'] = $item['productCode'];
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

            $i['doc_id'] = $item['doc_id'];
            $i['product_id'] = $item['productId'];
            $i['product_code'] = $item['productCode'];
            array_push($this->workerHours, $i);
        }
        unset($item['repairOrderItemMechanics']);
    }

    protected function collectPackageItems(array &$item)
    {
        if (empty($item['packageItems']))
            return;

        foreach ($item['packageItems'] as $i) {
            if (empty($i))
                continue;

            $i['doc_id'] = $item['doc_id'];
            $i['item_product_id'] = $item['productId'];
            $i['item_product_code'] = $item['productCode'];
            array_push($this->packageItems, $i);
        }
        unset($item['packageItems']);
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
