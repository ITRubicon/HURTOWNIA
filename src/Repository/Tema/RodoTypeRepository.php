<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class RodoTypeRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/customers/privacy-agreement-types';
    protected $table = 'tema_typ_rodo';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $this->fetchResult = $this->fetchApiResult($this->endpoint);
        $this->save();
        $resCount = count($this->fetchResult);
        $this->clearDataArrays();
        return ['fetched' => $resCount];
    }

    protected function getFieldsParams(): array
    {
        return [
            'type_id' => ['sourceField' => 'id', 'type' => ParameterType::INTEGER],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'description' => ['sourceField' => 'description', 'type' => ParameterType::STRING],
            'channel' => ['sourceField' => 'channel', 'type' => ParameterType::STRING],
            'scope' => ['sourceField' => 'scope', 'type' => ParameterType::STRING, 'format' => ['json' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
