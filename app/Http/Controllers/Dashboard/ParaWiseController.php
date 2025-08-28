<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Document;
use App\Models\Paragraph;
use App\Models\ParaWise;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ParaWiseController extends ApiController
{
    public function para_wise_analysis()
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }

        $all_para_wises = ParaWise::where('project_id', auth()->user()->current_project_id)->get();
        $project        = Project::findOrFail(auth()->user()->current_project_id);
        $users          = $project->assign_users;
        return view('project_dashboard.para_wise_analysis.index', compact('all_para_wises', 'users'));

    }

    public function store(Request $request)
    {
        do {
            $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (ParaWise::where('slug', $slug)->exists());

        ParaWise::create(['slug' => $slug, 'project_id' => auth()->user()->current_project_id, 'title' => $request->title, 'user_id' => $request->user_id, 'percentage_complete' => $request->percentage_complete]);
        return redirect('/project/para-wise-analysis')->with('success', 'Para-wise Created successfully.');

    }

    public function update(Request $request, $id)
    {
        $para_wise                      = ParaWise::where('slug', $id)->first();
        $para_wise->title               = $request->title;
        $para_wise->user_id             = $request->user_id;
        $para_wise->percentage_complete = $request->percentage_complete;
        $para_wise->save();
        return redirect('/project/para-wise-analysis')->with('success', 'Para-wise Updated successfully.');

    }

    public function delete($id)
    {
        ParaWise::where('slug', $id)->delete();
        return redirect('/project/para-wise-analysis')->with('success', 'Para-wise Deleted successfully.');

    }

    public function paragraphs($id)
    {
        $para_wise          = ParaWise::where('slug', $id)->first();
        $paragraphs         = Paragraph::where('para_wise_id', $para_wise->id)->get();
        $specific_paragraph = session('specific_paragraph');
        session()->forget('specific_paragraph');
        return view('project_dashboard.para_wise_analysis.paragraphs', compact('paragraphs', 'para_wise', 'specific_paragraph'));
    }

    public function delete_paragraph($id)
    {
        $slug = Paragraph::where('slug', $id)->first()->para_wise->slug;

        Paragraph::where('slug', $id)->delete();

        return redirect('/project/para-wise-analysis/paragraphs/' . $slug)->with('success', 'Paragraph Deleted successfully.');

    }

    public function changeFlag(Request $request)
    {
        $request->validate([
            'paragraph_id' => 'required|exists:paragraphs,id',
            'flag'         => 'required|in:blue_flag,red_flag,green_flag',
        ]);

        $paragraph = Paragraph::findOrFail($request->paragraph_id);

        // قلب القيمة 0 ↔ 1
        $paragraph->{$request->flag} = ! $paragraph->{$request->flag};
        $paragraph->save();

        return response()->json([
            'status' => 'success',
            'flag'   => $request->flag,
            'value'  => $paragraph->{$request->flag},
        ]);
    }

    public function create_paragraph($id)
    {
        $para_wise  = ParaWise::where('slug', $id)->first();
        $docs       = Document::where('project_id', auth()->user()->current_project_id)->select('id', 'slug', 'reference', 'storage_file_id')->with('storageFile')->get();
        $paragraphs = Paragraph::where('para_wise_id', $para_wise->id)->get();

        return view('project_dashboard.para_wise_analysis.create_paragraph', compact('para_wise', 'docs', 'paragraphs'));
    }

    public function stor_paragraph(Request $request)
    {
        // dd($request->all());
        if ($this->hasContent($request->background)) {
            $background = $request->background;
        } else {
            $background = null;
        }
        if ($this->hasContent($request->paragraph)) {
            $paragraph = $request->paragraph;
        } else {
            $paragraph = null;
        }
        if ($this->hasContent($request->reply)) {
            $reply = $request->reply;
        } else {
            $reply = null;
        }
        if ($request->para_numbers) {
            Paragraph::whereIn('id', $request->para_numbers)->update(['replyed' => "1", 'reply_user_id' => auth()->user()->id]);
            $para_numbers = implode(",", $request->para_numbers);
        } else {
            $para_numbers = null;
        }
        if ($request->para_exhibits) {
            $para_exhibits = implode(",", $request->para_exhibits);
        } else {
            $para_exhibits = null;
        }
        if ($request->reply_exhibits) {
            $reply_exhibits = implode(",", $request->reply_exhibits);
        } else {
            $reply_exhibits = null;
        }
        do {
            $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (Paragraph::where('slug', $slug)->exists());
        $para_wise = ParaWise::where('slug', $request->para_wise_slug)->first();
        $paragraph = Paragraph::create(['slug' => $slug, 'user_id'                        => auth()->user()->id, 'para_wise_id' => $para_wise->id,
            'reply'                                => $reply, 'paragraph'                     => $paragraph, 'background'           => $background, 'blue_flag' => $request->blue_flag,
            'green_flag'                           => $request->green_flag, 'red_flag'        => $request->red_flag, 'notes'        => $request->notes,
            'background_ref'                       => $request->background_ref, 'title_above' => $request->title_above, 'number'    => $request->number,
            'para_numbers'                         => $para_numbers, 'reply_exhibits'         => $reply_exhibits, 'para_exhibits'   => $para_exhibits]);

        if ($reply) {
            $paragraph->replyed       = "1";
            $paragraph->reply_user_id = auth()->user()->id;
        }
        $paragraph->save();

        return redirect('/project/para-wise-analysis/paragraphs/' . $para_wise->slug)->with('success', 'Paragraph Created successfully.');

    }
    private function hasContent($narrative)
    {
        // Remove all HTML tags except text content
        $text = strip_tags($narrative);

        // Remove extra spaces & line breaks
        $text = trim($text);

        // Check if there's any actual content
        return ! empty($text);
    }

    public function edit_paragraph($id)
    {
        $paragraph  = Paragraph::where('slug', $id)->first();
        $para_wise  = ParaWise::where('id', $paragraph->para_wise_id)->first();
        $docs       = Document::where('project_id', auth()->user()->current_project_id)->select('id', 'slug', 'reference', 'storage_file_id')->with('storageFile')->get();
        $paragraphs = Paragraph::where('para_wise_id', $para_wise->id)->where('id', '!=', $paragraph->id)->get();

        session(['specific_paragraph' => $id]);

        $previous = Paragraph::where('para_wise_id', $paragraph->para_wise_id)
            ->where('number', '<', $paragraph->number)
            ->orderBy('number', 'desc')
            ->first();

        // get next (أكبر number)
        $next = Paragraph::where('para_wise_id', $paragraph->para_wise_id)
            ->where('number', '>', $paragraph->number)
            ->orderBy('number', 'asc')
            ->first();

        return view('project_dashboard.para_wise_analysis.edit_paragraph', compact('next','previous','paragraph', 'para_wise', 'docs', 'paragraphs'));
    }

    public function update_paragraph(Request $request, $slug)
    {
        // هات البرجراف بالـ slug
        $paragraph = Paragraph::where('slug', $slug)->firstOrFail();

        // Background
        $background = $this->hasContent($request->background) ? $request->background : null;

        // Paragraph
        $paraContent = $this->hasContent($request->paragraph) ? $request->paragraph : null;

        // Reply
        $reply = $this->hasContent($request->reply) ? $request->reply : null;

        // para_numbers
        if ($paragraph->para_numbers) {
            Paragraph::whereIn('id', $paragraph->para_numbers)
                ->update(['replyed' => "0", 'reply_user_id' => null]);
        }
        if ($request->para_numbers) {
            Paragraph::whereIn('id', $request->para_numbers)
                ->update(['replyed' => "1", 'reply_user_id' => auth()->user()->id]);
            $para_numbers = implode(",", $request->para_numbers);
        } else {
            $para_numbers = null;
        }

        // Exhibits
        $para_exhibits  = $request->para_exhibits ? implode(",", $request->para_exhibits) : null;
        $reply_exhibits = $request->reply_exhibits ? implode(",", $request->reply_exhibits) : null;

        // Update paragraph
        $paragraph->update([
            'reply'          => $reply,
            'paragraph'      => $paraContent,
            'background'     => $background,
            'blue_flag'      => $request->blue_flag,
            'green_flag'     => $request->green_flag,
            'red_flag'       => $request->red_flag,
            'notes'          => $request->notes,
            'background_ref' => $request->background_ref,
            'title_above'    => $request->title_above,
            'number'         => $request->number,
            'para_numbers'   => $para_numbers,
            'reply_exhibits' => $reply_exhibits,
            'para_exhibits'  => $para_exhibits,
        ]);

        // لو فيه رد يتعلم عليه إنه replyed
        if ($reply) {
            $paragraph->replyed       = "1";
            $paragraph->reply_user_id = auth()->user()->id;
            $paragraph->save();
        }

        return redirect('/project/para-wise-analysis/paragraphs/' . $paragraph->para_wise->slug)
            ->with('success', 'Paragraph Updated successfully.');
    }

}
