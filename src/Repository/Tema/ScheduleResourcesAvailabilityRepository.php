<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class ScheduleResourcesAvailabilityRepository extends IApiRepository
{
    private string $endpoint = '';
    protected $table = 'tema_schedule_resources_availability';

    public function saveAvailability(array $items): int
    {
        $this->clearDataArrays();
        $this->fetchResult = $items;

        $this->onDuplicateClause = '
            ON DUPLICATE KEY UPDATE
                end = VALUES(end),
                day = VALUES(day),
                hours = VALUES(hours)
        ';

        $this->save();
        $resCount = count($this->fetchResult);
        $this->clearDataArrays();

        gc_collect_cycles();

        return $resCount;
    }


    protected function getFieldsParams(): array
    {
        return [
            'resource_code' => ['sourceField' => 'resourceCode', 'type' => ParameterType::STRING],
            'start' => ['sourceField' => 'start', 'type' => ParameterType::STRING, 'format' => ['date' => 'H:i:s']],
            'end' => ['sourceField' => 'end', 'type' => ParameterType::STRING, 'format' => ['date' => 'H:i:s']],
            'day' => ['sourceField' => 'day', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'hours' => ['sourceField' => 'hours', 'type' => ParameterType::INTEGER],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
