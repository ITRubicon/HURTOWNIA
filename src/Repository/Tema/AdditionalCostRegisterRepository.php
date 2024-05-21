<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class AdditionalCostRegisterRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/additional-costs-documents/01/registers';
    protected $table = 'tema_additional_cost_register';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $this->fetchResult = $this->fetchApiResult($this->endpoint);
        $resultCount = count($this->fetchResult);
        $this->save();

        return [
            'fetched' => $resultCount,
        ];
    }

    protected function getFieldsParams(): array
    {
        return [
            'id' => ['sourceField' => 'registerId', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
