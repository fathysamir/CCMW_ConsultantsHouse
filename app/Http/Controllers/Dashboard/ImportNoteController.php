<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\FileDocument;
use App\Models\GanttChartDocData;
use App\Models\Note;
use App\Models\Project;
use App\Models\ProjectFolder;
use App\Models\StorageFile;
use DateTime;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportNoteController extends ApiController
{
    public function import_notes_view()
    {
        session()->forget('extractedDataExcelFile');
        session()->forget('selected_file');
        $folders = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users   = $project->assign_users;
        return view('project_dashboard.import_notes.index', compact('folders', 'users', 'project'));
    }

    public function upload_import_excel_file(Request $request)
    {

        $request->validate([
            'file' => 'required|file|max:51200', // 10MB max
        ]);

        $file        = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());

        $data   = [];
        $sheets = [];
        foreach ($spreadsheet->getSheetNames() as $sheetName) {

            $sheet = $spreadsheet->getSheetByName($sheetName);
            $rows  = $sheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                continue; // Skip empty or invalid sheets
            }
            $sheets[]  = trim($sheetName);
            $headers   = array_map('trim', $rows[0]); // Extract headers
            $sheetData = [];

            foreach ($headers as $header) {
                $sheetData[$header] = [];
            }

            // Extract row data
            for ($i = 1; $i < count($rows); $i++) {
                foreach ($headers as $index => $header) {
                    if (isset($rows[$i][$index])) {
                        $sheetData[$header][] = $rows[$i][$index];
                    } else {
                        $sheetData[$header][] = null;
                    }
                }
            }

            $data[$sheetName] = $sheetData;
        }
        session(['extractedDataExcelFile' => $data]);

        $name = $file->getClientOriginalName();
        $size = $file->getSize();
        $type = $file->getMimeType();

        $storageFile = StorageFile::where('user_id', auth()->user()->id)->where('project_id', auth()->user()->current_project_id)->where('file_name', $name)->where('size', $size)->where('file_type', $type)->first();
        if ($storageFile) {
            return response()->json([
                'success' => true,
                'file'    => $storageFile,
                'sheets'  => $sheets,
            ]);
        }
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Create project-specific folder in public path
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/imports/excel';
        $path          = public_path($projectFolder);
        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Move file to public folder
        $file->move($path, $fileName);

        // Save file info to database
        $storageFile = StorageFile::create([
            'user_id'    => auth()->user()->id,
            'project_id' => auth()->user()->current_project_id,
            'file_name'  => $name,
            'size'       => $size,
            'file_type'  => $type,
            'path'       => $projectFolder . '/' . $fileName,
        ]);

        return response()->json([
            'success' => true,
            'file'    => $storageFile,
            'sheets'  => $sheets,
        ]);
    }

    public function getHeaders(Request $request)
    {
        $sheet = $request->input('sheet');
        session(['selected_file' => $request->input('file_id')]);

        $sheets       = session('extractedDataExcelFile');
        $headersArray = $sheets[$sheet];
        $headers      = [];
        foreach ($headersArray as $key => $val) {
            $headers[] = $key;
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'headers' => $headers,

        ]);
    }

    public function formate_date($date, $formate = 'd.M.Y')
    {

        $cleanedDate = preg_replace('/[^a-zA-Z0-9]/', '.', $date); // Replace any non-alphanumeric character with space
                                                                   // Create DateTime object from the original format (y/m/d)
        $dateTime = DateTime::createFromFormat($formate, $cleanedDate);

        if ($dateTime) {
            $formattedDate2 = $dateTime->format('Y-m-d');

            return $formattedDate2;
        } else {
            return null;
        }
    }

    public function start_import(Request $request)
    {

        $sheets = session('extractedDataExcelFile');

        $rowsCount      = $sheets[$request->sheet][$request->start_date];
        $unImportedRows = [];
        $importedRows   = [];

        $selected_file = session('selected_file');
        foreach ($rowsCount as $index => $val) {
            $sections      = [];
            $success       = true;
            $left_caption  = null;
            $right_caption = null;
            $subject       = null;
            $start_date    = null;
            $return_date   = null;
            $pl_sd         = null;
            $pl_fd         = null;
            $lp_sd         = null;
            $lp_fd         = null;
            $note          = null;

            if ($sheets[$request->sheet][$request->subject][$index] == null) {
                $unImportedRows['Row ' . $index + 2][] = '"' . $request->subject . '" is Empty';
                $success                               = false;
            } else {
                $subject = $sheets[$request->sheet][$request->subject][$index];
            }

            if ($sheets[$request->sheet][$request->start_date][$index] == null) {
                $unImportedRows['Row ' . $index + 2][] = '"' . $request->start_date . '" is Empty';
                $success                               = false;
            } else {
                // $start_date=date('Y-m-d',strtotime($sheets[$request->sheet][$request->start_date][$index]));
                $start_date = $this->formate_date($sheets[$request->sheet][$request->start_date][$index], 'd.M.Y');
            }

            if ($success == true) {
                if ($request->return_date) {
                    if ($sheets[$request->sheet][$request->return_date][$index] == null) {
                        $importedRows['Row ' . $index + 2][] = '"' . $request->return_date . '" is Empty';
                    } else {
                        // $return_date=date('Y-m-d',strtotime($sheets[$request->sheet][$request->end_date][$index]));
                        $return_date = $this->formate_date($sheets[$request->sheet][$request->return_date][$index], 'd.M.Y');

                    }
                }

                if ($request->left_caption) {
                    if ($sheets[$request->sheet][$request->left_caption][$index] == null) {
                        $importedRows['Row ' . $index + 2][] = '"' . $request->left_caption . '" is Empty';
                    }
                    $left_caption = $sheets[$request->sheet][$request->left_caption][$index];
                }
                if ($request->right_caption) {
                    if ($sheets[$request->sheet][$request->right_caption][$index] == null) {
                        $importedRows['Row ' . $index + 2][] = '"' . $request->right_caption . '" is Empty';
                    }
                    $right_caption = $sheets[$request->sheet][$request->right_caption][$index];
                }

                if ($request->pl_sd) {
                    if ($sheets[$request->sheet][$request->pl_sd][$index] == null) {
                        $importedRows['Row ' . $index + 2][] = '"' . $request->pl_sd . '" is Empty';
                    }
                    $pl_sd = $this->formate_date($sheets[$request->sheet][$request->pl_sd][$index], 'd.M.Y');
                }
                if ($request->pl_fd) {
                    if ($sheets[$request->sheet][$request->pl_fd][$index] == null) {
                        $importedRows['Row ' . $index + 2][] = '"' . $request->pl_fd . '" is Empty';
                    }
                    $pl_fd = $this->formate_date($sheets[$request->sheet][$request->pl_fd][$index], 'd.M.Y');
                }
                if ($request->lp_sd) {
                    if ($sheets[$request->sheet][$request->lp_sd][$index] == null) {
                        $importedRows['Row ' . $index + 2][] = '"' . $request->lp_sd . '" is Empty';
                    }
                    $lp_sd = $this->formate_date($sheets[$request->sheet][$request->lp_sd][$index], 'd.M.Y');
                }
                if ($request->lp_fd) {
                    if ($sheets[$request->sheet][$request->lp_fd][$index] == null) {
                        $importedRows['Row ' . $index + 2][] = '"' . $request->lp_fd . '" is Empty';
                    }
                    $lp_fd = $this->formate_date($sheets[$request->sheet][$request->lp_fd][$index], 'd.M.Y');
                }

                do {
                    $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
                } while (Note::where('slug', $invitation_code)->exists());

                $note = Note::create([
                    'slug'              => $invitation_code,
                    'user_id'           => $request->analyzed_By,
                    'project_id'        => auth()->user()->current_project_id,
                    'subject'           => $subject,
                    'start_date'        => $start_date,
                    'end_date'          => $return_date,
                    'note'              => $note,
                    'analysis_complete' => $request->analysis_complete,
                ]);
                if ($selected_file) {
                    $fileDoc     = FileDocument::create(['user_id' => auth()->user()->id, 'file_id' => $selected_file, 'note_id' => $note->id]);
                    $start_date  = $fileDoc->note->start_date;
                    $end_date    = $fileDoc->note->end_date;
                    $gantt_chart = GanttChartDocData::create(['file_document_id' => $fileDoc->id]);

                    $gantt_chart->lp_sd             = $lp_sd;
                    $gantt_chart->lp_fd             = $lp_fd;
                    $gantt_chart->pl_sd             = $pl_sd;
                    $gantt_chart->pl_fd             = $pl_fd;
                    $gantt_chart->cur_left_caption  = $left_caption;
                    $gantt_chart->cur_right_caption = $right_caption;
                    $gantt_chart->pl_color          = $request->pl_color;
                    $gantt_chart->pl_show_sd        = '1';
                    $gantt_chart->pl_show_fd        = '1';

                    $sections[] = [
                        'sd'    => $start_date,
                        'fd'    => $end_date,
                        'color' => $request->cur_color,
                    ];

                    $gantt_chart->cur_sections = json_encode($sections);
                    if ($end_date == null) {
                        $gantt_chart->cur_type = 'M';
                    }
                    if ($pl_fd == null) {
                        $gantt_chart->pl_type = 'M';
                    }
                    $gantt_chart->cur_show_ref = 'non';
                    $gantt_chart->save();
                }
                $importedRows['Row ' . $index + 2][] = 'Note is uploaded successfully';
            }

        }
        $html = view('project_dashboard.import_documents.report', compact('importedRows', 'unImportedRows'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Files Assigned successfully',
            'html'    => $html,
        ]);

    }
}
