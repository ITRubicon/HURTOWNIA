<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class AdditionalCostDocumentItemRepository extends IApiRepository
{
    private string $endpoint = 'none';
    protected $table = 'tema_additional_cost_document_item';

    public function saveItems(array $items): int
    {
        $this->clearDataArrays();
        
        foreach ($items as $item) {
            array_push($this->fetchResult, $item);
        }
        $resCount = count($this->fetchResult);
        $this->save();
        $this->clearDataArrays();

        return $resCount;
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'doc_id', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
