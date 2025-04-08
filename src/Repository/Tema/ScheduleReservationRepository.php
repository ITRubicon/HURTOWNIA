<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ScheduleReservationRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/schedule/{branchId}/reservation?id={reservationId}';
    protected $table = 'tema_schedule_reservation';

    public function fetch(): array
    {
        $this->onDuplicateClause = '
            ON DUPLICATE KEY UPDATE
                number = VALUES(number),
                repair_order_number = VALUES(repair_order_number),
                customer = VALUES(customer),
                customer_name = VALUES(customer_name)
        ';

        $this->clearDataArrays();
        $resCount = 0;

        foreach ($this->findReservationsInSchedule() as $reservationId => $branchId) {
            $this->clearDataArrays();

            $url = str_replace(
                ['{reservationId}', '{branchId}'],
                [$reservationId, $branchId],
                $this->endpoint
            );

            $this->fetchResult = $this->fetchApiResult($url);
            if (empty($this->fetchResult)) {
                continue;
            }
            
            $resCount += count($this->fetchResult);
            $this->save();
            $this->fetchResult = [];
            gc_collect_cycles();
        }
        
        return ['fetched' => $resCount];
    }

    private function findReservationsInSchedule(): \Generator
    {        
        $q = "SELECT DISTINCT
                SUBSTRING_INDEX(related_reservation, '=', -1) AS reservation_id,
                SUBSTRING(related_reservation, POSITION('=' IN related_reservation)+3, 2) AS branch_id
            FROM tema_schedule_items
            WHERE CAST(date_from AS date) BETWEEN :dateFrom AND :dateTo
                AND related_reservation != ''
        ";
        
        return $this->db->iterateKeyValue($q, ['dateFrom' => $this->dateFrom, 'dateTo' => $this->dateTo]);
    }

    protected function getFieldsParams(): array
    {
        return [
            'id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'number' => ['sourceField' => 'number', 'type' => ParameterType::STRING],
            'repair_order_number' => ['sourceField' => 'repairOrderNumber', 'type' => ParameterType::STRING],
            'customer' => ['sourceField' => 'customer', 'type' => ParameterType::STRING],
            'customer_name' => ['sourceField' => 'customerName', 'type' => ParameterType::STRING],
            // 'reservation_remarks' => ['sourceField' => 'reservationRemarks', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
