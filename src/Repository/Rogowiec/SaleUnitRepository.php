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
        $branches = $this->getBranches();
        $branchesCount = count($branches);
        $i = 1;
        foreach ($branches as $b) {
            echo "\nOddział $b ----> $i/$branchesCount";
            $res = $this->fetchApiResult($this->endpoint . "&BranchId=" . $b);
            $this->fetchResult = array_merge($this->fetchResult, $res);
            $i++;
        }
        $resCount = count($this->fetchResult);
        $this->save();
        return ['fetched' => $resCount];
    }

    private function getBranches()
    {
        $q = "SELECT id_zaob AS branch_id FROM rogowiec_branch WHERE source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
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
