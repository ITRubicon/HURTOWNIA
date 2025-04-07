<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ScheduleResourcesRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/schedule/resources?dateFrom={dateFrom}&dateTo={dateTo}';
    protected $table = 'tema_schedule_resources';

    public function fetch(): array
    {
        $this->onDuplicateClause = '
            ON DUPLICATE KEY UPDATE
                type = VALUES(type),
                name = VALUES(name),
                warehouse_ids = VALUES(warehouse_ids)
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
            
            $availabilities = $this->collectAvailability();
            $this->relatedRepositories['availability']->saveAvailability($availabilities);
            unset($availabilities);
            
            $resCount += count($this->fetchResult);
            $this->save();
            $this->fetchResult = [];
            gc_collect_cycles();
        }
        
        return ['fetched' => $resCount];
    }

    private function collectAvailability(): array
    {
        $availabilities = [];
        foreach ($this->fetchResult as $i => $res) {
            $resourceCode = $res['resourceCode'];
            
            if (empty($res['availabilities'])) {
                continue;
            }

            foreach ($res['availabilities'] as $availability) {
                $availability['resourceCode'] = $resourceCode;
                $availabilities[] = $availability;
            }
        }

        return $availabilities;
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
            'id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'resource_code' => ['sourceField' => 'resourceCode', 'type' => ParameterType::STRING],
            'type' => ['sourceField' => 'type', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'warehouse_ids' => ['sourceField' => 'warehouseIds', 'type' => ParameterType::STRING, 'format' => ['json' => true]],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
