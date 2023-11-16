<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CarReserveRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/vehicle-reserves/:sync';
    protected $table = 'tema_car_reserve';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $resCount = 0;
        $res = [];
        do {
            $nextTimestamp = '';
            if (!empty($res['lastTimestamp']))
                $nextTimestamp = '?timestamp=' . urlencode($res['lastTimestamp']);

            $res = $this->fetchApiResult($this->endpoint . $nextTimestamp);
            if (empty($res))
                continue;

            $this->fetchResult = array_merge($this->fetchResult, $res['items']);
            $resCount += count($res['items']);
        } while ($res['fetchNext']);
        $this->save();

        return [
            'fetched' => $resCount,
        ];
    }

    protected function getFieldsParams(): array
    {
        return [
            'row_id' => ['sourceField' => 'rowId', 'type' => ParameterType::INTEGER],
            'vehicle_id' => ['sourceField' => 'vehicleId', 'type' => ParameterType::INTEGER],
            'activity' => ['sourceField' => 'activity', 'type' => ParameterType::STRING],
            'creation_date' => ['sourceField' => 'creationDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'planned_realization_date' => ['sourceField' => 'plannedRealizationDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'reserve_number' => ['sourceField' => 'reserveNumber', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'expected_reserve_value' => ['sourceField' => 'expectedReserveValue', 'type' => ParameterType::STRING],
            'reserve_id' => ['sourceField' => 'reserveId', 'type' => ParameterType::STRING],
            'type' => ['sourceField' => 'type', 'type' => ParameterType::STRING],
            'order_id' => ['sourceField' => 'orderId', 'type' => ParameterType::STRING],
            'description' => ['sourceField' => 'description', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
