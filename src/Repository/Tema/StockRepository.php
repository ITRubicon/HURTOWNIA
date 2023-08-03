<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class StockRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/stocks';
    protected $table = 'tema_stock';

    public function fetch(): array
    {
        $this->clearDataArrays();
        
        $this->fetchResult = $this->fetchApiResult($this->endpoint);
        $this->save();
        $resCount = count($this->fetchResult);
        
        return ['fetched' => $resCount];
    }

    protected function getFieldsParams(): array
    {
        return [
            'category' => ['sourceField' => 'category', 'type' => ParameterType::STRING],
            'stock_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
