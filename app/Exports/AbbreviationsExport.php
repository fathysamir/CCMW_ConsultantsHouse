<?php

namespace App\Exports;

use App\Models\ProjectAbbreviation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbbreviationsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ProjectAbbreviation::where('project_id',auth()->user()->current_project_id)->select('abb', 'description') ->orderByRaw("LOWER(abb) ASC")->get();
    }

    public function headings(): array
    {
        return ['Abbreviation', 'Description'];
    }
}
