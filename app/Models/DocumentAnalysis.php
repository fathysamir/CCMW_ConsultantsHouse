<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentAnalysis extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'document_analysis';

    protected $fillable = [
        'document_id',
        'time_prolongation_cost',
        'disruption_cost',
        'variation',
        'impacted_zone',
        'concerned_part',
        'why_need_analysis',
        'affected_works',
        'analysis_date'
    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id', 'id')->withTrashed();
    }
}
