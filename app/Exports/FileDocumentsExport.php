<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FileDocumentsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $documents;

    public function __construct($documents)
    {
        $this->documents = $documents;
    }
    public function collection()
    {
        return $this->documents;
    }
    public function headings(): array
    {
        return ['Type', 'Reference', 'Subject', 'Date', 'Return Date','From', 'To', 'Rev','SN','Status','Note'];
    }

    public function map($document): array
    {
        return [
            $document->document->docType->name,
            $document->document->reference,
            $document->document->subject,
            date('d-M-Y', strtotime($document->document->start_date)),
            $document->document->end_date? date('d-M-Y', strtotime($document->document->end_date)): '__',
            $document->document->fromStakeHolder ? $document->document->fromStakeHolder->narrative : '_',  // relation sender
            $document->document->toStakeHolder ? $document->document->toStakeHolder->narrative : '_', // relation receiver
            $document->document->revision,
            $document->sn,
            $document->document->status,
            $document->notes1,
        ];
    }
}
