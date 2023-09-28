<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class SaleUnitRepository extends IApiRepository
{
    private string $endpoint = '/api/GetOrgUnits?UnitType=SALE';
    protected $table = 'rogowiec_sale_unit';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $this->fetchResult = $this->fetchApiResult($this->endpoint);
        $resCount = count($this->fetchResult);
        $this->save();
        return ['fetched' => $resCount];
    }

    protected function getFieldsParams(): array
    {
        return [
            'dms_id' => ['sourceField' => 'id', 'type' => ParameterType::INTEGER],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'type' => ['sourceField' => 'type', 'type' => ParameterType::STRING],
            'label' => ['sourceField' => 'label', 'type' => ParameterType::STRING],
            'resource_id' => ['sourceField' => 'resourceId', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
