<?php

namespace App\Repository\Tema;

use App\Repository\IApiRepository;
use Doctrine\DBAL\ParameterType;

class FvPaymentRepository extends IApiRepository
{
    private string $endpoint = '/api/dms/v1/sales-invoices/{branchId}/{invoiceId}/status';
    protected $table = 'tema_fv_payment';
    protected $onDuplicateClause = 'ON DUPLICATE KEY UPDATE
        document_payment_status = VALUES(document_payment_status),
        document_value = VALUES(document_value),
        document_payment_value = VALUES(document_payment_value)
    ';

    public function fetch(): array
    {
        $this->clearDataArrays();
        $genCount = iterator_count($this->documentIds());
        echo "\nPobieram zapisy płatności dla $genCount dokumentów";

        foreach ($this->documentIds() as $i => $row) {
            echo "\nDokument ----> $i/$genCount";
            if (empty($row['doc_id']) || empty($row['branch']))
                continue;

            $url = str_replace(['{branchId}', '{invoiceId}'],
                [$row['branch'], $row['doc_id']],
                $this->endpoint
            );

            $doc = $this->fetchApiResult($url);
            foreach ($doc['payments'] as $p) {
                $p['source'] = $this->source->getName();
                $p['id'] = $doc['id'];

                $this->fetchResult[] = $p;
            }
            
            unset($doc);

            if (count($this->fetchResult) >= $this->fetchLimit) {
                    $this->save();
                    $this->fetchResult = [];

                    gc_collect_cycles();
                }
        }

        return [
            'fetched' => count($this->fetchResult)
        ];
    }

    private function documentIds(): \Generator
    {
        // Póki co nie aktualizują się statusy płatności. Powód nieznany.
        // $q = "SELECT SUBSTRING(doc_id, 3, 2) AS branch, doc_id FROM prehurtownia.tema_fv_document WHERE source = :source";
        $q = "SELECT SUBSTRING(fv.doc_id, 3, 2) AS branch, fv.doc_id FROM prehurtownia.tema_fv_document fv
            LEFT JOIN prehurtownia.tema_fv_payment fp ON fp.source = fv.source AND fp.doc_id = fv.doc_id
            WHERE fv.source = :source
            AND fp.doc_id IS NULL
        ";
        $stmt = $this->db->executeQuery($q, ['source' => $this->source->getName()]);

        while (($row = $stmt->fetchAssociative()) !== false) {
            yield $row;
        }
    }

    protected function getFieldsParams(): array
    {
        return [
            'source' => ['sourceField' => 'source', 'type' => ParameterType::STRING],
            'doc_id' => ['sourceField' => 'id', 'type' => ParameterType::STRING],
            'document_payment_status' => ['sourceField' => 'documentPaymentStatus', 'type' => ParameterType::STRING],
            'document_value' => ['sourceField' => 'documentValue', 'type' => ParameterType::STRING],
            'document_payment_value' => ['sourceField' => 'documentPaymentValue', 'type' => ParameterType::STRING],
            'customer_id' => ['sourceField' => 'customerId', 'type' => ParameterType::STRING],
            'payment_document_id' => ['sourceField' => 'documentId', 'type' => ParameterType::STRING],
        ];
    }
}
