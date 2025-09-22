<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DocumentsExport implements FromCollection, WithHeadings, WithMapping
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
        return ['Type', 'Reference', 'Subject', 'Date', 'From', 'To', 'Rev'];
    }

    public function map($document): array
    {
        return [
            $document->docType->name,
            $document->reference,
            $document->subject,
            date('d-M-Y', strtotime($document->start_date)),
            $document->fromStakeHolder ? $document->fromStakeHolder->narrative : '_',  // relation sender
            $document->toStakeHolder ? $document->toStakeHolder->narrative : '_', // relation receiver
            $document->revision,
        ];
    }
}
