<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceOrderMechanicRepository extends IApiRepository
{
    protected $table = 'tema_service_order_item_mechanic';

    public function saveMechanics(array $cars): int
    {
        $this->clearDataArrays();
        $this->fetchResult = $cars;
        $resCount = count($this->fetchResult);
        $this->save();
        $this->clearDataArrays();

        return $resCount;
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'product_id' => ['sourceField' => 'product_id', 'type' => ParameterType::INTEGER],
            'product_code' => ['sourceField' => 'product_code', 'type' => ParameterType::STRING],
            'user_id' => ['sourceField' => 'userId', 'type' => ParameterType::INTEGER],
            'man_hour' => ['sourceField' => 'manHour', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
