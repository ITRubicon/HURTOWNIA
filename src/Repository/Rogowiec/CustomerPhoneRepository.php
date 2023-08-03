<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CustomerPhoneRepository extends IApiRepository
{
    protected $table = 'rogowiec_customer_phone';

    public function savePhones(array $phones)
    {
        $this->clearDataArrays();
        $this->fetchResult = $phones;
        $this->save();
        $this->clearDataArrays();
    }

    protected function getFieldsParams(): array
    {
        return [
            'customer_code' => ['sourceField' => 'customer_code', 'type' => ParameterType::STRING],
            'number' => ['sourceField' => 'number', 'type' => ParameterType::STRING],
            'owner' => ['sourceField' => 'owner', 'type' => ParameterType::STRING],
            'is_default' => ['sourceField' => 'default', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
