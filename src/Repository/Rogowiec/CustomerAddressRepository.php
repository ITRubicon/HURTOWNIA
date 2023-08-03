<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CustomerAddressRepository extends IApiRepository
{
    protected $table = 'rogowiec_customer_address';

    public function saveAddresses(array $addresses)
    {
        $this->clearDataArrays();
        $this->fetchResult = $addresses;
        $this->save();
        $this->clearDataArrays();
    }

    protected function getFieldsParams(): array
    {
        return [
            'customer_code' => ['sourceField' => 'customer_code', 'type' => ParameterType::STRING],
            'country' => ['sourceField' => 'country', 'type' => ParameterType::STRING],
            'city' => ['sourceField' => 'city', 'type' => ParameterType::STRING],
            'street' => ['sourceField' => 'street', 'type' => ParameterType::STRING],
            'postal_code' => ['sourceField' => 'postal_code', 'type' => ParameterType::STRING],
            'number' => ['sourceField' => 'number', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
