<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceOrderEndDocumentRepository extends IApiRepository
{
    protected $table = 'tema_service_order_end_documents';

    public function saveDocs(array $documents): int
    {
        $this->clearDataArrays();
        $this->fetchResult = $documents;
        unset($documents);
        $this->removeDocs();
        $resCount = count($this->fetchResult);
        $this->save();
        $this->clearDataArrays();

        

        return $resCount;
    }

    private function removeDocs()
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
            'end_doc_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'type' => ['sourceField' => 'type', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
