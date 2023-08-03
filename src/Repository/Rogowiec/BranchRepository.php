<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class BranchRepository extends IApiRepository
{
    private string $endpoint = '/wdf/Reports/oddzialy';
    protected $table = 'rogowiec_branch';


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
            'oddzial_id' => ['sourceField' => 'idoddzialu', 'type' => ParameterType::STRING],
            'nazwa' => ['sourceField' => 'nazwa', 'type' => ParameterType::STRING],
            'miejscowosc' => ['sourceField' => 'miejscowosc', 'type' => ParameterType::STRING],
            'id_zaob' => ['sourceField' => 'idzasob', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
