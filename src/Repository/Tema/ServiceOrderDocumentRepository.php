<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ServiceOrderDocumentRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/repair-orders/{branchId}';
    private $documentEndpoints = [];
    protected $table = 'tema_service_order_document';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $this->getDocumentEndpointList();
        $listCount = count($this->documentEndpoints);

        $documentItems = [];
        $endDocuments = [];
        $cars = [];


        if ($listCount) {
            echo "\nPobieram zapisy dokumentów";

            for ($i = 0; $i < $listCount; $i++) {
                echo "\nEndpoint ----> $i/$listCount";
                $doc = $this->fetchApiResult($this->documentEndpoints[$i]['getUrl']);
                unset($this->documentEndpoints[$i]);

                if (empty($doc))
                    continue;

                $this->collectItems($doc, $documentItems);
                $this->collectEndDocuments($doc, $endDocuments);
                $this->collectCar($doc, $cars);
                array_push($this->fetchResult, $doc);

                if (count($this->fetchResult) >= $this->fetchLimit) {
                    $this->save();
                    $this->fetchResult = [];
                    $this->relatedRepositories['items']->saveItems($documentItems);
                    $documentItems = [];
                    $this->relatedRepositories['endDocs']->saveDocs($endDocuments);
                    $endDocuments = [];
                    $this->relatedRepositories['cars']->saveCars($cars);
                    $cars = [];

                    gc_collect_cycles();
                }
            }

            $this->save();
            $this->fetchResult = [];
            $this->relatedRepositories['items']->saveItems($documentItems);
            unset($documentItems);
            $this->relatedRepositories['endDocs']->saveDocs($endDocuments);
            unset($endDocuments);
            $this->relatedRepositories['cars']->saveCars($cars);
            unset($cars);
            
            gc_collect_cycles();
        }

        return [
            'fetched' => $listCount,
        ];
    }

    private function getDocumentEndpointList()
    {
        $stocks = $this->getStocks();
        $stocksCount = count($stocks);

        if ($stocksCount) {
            echo "\nPobieranie endpointów dla oddziałów";
            $i = 1;

            foreach ($stocks as $stock) {
                echo "\nNr stocku $stock ----> $i/$stocksCount";
                $i++;

                $url = str_replace('{branchId}', $stock, $this->endpoint);
                $res = $this->fetchApiResult($url);
                if (empty($res))
                    continue;

                $this->documentEndpoints = array_merge($this->documentEndpoints, $res);
            }
        } else
            throw new \Exception("Nie żadnych jednostek organizacyjnych. Najpierw uruchom komendę pobierającą listę jednostek [tema:stock]", 99);
    }

    protected function getFieldsParams(): array
    {
        return [
            'doc_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'opening_date' => ['sourceField' => 'openingDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'closing_date' => ['sourceField' => 'closingDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'is_canceled' => ['sourceField' => 'isCanceled', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'gross_value' => ['sourceField' => 'grossValue', 'type' => ParameterType::STRING],
            'stock_status' => ['sourceField' => 'stockStatus', 'type' => ParameterType::STRING],
            'customer_id' => ['sourceField' => 'customerId', 'type' => ParameterType::STRING],
            'service_handling_user_id' => ['sourceField' => 'serviceHandlingUserId', 'type' => ParameterType::STRING],
            'description' => ['sourceField' => 'description', 'type' => ParameterType::STRING],
            'source_order_number' => ['sourceField' => 'sourceOrderNumber', 'type' => ParameterType::STRING],
            'source_order_id' => ['sourceField' => 'sourceOrderId', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    private function collectItems(array &$doc, array &$documentItems)
    {
        foreach ($doc['items'] as $item) {
            $item['doc_id'] = $doc['id'];
            array_push($documentItems, $item);
        }
        unset($doc['items']);
    }

    private function collectEndDocuments(array &$doc, array &$endDocuments)
    {
        foreach ($doc['documents'] as $item) {
            $item['doc_id'] = $doc['id'];
            array_push($endDocuments, $item);
        }
        unset($doc['documents']);
    }

    private function collectCar(array &$doc, array &$cars)
    {
        $doc['vehicle']['doc_id'] = $doc['id'];
        array_push($cars, $doc['vehicle']);
        unset($doc['vehicle']);
    }

    private function getStocks()
    {
        $q = "SELECT stock_id FROM tema_stock WHERE source = :source AND category = 'workshop'";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->documentEndpoints = [];
    }
}
