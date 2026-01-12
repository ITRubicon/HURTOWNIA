<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CustomerRepository extends IApiRepository
{
    private string $endpoint = '/api/GetCustomer?Code={code}';
    protected $table = 'rogowiec_customer';
    private $addresses = [];
    private $phones = [];
    private $emails = [];
    private $dataProcessingStatements = [];
    private const BATCH_SIZE = 5; // Number of simultaneous requests per batch

    public function fetch(): array
    {
        $this->clearDataArrays();
        $clientCodes = $this->fetchClientCodes();
        $codesCount = count($clientCodes);

        if ($codesCount === 0) {
            return [
                'fetched' => 0,
                'emails' => $this->emails,
                'phones' => $this->phones,
                'addresses' => $this->addresses,
                'rodo' => $this->dataProcessingStatements,
            ];
        }

        echo "\nPobieram dane klientów dla $codesCount kodów w batch'ach po " . self::BATCH_SIZE;

        // Process in batches of BATCH_SIZE requests
        for ($i = 0; $i < $codesCount; $i += self::BATCH_SIZE) {
            $batchNumber = (int)($i / self::BATCH_SIZE) + 1;
            $totalBatches = (int)ceil($codesCount / self::BATCH_SIZE);
            echo "\nBatch $batchNumber / $totalBatches";

            $batchCodes = array_slice($clientCodes, $i, self::BATCH_SIZE);
            $urls = [];

            foreach ($batchCodes as $index => $code) {
                $urls[$index] = str_replace('{code}', $code, $this->endpoint);
                echo "\n  Kod: $code ----> " . ($i + $index + 1) . "/$codesCount";
            }

            $responses = $this->httpClient->requestMulti($this->source, $urls);

            foreach ($responses as $responseRaw) {
                $res = $this->decodeResponseFromRaw($responseRaw);
                if (!empty($res['code'])) {
                    $this->collectFeature($res, 'addresses');
                    $this->collectFeature($res, 'emails');
                    $this->collectFeature($res, 'phones');
                    $this->collectFeature($res, 'dataProcessingStatements');
                    array_push($this->fetchResult, $res);
                }
            }

            // Save when we have enough data
            if (count($this->fetchResult) >= $this->fetchLimit) {
                $this->save();
                $this->fetchResult = [];
            }
        }

        // Final save for any remaining results
        if (!empty($this->fetchResult)) {
            $this->save();
            $this->fetchResult = [];
        }

        return [
            'fetched' => $codesCount,
            'emails' => $this->emails,
            'phones' => $this->phones,
            'addresses' => $this->addresses,
            'rodo' => $this->dataProcessingStatements,
        ];
    }

    public function fetchByCode(string $code): array
    {
        $url = str_replace('{code}', $code, $this->endpoint);
        $res = $this->fetchApiResult($url);
        if (empty($res['code']))
            return [];

        return $res;
    }

    public function saveCustomer(array $customer)
    {
        $q = "INSERT INTO rogowiec_customer_archive (source, code, name, first_name, last_name, tax_number, personal_id, busines_number, kind)
            VALUES (:source, :code, :name, :first_name, :last_name, :tax_number, :personal_id, :busines_number, :kind)
                ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                first_name = VALUES(first_name),
                last_name = VALUES(last_name),
                tax_number = VALUES(tax_number),
                personal_id = VALUES(personal_id),
                busines_number = VALUES(busines_number),
                kind = VALUES(kind),
                fetch_date = NOW()
        ";
        $this->db->executeQuery($q, [
            'source' => $this->source->getName(),
            'code' => $customer['code'],
            'name' => $customer['name'],
            'first_name' => $customer['firstName'],
            'last_name' => $customer['lastName'],
            'tax_number' => $customer['taxNumber'],
            'personal_id' => $customer['personalId'],
            'busines_number' => $customer['businesNumber'],
            'kind' => $customer['kind'],
        ], [
            'source' => ParameterType::STRING,
            'code' => ParameterType::STRING,
            'name' => ParameterType::STRING,
            'first_name' => ParameterType::STRING,
            'last_name' => ParameterType::STRING,
            'tax_number' => ParameterType::STRING,
            'personal_id' => ParameterType::STRING,
            'busines_number' => ParameterType::STRING,
            'kind' => ParameterType::STRING,
        ]);

        if (!empty($customer['addresses'])) {
            foreach ($customer['addresses'] as $a) {
                $q = "INSERT INTO rogowiec_customer_address_archive (source, customer_code, country, city, street, postal_code, `number`)
                    VALUES (:source, :customer_code, :country, :city, :street, :postal_code, :number)
                        ON DUPLICATE KEY UPDATE
                        country = VALUES(country),
                        city = VALUES(city),
                        street = VALUES(street),
                        postal_code = VALUES(postal_code),
                        `number` = VALUES(`number`),
                        fetch_date = NOW()
                ";
                $this->db->executeQuery($q, [
                    'source' => $this->source->getName(),
                    'customer_code' => $customer['code'],
                    'country' => $a['country'],
                    'city' => $a['city'],
                    'street' => $a['street'],
                    'postal_code' => $a['postalCode'],
                    'number' => $a['number'],
                ], [
                    'source' => ParameterType::STRING,
                    'customer_code' => ParameterType::STRING,
                    'country' => ParameterType::STRING,
                    'city' => ParameterType::STRING,
                    'street' => ParameterType::STRING,
                    'postal_code' => ParameterType::STRING,
                    'number' => ParameterType::STRING,
                ]);
            }
        }

        if (!empty($customer['phones'])) {
            foreach ($customer['phones'] as $p) {
                $q = "INSERT INTO rogowiec_customer_phone_archive (source, customer_code, `number`, owner, is_default)
                    VALUES (:source, :customer_code, :number, :owner, :is_default)
                        ON DUPLICATE KEY UPDATE
                        `number` = VALUES(`number`),
                        owner = VALUES(owner),
                        is_default = VALUES(is_default),
                        fetch_date = NOW()
                ";
                $this->db->executeQuery($q, [
                    'source' => $this->source->getName(),
                    'customer_code' => $customer['code'],
                    'number' => $p['number'],
                    'owner' => $p['owner'],
                    'is_default' => (bool) $p['isDefault'],
                ], [
                    'source' => ParameterType::STRING,
                    'customer_code' => ParameterType::STRING,
                    'number' => ParameterType::STRING,
                    'owner' => ParameterType::STRING,
                    'is_default' => ParameterType::BOOLEAN,
                ]);
            }
        }

        if (!empty($customer['emails'])) {
            foreach ($customer['emails'] as $e) {
                $q = "INSERT INTO rogowiec_customer_email_archive (source, customer_code, address, owner, is_default)
                    VALUES (:source, :customer_code, :address, :owner, :is_default)
                        ON DUPLICATE KEY UPDATE
                        address = VALUES(address),
                        owner = VALUES(owner),
                        is_default = VALUES(is_default),
                        fetch_date = NOW()
                ";
                $this->db->executeQuery($q, [
                    'source' => $this->source->getName(),
                    'customer_code' => $customer['code'],
                    'address' => $e['address'],
                    'owner' => $e['owner'],
                    'is_default' => (bool) $e['isDefault'],
                ], [
                    'source' => ParameterType::STRING,
                    'customer_code' => ParameterType::STRING,
                    'address' => ParameterType::STRING,
                    'owner' => ParameterType::STRING,
                    'is_default' => ParameterType::BOOLEAN,
                ]);
            }
        }

        if (!empty($customer['dataProcessingStatements'])) {
            foreach ($customer['dataProcessingStatements'] as $d) {
                $q = "INSERT INTO rogowiec_customer_rodo_archive (source, customer_code, statement, date_statement, method_statement)
                    VALUES (:source, :customer_code, :statement, :date_statement, :method_statement)
                        ON DUPLICATE KEY UPDATE
                        statement = VALUES(statement),
                        date_statement = VALUES(date_statement),
                        method_statement = VALUES(method_statement),
                        fetch_date = NOW()
                ";
                $this->db->executeQuery($q, [
                    'source' => $this->source->getName(),
                    'customer_code' => $customer['code'],
                    'statement' => $d['statement'],
                    'date_statement' => !empty($d['dateStatement']) ? date('Y-m-d H:i:s', strtotime($d['dateStatement'])) : null,
                    'method_statement' => $d['methodStatement'],
                ], [
                    'source' => ParameterType::STRING,
                    'customer_code' => ParameterType::STRING,
                    'statement' => ParameterType::STRING,
                    'date_statement' => ParameterType::STRING,
                    'method_statement' => ParameterType::STRING,
                ]);
            }
        }
    }

    public function archive()
    {
        $q = "INSERT INTO rogowiec_customer_archive (source, code, name, first_name, last_name, tax_number, personal_id, busines_number, kind)
            SELECT source, code, name, first_name, last_name, tax_number, personal_id, busines_number, kind FROM rogowiec_customer r
                ON DUPLICATE KEY UPDATE
                name = r.name,
                first_name = r.first_name,
                last_name = r.last_name,
                tax_number = r.tax_number,
                personal_id = r.personal_id,
                busines_number = r.busines_number,
                kind = r.kind,
                fetch_date = NOW()
        ";
        $this->db->executeQuery($q);

        $q = "INSERT INTO rogowiec_customer_address_archive (source, customer_code, country, city, street, postal_code, `number`)
            SELECT source, customer_code, country, city, street, postal_code, `number` FROM rogowiec_customer_address r
                ON DUPLICATE KEY UPDATE
                country = r.country,
                city = r.city,
                street = r.street,
                postal_code = r.postal_code,
                number = r.number,
                fetch_date = NOW()
        ";
        $this->db->executeQuery($q);

        $q = "INSERT INTO rogowiec_customer_email_archive (source, customer_code, address, owner, is_default)
            SELECT source, customer_code, address, owner, is_default FROM rogowiec_customer_email r
                ON DUPLICATE KEY UPDATE
                address = r.address,
                owner = r.owner,
                is_default = r.is_default,
                fetch_date = NOW()
        ";
        $this->db->executeQuery($q);

        $q = "INSERT INTO rogowiec_customer_phone_archive (source, customer_code, `number`, owner, is_default)
            SELECT source, customer_code, number, owner, is_default FROM rogowiec_customer_phone r
                ON DUPLICATE KEY UPDATE
                number = r.number,
                owner = r.owner,
                is_default = r.is_default,
                fetch_date = NOW()
        ";
        $this->db->executeQuery($q);
    }

    private function decodeResponseFromRaw($raw): array
    {
        if (empty($raw) || $raw === false) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function collectFeature(&$client, $feature)
    {
        if (isset($client[$feature])) {
            foreach ($client[$feature] as $a) {
                if (!empty($a)) {
                    $a['customer_code'] = $client['code'];
                    array_push($this->$feature, $a);
                }
                unset($client[$feature]);
            }
        }
    }

    private function fetchClientCodes()
    {
        $q = "SELECT DISTINCT kod_klienta AS code FROM (
                SELECT DISTINCT kod_klienta FROM rogowiec_cars_orders WHERE source = :source
                UNION
                SELECT DISTINCT kod_klienta FROM rogowiec_ageing_production WHERE source = :source
                UNION
                SELECT DISTINCT kod_nabywca FROM rogowiec_cars_sold WHERE source = :source
                UNION
                SELECT DISTINCT kod_odbiorca FROM rogowiec_cars_sold WHERE source = :source
                UNION
                SELECT DISTINCT kod_klienta FROM rogowiec_parts_sold WHERE source = :source
                UNION
                SELECT DISTINCT kod_klienta FROM rogowiec_service_sold WHERE source = :source
                UNION
                SELECT DISTINCT customer_code AS kod_klienta FROM rogowiec_invoice_customer_archive WHERE source = :source
                UNION
                SELECT DISTINCT customer_code FROM rogowiec_invoice_archive WHERE source = :source
            ) uq
            WHERE kod_klienta != ''
            AND kod_klienta NOT IN (SELECT code FROM rogowiec_customer_archive WHERE source = :source)
        ";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'code' => ['sourceField' => 'code', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'first_name' => ['sourceField' => 'firstName', 'type' => ParameterType::STRING],
            'last_name' => ['sourceField' => 'lastName', 'type' => ParameterType::STRING],
            'tax_number' => ['sourceField' => 'taxNumber', 'type' => ParameterType::STRING],
            'personal_id' => ['sourceField' => 'personalId', 'type' => ParameterType::STRING],
            'busines_number' => ['sourceField' => 'businesNumber', 'type' => ParameterType::STRING],
            'kind' => ['sourceField' => 'kind', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->addresses = [];
        $this->phones = [];
        $this->emails = [];
        $this->dataProcessingStatements = [];
    }
}
