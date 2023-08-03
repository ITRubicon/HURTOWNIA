<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CustomerEmailRepository extends IApiRepository
{
    protected $table = 'rogowiec_customer_email';

    public function saveEmails(array $emails)
    {
        $this->clearDataArrays();
        $this->fetchResult = $emails;
        $this->save();
        $this->clearDataArrays();
    }

    protected function getFieldsParams(): array
    {
        return [
            'customer_code' => ['sourceField' => 'customer_code', 'type' => ParameterType::STRING],
            'address' => ['sourceField' => 'address', 'type' => ParameterType::STRING],
            'owner' => ['sourceField' => 'owner', 'type' => ParameterType::STRING],
            'is_default' => ['sourceField' => 'default', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
