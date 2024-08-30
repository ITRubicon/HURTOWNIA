<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceInvoiceRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/service-invoices/{year}';
    private $documentEndpoints = [];
    private $documentItems = [];
    protected $table = 'tema_service_invoice';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $this->getDocumentEndpointList();
        $listCount = count($this->documentEndpoints);

        if ($listCount) {
            echo "\nPobieram zapisy dokumentów";

            for ($i = 0; $i < $listCount; $i++) {
                echo "\nEndpoint ----> $i/$listCount";
                $doc = $this->fetchApiResult($this->documentEndpoints[$i]['getUrl']);
                unset($this->documentEndpoints[$i]);

                if (empty($doc))
                    continue;

                $this->collectItems($doc);
                array_push($this->fetchResult, $doc);
            }
            $this->save();
            $this->fetchResult = [];
        }

        return [
            'fetched' => $listCount,
            'items' => $this->documentItems,
        ];
    }

    private function getDocumentEndpointList()
    {
        $year = date('Y', strtotime($this->dateFrom));
        $url = str_replace('{year}', $year, $this->endpoint);

        $this->documentEndpoints = $this->fetchApiResult($url);
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'customer_id' => ['sourceField' => 'customerId', 'type' => ParameterType::STRING],
            'issue_date' => ['sourceField' => 'issueDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'due_date' => ['sourceField' => 'dueDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'gross_value' => ['sourceField' => 'grossValue', 'type' => ParameterType::STRING],
            'operator_code' => ['sourceField' => 'operatorCode', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'notes' => ['sourceField' => 'notes', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    private function collectItems(array &$doc)
    {
        foreach ($doc['items'] as $item) {
            $item['doc_id'] = $doc['id'];
            array_push($this->documentItems, $item);
        }
        unset($doc['items']);
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->documentEndpoints = [];
        $this->documentItems = [];
    }
}
