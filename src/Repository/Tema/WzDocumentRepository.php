<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class WzDocumentRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/gdns/{branchId}';
    private $documentEndpoints = [];
    private $documentItems = [];
    protected $table = 'tema_wz_document';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $this->getDocumentEndpointList();
        $listCount = count($this->documentEndpoints);

        if ($listCount) {
            echo "\nPobieram zapisy dokumentów";

            for ($i=0; $i < $listCount; $i++) { 
                echo "\nEndpoint ----> $i/$listCount";
                $doc = $this->fetchApiResult($this->documentEndpoints[$i]/* ['getUrl'] */);
                unset($this->documentEndpoints[$i]);

                if (empty($doc))
                    continue;

                dump($doc['grossValue']);
                $this->collectItems($doc);
                array_push($this->fetchResult, $doc);

                if (count($this->fetchResult) >= $this->fetchLimit) {
                    $this->save();
                    $this->fetchResult = [];
                }
            }

            $this->save();
            $this->fetchResult = [];
        }

        return [
            'fetched' => $listCount,
            'items' => $this->documentItems
        ];
    }

    private function getDocumentEndpointList()
    {
        $stocks = $this->getStocks();
        $stocksCount = count($stocks);

        $this->documentEndpoints = [
            '/api/dms/v1/gdns/01/WZ0100002605', '/api/dms/v1/gdns/01/WZ0100002609', '/api/dms/v1/gdns/01/WZ0100002613', '/api/dms/v1/gdns/01/WZ0100002617', '/api/dms/v1/gdns/01/WZ0100002621', '/api/dms/v1/gdns/01/WZ0100002625', '/api/dms/v1/gdns/01/WZ0100002629', '/api/dms/v1/gdns/01/WZ0100002633', '/api/dms/v1/gdns/01/WZ0100002637', '/api/dms/v1/gdns/01/WZ0100002641', '/api/dms/v1/gdns/01/WZ0100002645', '/api/dms/v1/gdns/01/WZ0100002649', '/api/dms/v1/gdns/01/WZ0100002653', '/api/dms/v1/gdns/01/WZ0100002657', '/api/dms/v1/gdns/01/WZ0100002661', '/api/dms/v1/gdns/01/WZ0100002665', '/api/dms/v1/gdns/01/WZ0100002669', '/api/dms/v1/gdns/01/WZ0100002673', '/api/dms/v1/gdns/01/WZ0100002677', '/api/dms/v1/gdns/01/WZ0100002681', '/api/dms/v1/gdns/01/WZ0100002685', '/api/dms/v1/gdns/01/WZ0100002689', '/api/dms/v1/gdns/01/WZ0100002693', '/api/dms/v1/gdns/01/WZ0100002697', '/api/dms/v1/gdns/01/WZ0100002701', '/api/dms/v1/gdns/01/WZ0100002705', '/api/dms/v1/gdns/01/WZ0100002709', '/api/dms/v1/gdns/01/WZ0100002713', '/api/dms/v1/gdns/01/WZ0100002717', '/api/dms/v1/gdns/01/WZ0100002721', '/api/dms/v1/gdns/01/WZ0100002725', '/api/dms/v1/gdns/01/WZ0100002729', '/api/dms/v1/gdns/01/WZ0100002733', '/api/dms/v1/gdns/01/WZ0100002737', '/api/dms/v1/gdns/01/WZ0100002741', '/api/dms/v1/gdns/01/WZ0100002745', '/api/dms/v1/gdns/01/WZ0100002749', '/api/dms/v1/gdns/01/WZ0100002753', '/api/dms/v1/gdns/01/WZ0100002757', '/api/dms/v1/gdns/01/WZ0100002761', '/api/dms/v1/gdns/01/WZ0100002765', '/api/dms/v1/gdns/01/WZ0100002769', '/api/dms/v1/gdns/01/WZ0100002773', '/api/dms/v1/gdns/01/WZ0100002777', '/api/dms/v1/gdns/01/WZ0100002781', '/api/dms/v1/gdns/01/WZ0100002785', '/api/dms/v1/gdns/01/WZ0100002789', '/api/dms/v1/gdns/01/WZ0100002793', '/api/dms/v1/gdns/01/WZ0100002797', '/api/dms/v1/gdns/01/WZ0100002801', '/api/dms/v1/gdns/01/WZ0100002805', '/api/dms/v1/gdns/01/WZ0100002809', '/api/dms/v1/gdns/01/WZ0100002813', '/api/dms/v1/gdns/01/WZ0100002817', '/api/dms/v1/gdns/01/WZ0100002821', '/api/dms/v1/gdns/01/WZ0100002825', '/api/dms/v1/gdns/01/WZ0100002829', '/api/dms/v1/gdns/01/WZ0100002833', '/api/dms/v1/gdns/01/WZ0100002837', '/api/dms/v1/gdns/01/WZ0100002841', '/api/dms/v1/gdns/01/WZ0100002845', '/api/dms/v1/gdns/01/WZ0100002849', '/api/dms/v1/gdns/01/WZ0100002853', '/api/dms/v1/gdns/01/WZ0100002857', '/api/dms/v1/gdns/01/WZ0100002861', '/api/dms/v1/gdns/01/WZ0100002865', '/api/dms/v1/gdns/01/WZ0100002869', '/api/dms/v1/gdns/01/WZ0100002873', '/api/dms/v1/gdns/01/WZ0100002877', '/api/dms/v1/gdns/01/WZ0100002881', '/api/dms/v1/gdns/01/WZ0100002885', '/api/dms/v1/gdns/01/WZ0100002889', '/api/dms/v1/gdns/01/WZ0100002893', '/api/dms/v1/gdns/01/WZ0100002897', '/api/dms/v1/gdns/01/WZ0100002901', '/api/dms/v1/gdns/01/WZ0100002905', '/api/dms/v1/gdns/01/WZ0100002909', '/api/dms/v1/gdns/01/WZ0100002913', '/api/dms/v1/gdns/01/WZ0100002917', '/api/dms/v1/gdns/01/WZ0100002921', '/api/dms/v1/gdns/01/WZ0100002925', '/api/dms/v1/gdns/01/WZ0100002929', '/api/dms/v1/gdns/01/WZ0100002933', '/api/dms/v1/gdns/01/WZ0100002937', '/api/dms/v1/gdns/01/WZ0100002941', '/api/dms/v1/gdns/01/WZ0100002945', '/api/dms/v1/gdns/01/WZ0100002949', '/api/dms/v1/gdns/01/WZ0100002953', '/api/dms/v1/gdns/01/WZ0100002957', '/api/dms/v1/gdns/01/WZ0100002961', '/api/dms/v1/gdns/01/WZ0100002965', '/api/dms/v1/gdns/01/WZ0100002969', '/api/dms/v1/gdns/01/WZ0100002973', '/api/dms/v1/gdns/01/WZ0100002977', '/api/dms/v1/gdns/01/WZ0100002981', '/api/dms/v1/gdns/01/WZ0100002985', '/api/dms/v1/gdns/01/WZ0100002989', '/api/dms/v1/gdns/01/WZ0100002993', '/api/dms/v1/gdns/01/WZ0100002997', '/api/dms/v1/gdns/01/WZ0100003001', '/api/dms/v1/gdns/01/WZ0100003005', '/api/dms/v1/gdns/01/WZ0100003009', '/api/dms/v1/gdns/01/WZ0100003013', '/api/dms/v1/gdns/01/WZ0100003017', '/api/dms/v1/gdns/01/WZ0100003021', '/api/dms/v1/gdns/01/WZ0100003025', '/api/dms/v1/gdns/01/WZ0100003029', '/api/dms/v1/gdns/01/WZ0100003033', '/api/dms/v1/gdns/01/WZ0100003037', '/api/dms/v1/gdns/01/WZ0100003041', '/api/dms/v1/gdns/01/WZ0100003045', '/api/dms/v1/gdns/01/WZ0100003049', '/api/dms/v1/gdns/01/WZ0100003053', '/api/dms/v1/gdns/01/WZ0100003057', '/api/dms/v1/gdns/01/WZ0100003061', '/api/dms/v1/gdns/01/WZ0100003065', '/api/dms/v1/gdns/01/WZ0100003069', '/api/dms/v1/gdns/01/WZ0100003073', '/api/dms/v1/gdns/01/WZ0100003077', '/api/dms/v1/gdns/01/WZ0100003081', '/api/dms/v1/gdns/01/WZ0100003085', '/api/dms/v1/gdns/01/WZ0100003089', '/api/dms/v1/gdns/01/WZ0100003093', '/api/dms/v1/gdns/01/WZ0100003097', '/api/dms/v1/gdns/01/WZ0100003101', '/api/dms/v1/gdns/01/WZ0100003105', '/api/dms/v1/gdns/01/WZ0100003109', '/api/dms/v1/gdns/01/WZ0100003113', '/api/dms/v1/gdns/01/WZ0100003117', '/api/dms/v1/gdns/01/WZ0100003121', '/api/dms/v1/gdns/01/WZ0100003125', '/api/dms/v1/gdns/01/WZ0100003129', '/api/dms/v1/gdns/01/WZ0100003133', '/api/dms/v1/gdns/01/WZ0100003137', '/api/dms/v1/gdns/01/WZ0100003141', '/api/dms/v1/gdns/01/WZ0100003145', '/api/dms/v1/gdns/01/WZ0100003149', '/api/dms/v1/gdns/01/WZ0100003153', '/api/dms/v1/gdns/01/WZ0100003157', '/api/dms/v1/gdns/01/WZ0100003161', '/api/dms/v1/gdns/01/WZ0100003165', '/api/dms/v1/gdns/01/WZ0100003169', '/api/dms/v1/gdns/01/WZ0100003173', '/api/dms/v1/gdns/01/WZ0100003177', '/api/dms/v1/gdns/01/WZ0100003181', '/api/dms/v1/gdns/01/WZ0100003185', '/api/dms/v1/gdns/01/WZ0100003189', '/api/dms/v1/gdns/01/WZ0100003193', '/api/dms/v1/gdns/01/WZ0100003197', '/api/dms/v1/gdns/01/WZ0100003201', '/api/dms/v1/gdns/01/WZ0100003205', '/api/dms/v1/gdns/01/WZ0100003209', '/api/dms/v1/gdns/01/WZ0100003213', '/api/dms/v1/gdns/01/WZ0100003217', '/api/dms/v1/gdns/01/WZ0100003221', '/api/dms/v1/gdns/01/WZ0100003225', '/api/dms/v1/gdns/01/WZ0100003229', '/api/dms/v1/gdns/01/WZ0100003233', '/api/dms/v1/gdns/01/WZ0100003237', '/api/dms/v1/gdns/01/WZ0100003241', '/api/dms/v1/gdns/01/WZ0100003245', '/api/dms/v1/gdns/01/WZ0100003249', '/api/dms/v1/gdns/01/WZ0100003253', '/api/dms/v1/gdns/01/WZ0100003257', '/api/dms/v1/gdns/01/WZ0100003261', '/api/dms/v1/gdns/01/WZ0100003265', '/api/dms/v1/gdns/01/WZ0100003269', '/api/dms/v1/gdns/01/WZ0100003273', '/api/dms/v1/gdns/01/WZ0100003277', '/api/dms/v1/gdns/01/WZ0100003281', '/api/dms/v1/gdns/01/WZ0100003285', '/api/dms/v1/gdns/01/WZ0100003289', '/api/dms/v1/gdns/01/WZ0100003293', '/api/dms/v1/gdns/01/WZ0100003297', '/api/dms/v1/gdns/01/WZ0100003301', '/api/dms/v1/gdns/01/WZ0100003305', '/api/dms/v1/gdns/01/WZ0100003309', '/api/dms/v1/gdns/01/WZ0100003313', '/api/dms/v1/gdns/01/WZ0100003317', '/api/dms/v1/gdns/01/WZ0100003321', '/api/dms/v1/gdns/01/WZ0100003325', '/api/dms/v1/gdns/01/WZ0100003329', '/api/dms/v1/gdns/01/WZ0100003333', '/api/dms/v1/gdns/01/WZ0100003337', '/api/dms/v1/gdns/01/WZ0100003341', '/api/dms/v1/gdns/01/WZ0100003345', '/api/dms/v1/gdns/01/WZ0100003349', '/api/dms/v1/gdns/01/WZ0100003353', '/api/dms/v1/gdns/01/WZ0100003357', '/api/dms/v1/gdns/01/WZ0100003361', '/api/dms/v1/gdns/01/WZ0100003365', '/api/dms/v1/gdns/01/WZ0100003369', '/api/dms/v1/gdns/01/WZ0100003373', '/api/dms/v1/gdns/01/WZ0100003377', '/api/dms/v1/gdns/01/WZ0100003381', '/api/dms/v1/gdns/01/WZ0100003385', '/api/dms/v1/gdns/01/WZ0100003389', '/api/dms/v1/gdns/01/WZ0100003393', '/api/dms/v1/gdns/01/WZ0100003397', '/api/dms/v1/gdns/01/WZ0100003401', 
        ];
        return;

        if ($stocksCount) {
            echo "\nPobieranie endpointów dla oddziałów";
            $i = 1;

            foreach ($stocks as $stock) {
                echo "\nNr stocku $stock ----> $i/$stocksCount";

                $url = str_replace('{branchId}', $stock, $this->endpoint);
                $res = $this->fetchApiResult($url);
                if (empty($res))
                    continue;

                $this->documentEndpoints = array_merge($this->documentEndpoints, $res);

                $i++;
            }
        } else 
            throw new \Exception("Nie żadnych jednostek organizacyjnych. Najpierw uruchom komendę pobierającą listę jednostek [tema:stock]", 99);
    }

    protected function getFieldsParams(): array
    {
        return [
            'wz_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'name' => ['sourceField' => 'name', 'type' => ParameterType::STRING],
            'vin' => ['sourceField' => 'vin', 'type' => ParameterType::STRING],
            'customer_id' => ['sourceField' => 'customerId', 'type' => ParameterType::STRING],
            'issue_date' => ['sourceField' => 'issueDate', 'type' => ParameterType::STRING, 'format' => ['date' => 'Y-m-d H:i:s']],
            'net_value' => ['sourceField' => 'netValue', 'type' => ParameterType::STRING],
            'gross_value' => ['sourceField' => 'grossValue', 'type' => ParameterType::STRING],
            'order_id' => ['sourceField' => 'orderId', 'type' => ParameterType::STRING],
            'order_name' => ['sourceField' => 'orderName', 'type' => ParameterType::STRING],
            'notes' => ['sourceField' => 'notes', 'type' => ParameterType::STRING],
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
        ];
    }

    private function collectItems(array &$doc)
    {
        foreach ($doc['items'] as $item) {
            $item['wz_id'] = $doc['id'];
            array_push($this->documentItems, $item);
        }
        unset($doc['items']);
    }

    private function getStocks()
    {
        $q = "SELECT stock_id FROM tema_stock WHERE source = :source";
        return $this->db->fetchFirstColumn($q, ['source' => $this->source->getName()], ['source' => ParameterType::STRING]);
    }

    protected function clearDataArrays()
    {
        $this->fetchResult = [];
        $this->documentEndpoints = [];
        $this->documentItems = [];
    }
}
