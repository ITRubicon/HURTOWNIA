<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class InvoiceCustomerRepository extends IApiRepository
{
    protected $table = 'rogowiec_invoice_customer';

    public function saveCustomers(array $customers)
    {
        $this->clearDataArrays();
        $this->fetchResult = $customers;
        $this->save();
        $this->clearDataArrays();
    }

    protected function getFieldsParams(): array
    {
        return [
            'customer_code' => ['sourceField' => 'code', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'first_name' => ['sourceField' => 'firstName', 'type' => ParameterType::STRING],
            'last_name' => ['sourceField' => 'lastName', 'type' => ParameterType::STRING],
            'tax_number' => ['sourceField' => 'taxNumber', 'type' => ParameterType::STRING],
            'personal_id' => ['sourceField' => 'personalId', 'type' => ParameterType::STRING],
            'busines_number' => ['sourceField' => 'businesNumber', 'type' => ParameterType::STRING],
            'kind' => ['sourceField' => 'kind', 'type' => ParameterType::STRING],
            'customer_kind' => ['sourceField' => 'customer_kind', 'type' => ParameterType::STRING],
            'invoice_id' => ['sourceField' => 'invoice_id', 'type' => ParameterType::INTEGER],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
