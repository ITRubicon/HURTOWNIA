<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class PackageItemFvRepository extends IApiRepository
{
    private string $endpoint = '';
    protected $table = 'tema_service_order_item_package_item_fv';

    public function saveInvoices(array $items)
    {
        $this->clearDataArrays();
        $this->fetchResult = array_merge($this->fetchResult, $items);
        $resCount = count($this->fetchResult);
        $this->save();
        $this->clearDataArrays();

        return $resCount;
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'item_product_id' => ['sourceField' => 'item_product_id', 'type' => ParameterType::STRING],
            'item_product_code' => ['sourceField' => 'item_product_code', 'type' => ParameterType::STRING],
            'product_id' => ['sourceField' => 'product_id', 'type' => ParameterType::STRING],
            'product_code' => ['sourceField' => 'product_code', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'invoice_name', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
