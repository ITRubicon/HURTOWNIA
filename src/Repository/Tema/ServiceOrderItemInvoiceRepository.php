<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceOrderItemInvoiceRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/repair-orders/{branchId}/{repairOrderId}';
    protected $table = 'tema_service_order_item_invoice';

    public function saveInvoices(array $items)
    {
        $this->clearDataArrays();
        $this->fetchResult = array_merge($this->fetchResult, $items);
        $this->save();
        $resCount = count($this->fetchResult);
        $this->clearDataArrays();

        return $resCount;
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'product_id' => ['sourceField' => 'product_id', 'type' => ParameterType::STRING],
            'product_code' => ['sourceField' => 'product_code', 'type' => ParameterType::STRING],
            'invoice_name' => ['sourceField' => 'invoice_name', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
