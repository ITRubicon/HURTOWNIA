<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class FkKontrahenciRepository extends IApiRepository
{
    private string $endpoint = '/api/accounting/v3/01/contractors';
    protected $table = 'tema_fk_kontrahenci';
    private $bankAccounts = [];

    public function fetch(): array
    {
        $this->clearDataArrays();
        $resCount = 0;
        $res = [];

        do {
            $nextTimestamp = '';            
            if (!empty($res['lastTimestamp']))
                $nextTimestamp = '?ts=' . urlencode($res['lastTimestamp']);
            
            $res = $this->fetchApiResult($this->endpoint . $nextTimestamp);
            if (empty($res))
                continue;

            $this->collectBankAccounts($res['items']);
            $this->fetchResult = array_merge($this->fetchResult, $res['items']);
            $resCount += count($res['items']);

            if (count($this->fetchResult) >= $this->fetchLimit) {
                $this->save();
                $this->fetchResult = [];
            }
        } while ($res['fetchNext']);
        $this->save();

        return [
            'fetched' => $resCount,
            'bank_accounts' => $this->bankAccounts
        ];
    }
    private function collectBankAccounts(array &$customers)
    {
        foreach ($customers as $i => $c) {
            foreach ($c['bankAccounts'] as $account) {
                $account['kontrahent_id'] = $c['id'];
                array_push($this->bankAccounts, $account);
                unset($customers[$i]['bankAccounts']);
            }
        }
    }

    protected function getFieldsParams(): array
    {
        return [
            'kontrahent_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'row_id' => ['sourceField' => 'rowId', 'type' => ParameterType::INTEGER],
            'vat_id' => ['sourceField' => 'vatId', 'type' => ParameterType::STRING],
            'is_vat_payer' => ['sourceField' => 'isVatPayer', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'payer_erp_id' => ['sourceField' => 'payerErpId', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'address' => ['sourceField' => 'address', 'type' => ParameterType::STRING],
            'post_code' => ['sourceField' => 'postCode', 'type' => ParameterType::STRING],
            'city' => ['sourceField' => 'city', 'type' => ParameterType::STRING],
            'country' => ['sourceField' => 'countryName', 'type' => ParameterType::STRING],
            'phone_number' => ['sourceField' => 'phoneNumber', 'type' => ParameterType::STRING],
            'email' => ['sourceField' => 'email', 'type' => ParameterType::STRING],
            'payment_days' => ['sourceField' => 'paymentDays', 'type' => ParameterType::INTEGER],
            'payment_method' => ['sourceField' => 'paymentMethod', 'type' => ParameterType::STRING],
            'grupa' => ['sourceField' => 'group', 'type' => ParameterType::STRING],
            'route_id' => ['sourceField' => 'routeId', 'type' => ParameterType::INTEGER],
            'route_order' => ['sourceField' => 'routeOrder', 'type' => ParameterType::INTEGER],
            'debt_limit' => ['sourceField' => 'debtLimit', 'type' => ParameterType::INTEGER],
            'salesman_id' => ['sourceField' => 'salesmanId', 'type' => ParameterType::INTEGER],
            'type' => ['sourceField' => 'type', 'type' => ParameterType::STRING],
            'delivery_note_type' => ['sourceField' => 'deliveryNoteType', 'type' => ParameterType::STRING],
            'activity' => ['sourceField' => 'activity', 'type' => ParameterType::STRING],
            'check_maximum_discount' => ['sourceField' => 'checkMaximumDiscount', 'type' => ParameterType::INTEGER],
            'priority' => ['sourceField' => 'priority', 'type' => ParameterType::INTEGER],
            'separate_invoice' => ['sourceField' => 'separateInvoice', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
            'region' => ['sourceField' => 'region', 'type' => ParameterType::STRING],
            'beer_sale_permission_no' => ['sourceField' => 'beerSalePermissionNo', 'type' => ParameterType::STRING],
            'beer_sale_permission_date' => ['sourceField' => 'beerSalePermissionDate', 'type' => ParameterType::STRING],
            'beer_sale_permission_expiration_date' => ['sourceField' => 'beerSalePermissionExpirationDate', 'type' => ParameterType::STRING],
            'wine_sale_permission_no' => ['sourceField' => 'wineSalePermissionNo', 'type' => ParameterType::STRING],
            'wine_sale_permission_date' => ['sourceField' => 'wineSalePermissionDate', 'type' => ParameterType::STRING],
            'wine_sale_permission_expiration_date' => ['sourceField' => 'wineSalePermissionExpirationDate', 'type' => ParameterType::STRING],
            'vodka_sale_permission_no' => ['sourceField' => 'vodkaSalePermissionNo', 'type' => ParameterType::STRING],
            'vodka_sale_permission_date' => ['sourceField' => 'vodkaSalePermissionDate', 'type' => ParameterType::STRING],
            'vodka_sale_permission_expiration_date' => ['sourceField' => 'vodkaSalePermissionExpirationDate', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->bankAccounts = [];
    }
}
