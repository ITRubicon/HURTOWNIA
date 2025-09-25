<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class OrgUnitRepository extends IApiRepository
{
    private string $endpoint = '/wdf/Reports/jednostkiOrg';
    protected $table = 'rogowiec_org_unit';

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
            'jedn_org_id' => ['sourceField' => 'idjo', 'type' => ParameterType::STRING],
            'jedn_org_kod' => ['sourceField' => 'kodjo', 'type' => ParameterType::STRING],
            'nazwa' => ['sourceField' => 'nazwa', 'type' => ParameterType::STRING],
            'rodzaj' => ['sourceField' => 'rodzaj', 'type' => ParameterType::STRING],
            'oddzial' => ['sourceField' => 'oodzial', 'type' => ParameterType::STRING],
            'id_zasob' => ['sourceField' => 'idzasob', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
