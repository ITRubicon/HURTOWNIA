<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CustomerRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/customers/:sync';
    private $contacts = [];
    protected $table = 'tema_customer';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $resCount = 0;
        $res = [];
        do {
            $nextTimestamp = '';            
            if (!empty($res['lastTimestamp']))
                $nextTimestamp = '?timestamp=' . urlencode($res['lastTimestamp']);

            $res = $this->fetchApiResult($this->endpoint . $nextTimestamp);
            if (empty($res))
                continue;

            $this->getClientData($res['items']);
            $this->fetchResult = $res['items'];
            $this->save();
            $this->fetchResult = [];
            $resCount += count($res['items']);
        } while ($res['fetchNext']);

        return [
            'fetched' => $resCount,
            'contacts' => $this->contacts
        ];
    }

    private function getClientData(array &$customers)
    {
        foreach ($customers as $i => $c) {
            foreach ($c['contacts'] as $contact) {
                $contact['customer_id'] = $c['id'];
                array_push($this->contacts, $contact);
                unset($customers[$i]['contacts']);
            }
        }
    }

    protected function getFieldsParams(): array
    {
        return [
            'row_id' => ['sourceField' => 'rowId', 'type' => ParameterType::INTEGER],
            'customer_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'vat_id' => ['sourceField' => 'vatId', 'type' => ParameterType::STRING],
            'personal_id' => ['sourceField' => 'personalId', 'type' => ParameterType::STRING],
            'krs_id' => ['sourceField' => 'krsId', 'type' => ParameterType::STRING],
            'regon_id' => ['sourceField' => 'regonId', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'address' => ['sourceField' => 'address', 'type' => ParameterType::STRING],
            'post_code' => ['sourceField' => 'postCode', 'type' => ParameterType::STRING],
            'city' => ['sourceField' => 'city', 'type' => ParameterType::STRING],
            'country_code' => ['sourceField' => 'countryCode', 'type' => ParameterType::STRING],
            'country_name' => ['sourceField' => 'countryName', 'type' => ParameterType::STRING],
            'phone_number' => ['sourceField' => 'phoneNumber', 'type' => ParameterType::STRING],
            'email' => ['sourceField' => 'email', 'type' => ParameterType::STRING],
            'type' => ['sourceField' => 'type', 'type' => ParameterType::STRING],
            'modify_date' => ['sourceField' => 'modifyDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'renault_person_id' => ['sourceField' => 'renaultPersonId', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->contacts = [];
    }
}
