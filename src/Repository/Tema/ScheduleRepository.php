<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ScheduleRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/schedule/items-data?dateFrom={dateFrom}&dateTo={dateTo}';
    protected $table = 'tema_schedule_items';

    public function fetch(): array
    {
        $this->onDuplicateClause = '
            ON DUPLICATE KEY UPDATE
                related_reservation = VALUES(related_reservation),
                warehouse_id = VALUES(warehouse_id),
                date_from = VALUES(date_from),
                date_to = VALUES(date_to),
                title = VALUES(title),
                last_modified_by_operator = VALUES(last_modified_by_operator),
                created_by_operator = VALUES(created_by_operator),
                type = VALUES(type)
        ';
        $this->clearDataArrays();
        $periods = $this->createMonthPeriods();
        $resCount = 0;

        foreach ($periods as $period) {
            $this->clearDataArrays();

            $url = str_replace(
                ['{dateFrom}', '{dateTo}'],
                [$period['from'], $period['to']],
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

    private function createMonthPeriods(): array
    {
        $periods = [];
        $start = new \DateTime($this->dateFrom);
        $end = new \DateTime($this->dateTo);
        
        while ($start <= $end) {
            $periods[] = [
                'from' => $start->format('Y-m-01'),
                'to' => $start->format('Y-m-t'),
            ];
            $start->modify('+1 month');
        }

        return $periods;
    }

    protected function getFieldsParams(): array
    {
        return [
            'id' => ['sourceField' => 'id', 'type' => ParameterType::INTEGER],
            'related_reservation' => ['sourceField' => 'relatedReservation', 'type' => ParameterType::STRING],
            'warehouse_id' => ['sourceField' => 'warehouseId', 'type' => ParameterType::STRING],
            'date_from' => ['sourceField' => 'from', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'date_to' => ['sourceField' => 'to', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'title' => ['sourceField' => 'title', 'type' => ParameterType::STRING],
            'last_modified_by_operator' => ['sourceField' => 'lastModifiedByOperator', 'type' => ParameterType::STRING],
            'created_by_operator' => ['sourceField' => 'createdByOperator', 'type' => ParameterType::STRING],
            'type' => ['sourceField' => 'type', 'type' => ParameterType::STRING],
            'resource' => ['sourceField' => 'resource', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
