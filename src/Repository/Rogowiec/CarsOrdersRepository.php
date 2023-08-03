<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CarsOrdersRepository extends IApiRepository
{
    private $endpoint = '/wdf/Reports/vehicleOrderList?id_oddzial={id_oddzial}&data_od={date_from}&data_do={date_to}';
    protected $table = 'rogowiec_cars_orders';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $branchesIds = $this->fetchBranchId();
        $branchesCount = count($branchesIds);
        $resCount = 0;

        if ($branchesCount) {
            $i = 1;

            foreach ($branchesIds as $id) {
                echo "\nId jednostki $id ----> $i/$branchesCount";
                $url = str_replace(
                    ['{id_oddzial}', '{date_from}', '{date_to}'],
                    [$id, $this->dateFrom, $this->dateTo],
                    $this->endpoint
                );

                $this->fetchResult = $this->fetchApiResult($url);
                $resCount += count($this->fetchResult);
                $this->save();
                $this->clearDataArrays();
                $i++;
            }
        } else 
            throw new \Exception("Nie żadnych . Najpierw uruchom komendę pobierającą listę oddziałów [rogowiec:branch]", 99);

        return ['fetched' => $resCount];
    }

    private function fetchBranchId()
    {
        
        $q = "SELECT dms_id FROM rogowiec_branch WHERE source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'id_zam' => ['sourceField' => 'Id', 'type' => ParameterType::INTEGER],
            'numer_zam' => ['sourceField' => 'NumerZam', 'type' => ParameterType::STRING],
            'data' => ['sourceField' => 'Data', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'marka' => ['sourceField' => 'Marka', 'type' => ParameterType::STRING],
            'opis_samochodu' => ['sourceField' => 'OpisSam', 'type' => ParameterType::STRING],
            'kod_klienta' => ['sourceField' => 'KodKlienta', 'type' => ParameterType::STRING],
            'wartosc_brutto' => ['sourceField' => 'WartBrutto', 'type' => ParameterType::STRING],
            'data_plan_odbior' => ['sourceField' => 'DataOdbPlan', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'stan_zamowienia' => ['sourceField' => 'StanZam', 'type' => ParameterType::STRING],
            'status_zamowienia' => ['sourceField' => 'StatusZam', 'type' => ParameterType::STRING],
            'zamkniete' => ['sourceField' => 'Zamkniete', 'type' => ParameterType::STRING],
            'sprzedawca' => ['sourceField' => 'Sprzedawca', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'Vin', 'type' => ParameterType::STRING],
            'id_samochodu' => ['sourceField' => 'VehicleId', 'type' => ParameterType::INTEGER],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
