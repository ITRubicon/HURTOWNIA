<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CustomerContactRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/customers/{customerId}/contacts';
    protected $table = 'tema_customer_contact';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $customersIds = $this->getCustomers();
        $customersCount = count($customersIds);
        if ($customersCount) {
            $i = 1;

            foreach ($customersIds as $id) {
                echo "\nId klienta $id ----> $i/$customersCount";

                $url = str_replace('{customerId}', $id, $this->endpoint);
                $res = $this->fetchApiResult($url);

                if (!empty($res)) {
                    foreach ($res as $r) {
                        $r['customer_id'] = $id;
                        array_push($this->fetchResult, $r);
                    }
                }
                $i++;

                if (count($this->fetchResult) >= $this->fetchLimit) {
                    $this->save();
                    $this->fetchResult = [];
                }
            }
            $this->save();
        } else 
            throw new \Exception("Nie znaleziono klentów. Najpierw uruchom komendę pobierającą listę klientóœ [tema:customer]", 99);

        $resCount = count($this->fetchResult);
        $this->clearDataArrays();
        return ['fetched' => $resCount];
    }

    public function saveContacts(array $contacts)
    {
        $this->fetchResult = $contacts;
        $this->save();
    }

    protected function getFieldsParams(): array
    {
        return [
            'customer_id' => ['sourceField' => 'customer_id', 'type' => ParameterType::STRING],
            'row_id' => ['sourceField' => 'rowId', 'type' => ParameterType::INTEGER],
            'contact_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'personal_id' => ['sourceField' => 'personalId', 'type' => ParameterType::STRING],
            'first_name' => ['sourceField' => 'firstName', 'type' => ParameterType::STRING],
            'last_name' => ['sourceField' => 'lastName', 'type' => ParameterType::STRING],
            'email' => ['sourceField' => 'email', 'type' => ParameterType::STRING],
            'phone_number' => ['sourceField' => 'phoneNumber', 'type' => ParameterType::STRING],
            'is_default' => ['sourceField' => 'isDefault', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    private function getCustomers()
    {
        $q = "SELECT DISTINCT customer_id AS customer_id FROM tema_customer WHERE source = :source ORDER BY customer_id";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }
}
