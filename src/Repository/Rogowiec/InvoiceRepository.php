<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class InvoiceRepository extends IApiRepository
{
    private string $invoiceIdEndpoint = '/api/GetInvoiceList?DateFrom={date_from}&DateTo={date_to}&ResourceId={resource_id}';
    private string $endpoint = '/api/GetInvoice?Id={id}';
    private $customers = [];
    protected $table = 'rogowiec_invoice';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $invoicesIds = $this->fetchInvoiceId();
        $invoicesIdsCount = count($invoicesIds);
        $this->clearDataArrays();

        for ($i=0; $i < $invoicesIdsCount ; $i++) {
            echo "\nId faktury " . $invoicesIds[$i] . " ----> " . $i+1 . "/$invoicesIdsCount";
            $url = str_replace('{id}', $invoicesIds[$i], $this->endpoint);
            $res = $this->fetchApiResult($url);
            if ($res['id'] === 0)
                continue;

            $this->collectClients($res);
            array_push($this->fetchResult, $res);
            
            unset($invoicesIds[$i]);
        }
            $this->save();
            $this->fetchResult = [];

        return [
            'fetched' => $invoicesIdsCount,
            'customers' => $this->customers
        ];
    }

    private function collectClients(array &$invoice)
    {
        foreach ($invoice['customers'] as $c) {
            $c['customer']['invoice_id'] = $invoice['id'];
            $c['customer']['customer_kind'] = $c['kind'];
            array_push($this->customers, $c['customer']);
        }
        unset($invoice['customers']);
    }

    private function fetchInvoiceId()
    {
        $unitsIds = $this->fetchUnitId();
        $branchesCount = count($unitsIds);
        $invoiceIds = [];

        if ($branchesCount) {
            foreach ($unitsIds as $id) {
                $url = str_replace(
                    ['{resource_id}', '{date_from}', '{date_to}'],
                    [$id, $this->dateFrom, $this->dateTo],
                    $this->invoiceIdEndpoint
                );

                $res = $this->fetchApiResult($url);
                if (!empty($res['items']))
                    $invoiceIds = array_merge($invoiceIds, array_values($res['items']));
            }
        } else 
            throw new \Exception("Nie żadnych . Najpierw uruchom komendę pobierającą listę oddziałów [rogowiec:saleunit]", 99);

        return $invoiceIds;
    }

    private function fetchUnitId()
    {
        $q = "SELECT resource_id FROM rogowiec_sale_unit WHERE (name LIKE '%samo%' OR name LIKE '%moto%') AND name NOT LIKE '%faktury zal%' AND source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'id' => ['sourceField' => 'id', 'type' => ParameterType::INTEGER],
            'number' => ['sourceField' => 'number', 'type' => ParameterType::STRING],
            'corrected_no' => ['sourceField' => 'correctedNo', 'type' => ParameterType::STRING],
            'doc_date' => ['sourceField' => 'docDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'sale_date' => ['sourceField' => 'saleDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'currency' => ['sourceField' => 'currency', 'type' => ParameterType::STRING],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'gross_value' => ['sourceField' => 'grossValue', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->customers = [];
    }
}
