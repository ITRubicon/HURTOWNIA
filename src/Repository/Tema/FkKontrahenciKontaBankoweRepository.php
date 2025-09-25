<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class FkKontrahenciKontaBankoweRepository extends IApiRepository
{
    private string $endpoint = '';
    protected $table = 'tema_fk_kontrahenci_konta_bankowe';

    public function saveItems(array $items): int
    {
        $this->clearDataArrays();
        
        $this->fetchResult = $items;
        $this->save();
        $resCount = count($this->fetchResult);
        $this->clearDataArrays();

        return $resCount;
    }

    protected function getFieldsParams(): array
    {
        return [
            'account_number' => ['sourceField' => 'accountNumber', 'type' => ParameterType::STRING],
            'bank_name' => ['sourceField' => 'bankName', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
            'kontrahent_id' => ['sourceField' => 'kontrahent_id', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];   
    }
}
