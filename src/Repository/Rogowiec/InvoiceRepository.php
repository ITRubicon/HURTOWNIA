<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class InvoiceRepository extends IApiRepository
{
    private string $invoiceIdEndpoint = '/api/GetInvoiceList?DateFrom={date_from}&DateTo={date_to}&ResourceId={resource_id}';
    private string $idEndpoint = '/api/GetInvoice?Id={id}';
    private string $nameEndpoint = '/api/GetInvoice?InvoiceNo={InvoiceNo}';
    private $customers = [];
    protected $table = 'rogowiec_invoice';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $invoicesIds = $this->fetchInvoiceId();
        $invoicesArchived = $this->getPresentDocumentIds();
        $invoicesIdsToFetch = array_values(array_diff($invoicesIds, $invoicesArchived));

        $invoicesIdsCount = count($invoicesIdsToFetch);

        for ($i = 0; $i < $invoicesIdsCount; $i++) {
            echo "\nId faktury " . $invoicesIdsToFetch[$i] . " ----> " . $i + 1 . "/$invoicesIdsCount";
            $url = str_replace('{id}', $invoicesIdsToFetch[$i], $this->idEndpoint);
            $res = $this->fetchApiResult($url);
            if (empty($res['id']))
                continue;

            $this->collectClients($res);
            array_push($this->fetchResult, $res);
            $this->save();
            unset($invoicesIdsToFetch[$i]);
            $this->fetchResult = [];
        }

        return [
            'fetched' => $invoicesIdsCount,
            'customers' => $this->customers
        ];
    }

    public function archive()
    {
        $q = "INSERT INTO rogowiec_invoice_archive (id, source, `number`, doc_date, sale_date, currency, net_value, gross_value, corrected_no, worker)
            SELECT id, source, `number`, doc_date, sale_date, currency, net_value, gross_value, corrected_no, worker
            FROM rogowiec_invoice ri WHERE source = :source
                ON duplicate KEY UPDATE
                id = ri.id,
                net_value = ri.net_value,
                gross_value = ri.gross_value,
                corrected_no = ri.corrected_no,
                worker = ri.worker
        ";
        $this->db->executeQuery($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);

        $q = "SELECT COUNT(*) AS total FROM rogowiec_invoice_customer";
        $totalCustomers = $this->db->fetchOne($q);
        $chunkSize = 10000;
        $steps = ceil($totalCustomers / $chunkSize);

        for ($i = 0; $i < $steps; $i++) {
            $offset = $i * $chunkSize;

            $q = "INSERT INTO rogowiec_invoice_customer_archive (invoice_id, source, customer_kind, customer_code, name, first_name, last_name, tax_number, personal_id, busines_number, kind)
                SELECT invoice_id, source, customer_kind, customer_code, name, first_name, last_name, tax_number, personal_id, busines_number, kind
                FROM rogowiec_invoice_customer ric WHERE source = :source
                LIMIT $offset, $chunkSize
                ON duplicate KEY UPDATE
                    invoice_id = ric.invoice_id,
                    customer_kind = ric.customer_kind,
                    customer_code = ric.customer_code,
                    name = ric.name,
                    first_name = ric.first_name,
                    last_name = ric.last_name,
                    tax_number = ric.tax_number,
                    personal_id = ric.personal_id,
                    busines_number = ric.busines_number,
                    kind = ric.kind
            ";
            $this->db->executeQuery($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
        }

        $q = "UPDATE rogowiec_invoice_archive i
            JOIN rogowiec_invoice_customer_archive c ON c.source = :source AND c.invoice_id = i.id AND ISNULL(i.customer_code) AND c.customer_kind = 'Recipient'
            SET i.customer_code = c.customer_code
        ";
        $this->db->executeQuery($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);

        $q = "UPDATE rogowiec_invoice_archive i
            JOIN rogowiec_invoice_customer_archive c ON c.source = :source AND c.invoice_id = i.id AND ISNULL(i.customer_code) AND c.customer_kind = 'ExternalPayment'
            SET i.customer_code = c.customer_code
        ";
        $this->db->executeQuery($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);

        $q = "UPDATE rogowiec_invoice_archive i
            JOIN rogowiec_invoice_customer_archive c ON c.source = :source AND c.invoice_id = i.id AND ISNULL(i.customer_code) AND c.customer_kind = 'Buyer'
            SET i.customer_code = c.customer_code
        ";
        $this->db->executeQuery($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);

        $q = "UPDATE rogowiec_invoice_archive i
            JOIN rogowiec_invoice_customer_archive c ON c.source = :source AND c.invoice_id = i.id AND ISNULL(i.customer_code) AND c.customer_kind = 'Dealer'
            SET i.customer_code = c.customer_code
        ";
        $this->db->executeQuery($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
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
            throw new \Exception("Nie żadnych jednostek sprzedaży. Najpierw uruchom komendę pobierającą listę oddziałów [rogowiec:saleunit]", 99);

        return $invoiceIds;
    }

    private function fetchUnitId()
    {
        $q = "SELECT resource_id FROM rogowiec_sale_unit WHERE source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    private function getPresentDocumentIds()
    {
        $q = "SELECT id FROM rogowiec_invoice_archive WHERE (NOT ISNULL(id) OR coalesce(customer_code,'') = '') AND source = :source AND doc_date BETWEEN :dateFrom AND :dateTo";
        return $this->db->fetchFirstColumn(
            $q,
            ['source' => $this->source->getName(), 'dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo],
            ['source' => ParameterType::STRING, 'dateFrom' => ParameterType::STRING, 'dateTo' => ParameterType::STRING]
        );
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
            'worker' => ['sourceField' => 'issuedBy', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->customers = [];
    }
}
