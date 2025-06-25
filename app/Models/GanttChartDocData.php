<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GanttChartDocData extends Model
{
    use HasFactory;

    protected $table = 'gantt_chart_doc_data';

    protected $fillable = [

        'file_document_id', ////////////////
        'show_cur',  //////////////////
        'cur_type', ///////////////////
        'cur_sections',
        'cur_left_caption', /////////////
        'cur_right_caption', //////////////
        'cur_show_sd', //////////////////
        'cur_show_fd', //////////////////
        'cur_show_ref', ///////////////
       

        'show_pl', //////////////////////
        'pl_type', ////////////////////
        'pl_sd', ///////////////
        'pl_fd', /////////////////
        'pl_color', ///////////////////
        'pl_left_caption', /////////////
        'pl_right_caption', ///////////////
        'pl_show_sd', ///////////////////
        'pl_show_fd', ///////////////////

        'show_lp', /////////////////
        'lp_sd', //////////
        'lp_fd' /////////////
    ];

    protected $allowedSorts = [

        'created_at',
        'updated_at',
    ];

    protected $hidden = ['deleted_at'];

    public function fileDoc()
    {
        return $this->belongsTo(FileDocument::class, 'file_document_id', 'id')->withTrashed();
    }

}
