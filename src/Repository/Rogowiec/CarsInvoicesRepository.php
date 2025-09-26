<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CarsInvoicesRepository extends IApiRepository
{
    private $endpoint = '/wdf/Reports/vehicleInvoiceList?VIN={vin}&id_oddzial={branch_id}';
    protected $table = 'rogowiec_car_invoices';
    protected $onDuplicateClause = 'ON DUPLICATE KEY UPDATE 
        vehicle_id = VALUES(vehicle_id),
        fv_data = VALUES(fv_data),
        fv_data_wplyw = VALUES(fv_data_wplyw),
        wartosc = VALUES(wartosc),
        faktura_rodzaj = VALUES(faktura_rodzaj),
        kod_typu_korekty = VALUES(kod_typu_korekty),
        typ_korekty = VALUES(typ_korekty)
    ';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $vinBranches = $this->fetchBranchWithVin();
        $vinCount = count($vinBranches);
        $resCount = 0;

        if ($vinCount) {
            $i = 1;

            foreach ($vinBranches as $vb) {
                echo "\nId jednostki {$vb['branch_id']}, VIN: {$vb['vin']} ----> $i/$vinCount";
                $url = str_replace(
                    ['{vin}', '{branch_id}'],
                    [$vb['vin'], $vb['branch_id']],
                    $this->endpoint
                );
                $this->fetchResult = $this->fetchApiResult($url);
                $resCount += count($this->fetchResult);
                $this->save();
                $this->clearDataArrays();
                $i++;
            }
        } else
            throw new \Exception("Nie żadnych oddziałów lub VINów dla " . $this->source->getName() . ". Najpierw uruchom komendę pobierającą listę oddziałów [rogowiec:branch] i sprzedanych samochodów [rogowiec:cars:sold]", 99);

        return ['fetched' => $resCount];
    }

    private function fetchBranchWithVin()
    {
        $q = "SELECT
                b.source,
                b.dms_id AS branch_id,
                c.vin
            FROM prehurtownia.rogowiec_branch b
            JOIN prehurtownia.rogowiec_cars_sold c ON c.source = b.source AND SUBSTRING(c.fv_numer, 1, 1) = CAST(b.oddzial_id AS UNSIGNED)
            WHERE b.source = :source
        ";
        return $this->db->fetchAllAssociative($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'vehicle_id' => ['sourceField' => 'VehicleId', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'VIN', 'type' => ParameterType::STRING],
            'fv_numer' => ['sourceField' => 'NumerFaktury', 'type' => ParameterType::STRING],
            'fv_data' => ['sourceField' => 'DataWystawieniaFaktury', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'fv_data_wplyw' => ['sourceField' => 'DataWplywuFaktury', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d']],
            'wartosc' => ['sourceField' => 'WartoscFaktury', 'type' => ParameterType::STRING],
            'faktura_rodzaj' => ['sourceField' => 'RodzajFaktury', 'type' => ParameterType::STRING],
            'kod_typu_korekty' => ['sourceField' => 'KodTypuKorekty', 'type' => ParameterType::STRING],
            'typ_korekty' => ['sourceField' => 'TypKorekty', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
