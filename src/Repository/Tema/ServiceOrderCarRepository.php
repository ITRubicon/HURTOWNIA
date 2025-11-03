<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceOrderCarRepository extends IApiRepository
{
    protected $table = 'tema_service_order_car';

    public function saveCars(array $cars): int
    {
        $this->clearDataArrays();
        $this->fetchResult = $cars;
        unset($cars);
        $this->removeCars();
        $resCount = count($this->fetchResult);
        $this->save();
        $this->clearDataArrays();

        return $resCount;
    }

    private function removeCars()
    {
        $docIds = array_unique(array_column($this->fetchResult, 'doc_id'));
        if (empty($docIds))
            return;

        $docIds = "'" . implode("','", $docIds) . "'";

        $q = "DELETE FROM $this->table WHERE source = :source AND doc_id IN ($docIds)";
        $this->db->executeStatement($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'registration_no' => ['sourceField' => 'registrationNo', 'type' => ParameterType::STRING],
            'brand_id' => ['sourceField' => 'brandId', 'type' => ParameterType::INTEGER],
            'model' => ['sourceField' => 'model', 'type' => ParameterType::STRING],
            'mileage' => ['sourceField' => 'mileage', 'type' => ParameterType::INTEGER],
            'user_id' => ['sourceField' => 'userId', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
