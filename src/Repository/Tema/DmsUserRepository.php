<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class DmsUserRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/users/:sync';
    protected $table = 'tema_dms_user';

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
        $this->fetchResult = [];
        
        gc_collect_cycles();

        return ['fetched' => $resCount];
    }

    protected function getFieldsParams(): array
    {
        return [
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
            'row_id' => ['sourceField' => 'rowId', 'type' => ParameterType::INTEGER],
            'id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'full_name' => ['sourceField' => 'fullName', 'type' => ParameterType::STRING],
            'is_active' => ['sourceField' => 'active', 'type' => ParameterType::INTEGER, 'format' => ['int' => true]],
        ];
    }
}
