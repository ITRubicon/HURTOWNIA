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
        $branches = $this->findBrachesId();
        $resCount = 0;
        $totalBranches = count($branches);
        if ($totalBranches === 0) {
            throw new \Exception("Brak oddziałów dla " . $this->source->getName() . ". Najpierw uruchom komendę pobierającą listę oddziałów [rogowiec:branch]", 99);
        }
        $branchIdx = 1;
        foreach ($branches as $branch) {
            $branchId = $branch['branch_id'];
            echo "\nPrzetwarzam oddział dms_id={$branchId} ($branchIdx/$totalBranches)";
            // Fetch VINs for this branch
            $vins = $this->fetchVinsForBranch($branchId);
            $vinCount = count($vins);
            if ($vinCount === 0) {
                echo "\nBrak VINów dla oddziału $branchId";
                $branchIdx++;
                continue;
            }
            echo "\nPobieram faktury samochodów w batch'ach po " . self::BATCH_SIZE . " (oddział $branchId, VINów: $vinCount)";
            $totalBatches = (int)ceil($vinCount / self::BATCH_SIZE);
            for ($i = 0; $i < $vinCount; $i += self::BATCH_SIZE) {
                $batchNumber = (int)($i / self::BATCH_SIZE) + 1;
                echo "\nBatch $batchNumber / $totalBatches (oddział $branchId)";
                $batchVins = array_slice($vins, $i, self::BATCH_SIZE);
                $urls = [];
                foreach ($batchVins as $index => $vin) {
                    $url = str_replace(
                        ['{vin}', '{branch_id}'],
                        [$vin, $branchId],
                        $this->endpoint
                    );
                    $urls[$index] = $url;
                    echo "\nId jednostki $branchId, VIN: $vin ----> " . ($i + $index + 1) . "/$vinCount";
                }
                $responses = $this->httpClient->requestMulti($this->source, $urls);
                foreach ($responses as $responseRaw) {
                    $response = $this->decodeResponseFromRaw($responseRaw);
                    if (!empty($response)) {
                        $this->fetchResult = array_merge($this->fetchResult, $response);
                        $resCount += count($response);
                    }
                }
                if (count($this->fetchResult) >= $this->fetchLimit) {
                    $this->save();
                    $this->clearDataArrays();
                }
            }
            if (!empty($this->fetchResult)) {
                $this->save();
                $this->clearDataArrays();
            }
            $branchIdx++;
        }
        return ['fetched' => $resCount];
    // Fetch VINs for a given branch
    private function fetchVinsForBranch($branchId)
    {
        $q = "SELECT vin FROM prehurtownia.rogowiec_cars_sold WHERE source = :source AND SUBSTRING(fv_numer, 1, 1) = CAST((SELECT oddzial_id FROM prehurtownia.rogowiec_branch WHERE dms_id = :branchId AND source = :source LIMIT 1) AS UNSIGNED)";
        $result = $this->db->fetchAllAssociative($q, [
            'source' => $this->source->getName(),
            'branchId' => $branchId
        ], [
            'source' => ParameterType::STRING,
            'branchId' => ParameterType::STRING
        ]);
        return array_column($result, 'vin');
    }
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

    private function findBrachesId()
    {
        $q = "SELECT
                b.source,
                b.dms_id AS branch_id
            FROM prehurtownia.rogowiec_branch b
            WHERE b.source = :source
            ORDER BY b.dms_id
        ";
        return $this->db->fetchAllAssociative($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
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
    // Fetch VINs for a given branch
    private function fetchVinsForBranch($branchId)
    {
        $q = "SELECT vin FROM prehurtownia.rogowiec_cars_sold WHERE source = :source AND SUBSTRING(fv_numer, 1, 1) = CAST((SELECT oddzial_id FROM prehurtownia.rogowiec_branch WHERE dms_id = :branchId AND source = :source LIMIT 1) AS UNSIGNED)";
        $result = $this->db->fetchAllAssociative($q, [
            'source' => $this->source->getName(),
            'branchId' => $branchId
        ], [
            'source' => ParameterType::STRING,
            'branchId' => ParameterType::STRING
        ]);
        return array_column($result, 'vin');
    }
}
