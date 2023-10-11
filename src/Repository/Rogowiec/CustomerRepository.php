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

    public function fetch(): array
    {
        $this->clearDataArrays();
        $clientCodes = $this->fetchClientCodes();
        $codesCount = count($clientCodes);
        $i = 0;

        for ($i = 0; $i < $codesCount; $i++) {
            echo "\nKlient " . $i + 1 . "/$codesCount";
            $url = str_replace('{code}', $clientCodes[$i], $this->endpoint);
            $res = $this->fetchApiResult($url);
            $this->collectFeature($res, 'addresses');
            $this->collectFeature($res, 'emails');
            $this->collectFeature($res, 'phones');
            $this->collectFeature($res, 'dataProcessingStatements');
            array_push($this->fetchResult, $res);
            unset($clientCodes[$i]);
        }

        $this->save();

        return [
            'fetched' => $codesCount,
            'emails' => $this->emails,
            'phones' => $this->phones,
            'addresses' => $this->addresses,
            'rodo' => $this->dataProcessingStatements,
        ];
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
                kind = r.kind
        ";
        $this->db->executeQuery($q);

        $q = "INSERT INTO rogowiec_customer_address_archive (source, customer_code, country, city, street, postal_code, `number`)
            SELECT source, customer_code, country, city, street, postal_code, `number` FROM rogowiec_customer_address r
                ON DUPLICATE KEY UPDATE
                country = r.country,
                city = r.city,
                street = r.street,
                postal_code = r.postal_code,
                number = r.number
        ";
        $this->db->executeQuery($q);

        $q = "INSERT INTO rogowiec_customer_email_archive (source, customer_code, address, owner, is_default)
            SELECT source, customer_code, address, owner, is_default FROM rogowiec_customer_email r
                ON DUPLICATE KEY UPDATE
                address = r.address,
                owner = r.owner,
                is_default = r.is_default
        ";
        $this->db->executeQuery($q);
        
        $q = "INSERT INTO rogowiec_customer_phone_archive (source, customer_code, `number`, owner, is_default)
            SELECT source, customer_code, number, owner, is_default FROM rogowiec_customer_phone r
                ON DUPLICATE KEY UPDATE
                number = r.number,
                owner = r.owner,
                is_default = r.is_default
        ";
        $this->db->executeQuery($q);
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
