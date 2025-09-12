<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class FvDocumentRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/sales-invoices/{branchId}';
    private $documentEndpoints = [];
    protected $table = 'tema_fv_document';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $this->getDocumentEndpointList();
        $this->filterOutPossessed();

        $listCount = count($this->documentEndpoints);
        $documentItems = [];
        $setProducts = [];

        if ($listCount) {
            echo "\nPobieram zapisy dokumentów";

            for ($i=0; $i < $listCount; $i++) {
                echo "\nEndpoint ----> $i/$listCount";
                $doc = $this->fetchApiResult($this->documentEndpoints[$i]['getUrl']);
                unset($this->documentEndpoints[$i]);

                if (empty($doc))
                    continue;

                $this->collectItems($doc, $documentItems, $setProducts);
                array_push($this->fetchResult, $doc);
                unset($doc);

                if (count($this->fetchResult) >= $this->fetchLimit) {
                    $this->save();
                    $this->fetchResult = [];
                    $this->relatedRepositories['items']->saveItems($documentItems);
                    $documentItems = [];
                    $this->relatedRepositories['setProducts']->saveSetProducts($setProducts);
                    $setProducts = [];

                    gc_collect_cycles();
                }
            }

            $this->save();
            $this->fetchResult = [];
            $this->relatedRepositories['items']->saveItems($documentItems);
            $this->relatedRepositories['setProducts']->saveSetProducts($setProducts);
            unset($documentItems, $setProducts);
        }

        return [
            'fetched' => $listCount,
        ];
    }

    private function filterOutPossessed()
    {
        $possessed = $this->db->fetchFirstColumn("SELECT doc_id FROM {$this->table} WHERE source = :source", ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);

        echo PHP_EOL . 'Możliwe do pobrania dokumenty: ' . count($this->documentEndpoints) . "\033[0m" . PHP_EOL;
        echo PHP_EOL . 'Posiadane dokumenty: ' . count($possessed) . "\033[0m" . PHP_EOL;

        if (!empty($possessed)) {
            // array:2 [
            // "objectId" => "FV1100000109"
            // "getUrl" => "/api/dms/v1/sales-invoices/11/FV1100000109"
            // ]

            $this->documentEndpoints = array_filter($this->documentEndpoints, function($doc) use ($possessed) {
                return !in_array($doc['objectId'], $possessed);
            });
            // reset keys
            $this->documentEndpoints = array_values($this->documentEndpoints);
        }
        echo PHP_EOL . 'Do pobrania dokumenty: ' . count($this->documentEndpoints) . "\033[0m" . PHP_EOL;
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
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'customer_id' => ['sourceField' => 'customerId', 'type' => ParameterType::STRING],
            'issue_date' => ['sourceField' => 'issueDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'due_date' => ['sourceField' => 'dueDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'gross_value' => ['sourceField' => 'grossValue', 'type' => ParameterType::STRING],
            'currency' => ['sourceField' => 'currency', 'type' => ParameterType::STRING],
            'order_id' => ['sourceField' => 'orderId', 'type' => ParameterType::STRING],
            'order_name' => ['sourceField' => 'orderName', 'type' => ParameterType::STRING],
            'payment_method' => ['sourceField' => 'paymentMethod', 'type' => ParameterType::STRING],
            'operator_code' => ['sourceField' => 'operatorCode', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'notes' => ['sourceField' => 'notes', 'type' => ParameterType::STRING],
            'who' => ['sourceField' => 'who', 'type' => ParameterType::STRING],
            'for_whom' => ['sourceField' => 'forWhom', 'type' => ParameterType::STRING],
            'reason' => ['sourceField' => 'reason', 'type' => ParameterType::STRING],
            'branch' => ['sourceField' => 'branch', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    private function collectItems(array &$doc, array &$documentItems, array &$setProducts)
    {
        foreach ($doc['items'] as $item) {               
            $item['doc_id'] = $doc['id'];
            $item['unit'] = $item['unit']['name'] ?? '';
            array_push($documentItems, $item);

            if ($item['setProductId'] !== 0 || !empty($item['setProducts'])) {
                foreach ($item['setProducts'] as $product) {
                    $product['set_product_id'] = $item['setProductId'];
                    $product['doc_id'] = $doc['id'];
                    $product['unit'] = $product['unit']['name'] ?? '';
                    array_push($setProducts, $product);
                }
            }
        }
        unset($doc['items']);
    }

    private function getStocks()
    {
        $q = "SELECT stock_id FROM tema_stock WHERE source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->documentEndpoints = [];
    }
}
