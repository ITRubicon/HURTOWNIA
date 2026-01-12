<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class InvoiceCustomerRepository extends IApiRepository
{
    private $customerRepo;
    protected $table = 'rogowiec_invoice_customer';

    public function setCustomerRepository(CustomerRepository $customerRepo)
    {
        $this->customerRepo = $customerRepo;
        return $this;
    }

    public function saveCustomers(array $customers)
    {
        $this->clearDataArrays();
        $this->fetchResult = $customers;
        $this->fetchCustomerData($customers);
        $this->save();
        $this->archive();
        $this->clearDataArrays();
    }

    public function archive()
    {
        $q = "INSERT INTO rogowiec_invoice_customer_archive (customer_code, name, first_name, last_name, tax_number, personal_id, busines_number, kind, customer_kind, invoice_id, source)
            SELECT customer_code, name, first_name, last_name, tax_number, personal_id, busines_number, kind, customer_kind, invoice_id, source
            FROM rogowiec_invoice_customer ric WHERE source = :source
                ON duplicate KEY UPDATE
                customer_code = ric.customer_code,
                name = ric.name,
                first_name = ric.first_name,
                last_name = ric.last_name,
                tax_number = ric.tax_number,
                personal_id = ric.personal_id,
                busines_number = ric.busines_number,
                kind = ric.kind,
                customer_kind = ric.customer_kind
        ";
        $this->db->executeQuery($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'customer_code' => ['sourceField' => 'code', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'first_name' => ['sourceField' => 'firstName', 'type' => ParameterType::STRING],
            'last_name' => ['sourceField' => 'lastName', 'type' => ParameterType::STRING],
            'tax_number' => ['sourceField' => 'taxNumber', 'type' => ParameterType::STRING],
            'personal_id' => ['sourceField' => 'personalId', 'type' => ParameterType::STRING],
            'busines_number' => ['sourceField' => 'businesNumber', 'type' => ParameterType::STRING],
            'kind' => ['sourceField' => 'kind', 'type' => ParameterType::STRING],
            'customer_kind' => ['sourceField' => 'customer_kind', 'type' => ParameterType::STRING],
            'invoice_id' => ['sourceField' => 'invoice_id', 'type' => ParameterType::INTEGER],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    private function fetchCustomerData(array $customers)
    {
        $uniqueCustomers = array_unique(array_column($customers, 'code'));
        
        foreach ($uniqueCustomers as $c) {
            $customer = $this->customerRepo->fetchByCode($c);
            if ($customer) {
                echo "Znaleziono klienta: {$customer['code']}. Zapisuję...\n";
                $this->customerRepo->saveCustomer($customer);
            }
        }
    }
}
