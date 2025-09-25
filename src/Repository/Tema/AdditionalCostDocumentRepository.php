<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class AdditionalCostDocumentRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/additional-costs-documents/01/{registerId}/:sync';
    private $items = [];
    protected $table = 'tema_additional_cost_document';

    public function fetch(): array
    {
        $registers = $this->getRegisters();
        $resCount = 0;

        foreach ($registers as $registerId) {
            $this->clearDataArrays();
            $res = [];
            do {
                $nextTimestamp = '';
                if (!empty($res['lastTimestamp']))
                    $nextTimestamp = '?timestamp=' . urlencode($res['lastTimestamp']);

                $endpoint = str_replace('{registerId}', $registerId, $this->endpoint);
                $res = $this->fetchApiResult($endpoint . $nextTimestamp);
                if (empty($res))
                    continue;

                $this->getItems($res['items']);
                $this->fetchResult = array_merge($this->fetchResult, $res['items']);
                $resCount += count($res['items']);

                if (count($this->fetchResult) >= $this->fetchLimit) {
                    $this->save();
                    $this->fetchResult = [];
                    
                    
                }
            } while ($res['fetchNext']);
            $this->save();
            $this->fetchResult = [];

            
        }

        return [
            'fetched' => $resCount,
            'items' => $this->items
        ];
    }

    private function getItems(array &$documents)
    {
        foreach ($documents as $i => $d) {
            foreach ($d['items'] as $item) {
                $item['doc_id'] = $d['rowId'];
                array_push($this->items, $item);
                unset($documents[$i]['items']);
            }
        }
    }

    private function getRegisters(): array
    {
        $q = "SELECT id FROM tema_additional_cost_register WHERE source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'rowId', 'type' => ParameterType::INTEGER],
            'name' => ['sourceField' => 'documentNumber', 'type' => ParameterType::STRING],
            'issue_date' => ['sourceField' => 'issueDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'entry_date' => ['sourceField' => 'entryDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'customer_id' => ['sourceField' => 'customerId', 'type' => ParameterType::STRING],
            'payment_method' => ['sourceField' => 'paymentMethod', 'type' => ParameterType::STRING],
            'due_date' => ['sourceField' => 'dueDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'gross_value' => ['sourceField' => 'grossValue', 'type' => ParameterType::STRING],
            'tax_amount' => ['sourceField' => 'taxAmount', 'type' => ParameterType::STRING],
            'notes' => ['sourceField' => 'notes', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->items = [];
    }
}
