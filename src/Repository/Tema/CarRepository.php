<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class CarRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/vehicles/:sync';
    protected $table = 'tema_car';

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

            if (count($this->fetchResult) >= $this->fetchLimit) {
                $this->save();
                $this->fetchResult = [];
            }
        } while ($res['fetchNext']);
        $this->save();

        return ['fetched' => $resCount];
    }

    protected function getFieldsParams(): array
    {
        return [
            'row_id' => ['sourceField' => 'rowId', 'type' => ParameterType::INTEGER],
            'car_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'brand_id' => ['sourceField' => 'brandId', 'type' => ParameterType::INTEGER],
            'brand_name' => ['sourceField' => 'brandName', 'type' => ParameterType::STRING],
            'model_code' => ['sourceField' => 'modelCode', 'type' => ParameterType::STRING],
            'model_name' => ['sourceField' => 'modelName', 'type' => ParameterType::STRING],
            'body_color' => ['sourceField' => 'bodyColor', 'type' => ParameterType::STRING],
            'description' => ['sourceField' => 'description', 'type' => ParameterType::STRING],
            'manufacturing_year' => ['sourceField' => 'manufacturingYear', 'type' => ParameterType::INTEGER],
            'user_id' => ['sourceField' => 'userId', 'type' => ParameterType::STRING],
            'owner_id' => ['sourceField' => 'ownerId', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'registration_no' => ['sourceField' => 'registrationNo', 'type' => ParameterType::STRING],
            'mileage' => ['sourceField' => 'mileage', 'type' => ParameterType::INTEGER],
            'first_registration_date' => ['sourceField' => 'firstRegistrationDate', 'type' => ParameterType::STRING],
            'next_inspection_date' => ['sourceField' => 'nextInspectionDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'renault_order_number' => ['sourceField' => 'renaultOrderNumber', 'type' => ParameterType::STRING],
            'ips_number' => ['sourceField' => 'ipsNumber', 'type' => ParameterType::STRING],
            'type_approval' => ['sourceField' => 'typeApproval', 'type' => ParameterType::INTEGER],
            'vehicle_kind' => ['sourceField' => 'vehicleKind', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }
}
