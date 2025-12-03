<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

function uploadMedia($request_file, $collection_name, $model)
{
    ini_set('post_max_size', '500M');
    ini_set('upload_max_filesize', '500M');
    ini_set('memory_limit', '1000M');
    set_time_limit(10000000);
    $directory = public_path('images');

    if (! File::exists($directory)) {
        File::makeDirectory($directory, 0755, true);
    }
    $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_'), 0, 12);
    $image = $model->id.''.$invitation_code.''.time().'.'.$request_file->extension();

    $request_file->move(public_path('images/'), $image);
    $path = ('/images/').$image;
    DB::table('media')->insert([
        'attachmentable_type' => get_class($model),
        'attachmentable_id' => $model->id,
        'collection_name' => $collection_name,
        'Path' => $path,
    ]);

    return $path;
}

function getMediaUrl($model, $collection_name)
{
    // $attachment = $model->attachment()
    //     ->where('collection_name', $collection_name)
    //     ->first();
    $attachments = DB::table('media')->where('attachmentable_id', $model->id)->where('collection_name', $collection_name)->where('attachmentable_type', get_class($model))->select('path')->get();

    if (count($attachments) == 0) {
        return null;
    } else {

        foreach ($attachments as $attachment) {
            $attachment->path = url($attachment->path);
        }

        return $attachments;
    }

}

function getFirstMediaUrl($model, $collection_name, $with_url = true)
{
    // $attachment = $model->attachment()
    //     ->where('collection_name', $collection_name)
    //     ->first();
    $attachment = DB::table('media')->where('attachmentable_id', $model->id)->where('collection_name', $collection_name)->where('attachmentable_type', get_class($model))->first();

    if (! $attachment || $attachment->path == null) {
        return null;
    }
    if ($with_url) {
        return url($attachment->path);

    } else {
        return $attachment->path;
    }
}

function deleteMedia($model, $collection_name = null)
{

    return DB::table('media')->where('attachmentable_type', get_class($model))->where('attachmentable_id', $model->id)->where('collection_name', $collection_name)->delete();

}

function generateOTP()
{
    return rand(100000, 999999);
}

function highlight($text, $search)
{
    if ($search) {
        return str_ireplace($search, "<mark style='background-color:rgb(143, 118, 9); padding:0px;'>$search</mark>", $text);
    }

    return $text;
}

function extractTextSnippet($html, $limit = 50)
{
    // 1) Remove HTML tags
    $text = strip_tags($html);

    // 2) Trim spaces
    $text = trim($text);

    // 3) If length > 70 cut and add "..."
    if (mb_strlen($text) > $limit) {
        return mb_substr($text, 0, $limit) . '...';
    }

    return $text;
}

function containsPlaceholder($text)
{
    $keys = [
        'fnWNo(',
        'fnPrevWNo(',
        'fnCompDate(',
        'fnDrivAct(',
        'fnListOfDEs(',
        'fnCulpable(',
        'fnExcusable(',
        'fnCompensable(',
        'fnCompensableTransfer('
    ];

    foreach ($keys as $k) {
        if (strpos($text, $k) !== false) {
            return true;
        }
    }
    return false;
}

function getFunctionValue($text){

}

