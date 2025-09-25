<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CustomerRodoRepository extends IApiRepository
{
    protected $table = 'rogowiec_customer_rodo';

    public function saveRodo(array $rodo)
    {
        $this->clearDataArrays();
        $this->fetchResult = $rodo;
        $this->save();
        $this->clearDataArrays();
    }

    protected function getFieldsParams(): array
    {
        return [
            'customer_code' => ['sourceField' => 'customer_code', 'type' => ParameterType::STRING],
            'producer' => ['sourceField' => 'producer', 'type' => ParameterType::STRING],
            'number' => ['sourceField' => 'number', 'type' => ParameterType::STRING],
            'time' => ['sourceField' => 'time', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'valid_until' => ['sourceField' => 'validUntil', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'consents' => ['sourceField' => 'consents', 'type' => ParameterType::STRING, 'format' => ['json' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
