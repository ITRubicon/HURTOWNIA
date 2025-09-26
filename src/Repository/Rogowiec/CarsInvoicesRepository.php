<?php

namespace App\Repository\Rogowiec;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CarsInvoicesRepository extends IApiRepository
{
    private $endpoint = '/wdf/Reports/vehicleInvoiceList?VIN={vin}&id_oddzial={branch_id}';
    protected $table = 'rogowiec_car_invoices';
    private const BATCH_SIZE = 5; // Number of simultaneous requests per batch
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
            echo "\nPobieram faktury samochodów w batch'ach po " . self::BATCH_SIZE;
            $totalBatches = (int)ceil($vinCount / self::BATCH_SIZE);

            for ($i = 0; $i < $vinCount; $i += self::BATCH_SIZE) {
                $batchNumber = (int)($i / self::BATCH_SIZE) + 1;
                echo "\nBatch $batchNumber / $totalBatches";

                $batchVinBranches = array_slice($vinBranches, $i, self::BATCH_SIZE);
                $urls = [];

                // Prepare URLs for the batch
                foreach ($batchVinBranches as $index => $vb) {
                    $url = str_replace(
                        ['{vin}', '{branch_id}'],
                        [$vb['vin'], $vb['branch_id']],
                        $this->endpoint
                    );
                    $urls[$index] = $url;
                    echo "\nId jednostki {$vb['branch_id']}, VIN: {$vb['vin']} ----> " . ($i + $index + 1) . "/$vinCount";
                }

                // Fetch multiple endpoints in parallel
                $responses = $this->httpClient->requestMulti($this->source, $urls);

                // Process responses
                foreach ($responses as $responseRaw) {
                    $response = $this->decodeResponseFromRaw($responseRaw);
                    if (!empty($response)) {
                        $this->fetchResult = array_merge($this->fetchResult, $response);
                        $resCount += count($response);
                    }
                }

                // Save when we have enough data or at the end of processing
                if (count($this->fetchResult) >= $this->fetchLimit || ($i + self::BATCH_SIZE) >= $vinCount) {
                    $this->save();
                    $this->clearDataArrays();
                }
            }
        } else {
            throw new \Exception("Nie żadnych oddziałów lub VINów dla " . $this->source->getName() . ". Najpierw uruchom komendę pobierającą listę oddziałów [rogowiec:branch] i sprzedanych samochodów [rogowiec:cars:sold]", 99);
        }

        return ['fetched' => $resCount];
    }

    // Helper to decode raw response (since fetchApiResult is not used directly)
    private function decodeResponseFromRaw($raw): array
    {
        if (empty($raw) || $raw === false) {
            return [];
        }

        if (str_contains($raw, 'Us³ugaPunktSprzedazy')) {
            $raw = preg_replace('/Us³ugaPunktSprzedazy/', 'UsługaPunktSprzedazy', $raw);
        }
        
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
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
