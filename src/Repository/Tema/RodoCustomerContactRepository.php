<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class RodoCustomerContactRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/customers/{customerId}/contacts/{contactId}/privacy-agreements';
    protected $table = 'tema_contact_rodo';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $contacts = $this->getCustomerContacts();
        $contactsCount = count($contacts);

        if ($contactsCount) {
            $i = 1;

            foreach ($contacts as $c) {
                $customerId = $c['customer_id'];
                $contactId = $c['contact_id'];
                echo "\nId klienta $customerId, id kontaktu $contactId ----> $i/$contactsCount";

                $url = str_replace(
                    ['{customerId}', '{contactId}'],
                    [$customerId, $contactId],
                    $this->endpoint
                );
                $res = $this->fetchApiResult($url);

                if (!empty($res)) {
                    foreach ($res as $r) {
                        $r['customer_id'] = $customerId;
                        $r['contact_id'] = $contactId;
                        array_push($this->fetchResult, $r);
                    }
                }
                $i++;
            }
            $this->save();
        } else 
            throw new \Exception("Nie żadnych klientów. Najpierw uruchom komendę pobierającą listę klientów [tema:customer]", 99);

        $resCount = count($this->fetchResult);
        $this->clearDataArrays();
        return ['fetched' => $resCount];
    }

    protected function getFieldsParams(): array
    {
        return [
            'customer_id' => ['sourceField' => 'customer_id', 'type' => ParameterType::STRING],
            'contact_id' => ['sourceField' => 'contact_id', 'type' => ParameterType::STRING],
            'value' => ['sourceField' => 'value', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'date' => ['sourceField' => 'date', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'modify_date' => ['sourceField' => 'modifyDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'type_id' => ['sourceField' => 'typeId', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }

    private function getCustomerContacts()
    {
        $q = "SELECT DISTINCT customer_id, contact_id FROM tema_customer_contact WHERE source = :source ORDER BY customer_id";
        return $this->db->fetchAllAssociative($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }
}
