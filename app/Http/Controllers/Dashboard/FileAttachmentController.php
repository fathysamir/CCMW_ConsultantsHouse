<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\ContractTag;
use App\Models\Document;
use App\Models\ExportFormate;
use App\Models\FileAttachment;
use App\Models\FileAttachmentFlag;
use App\Models\FileDocument;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectFolder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class FileAttachmentController extends ApiController
{
    public function index($id, $type)
    {
        $user     = auth()->user();
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $file        = ProjectFile::where('slug', $id)->first();
        $attachments = FileAttachment::where('file_id', $file->id)->where('section', $type)->orderBy('order', 'asc')
            ->get();
        $specific_file_attach = session('specific_file_attach');
        session()->forget('specific_file_attach');
        $folders          = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');
        $array_red_flags  = FileAttachmentFlag::where('user_id', auth()->user()->id)->where('flag', 'red')->pluck('file_attachment_id')->toArray();
        $array_blue_flags = FileAttachmentFlag::where('user_id', auth()->user()->id)->where('flag', 'blue')->pluck('file_attachment_id')->toArray();
        $Type_Name        = ['1' => 'Synopsis', '2' => 'Contractual Position', '3' => 'Cause-and-Effect Analysis'];
        $tags             = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->orderBy('order', 'asc')->get();

        return view('project_dashboard.file_attachments.index', compact('tags', 'Type_Name', 'type', 'array_red_flags', 'array_blue_flags', 'attachments', 'folders', 'file', 'specific_file_attach'));
    }

    public function create_attachment($type, $file_id)
    {
        $file      = ProjectFile::where('slug', $file_id)->first();
        $Type_Name = ['1' => 'Synopsis', '2' => 'Contractual Position', '3' => 'Cause-and-Effect Analysis'];

        return view('project_dashboard.file_attachments.create_attachment', compact('Type_Name', 'type', 'file'));
    }

    public function stor_file_attachment(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        if ($this->hasContent($request->narrative)) {
            $narrative = $request->narrative;
            $narrative = preg_replace('/<p>\s*(<img\b[^>]*>)\s*<\/p>/i', '$1', $narrative);

            $narrative = preg_replace_callback('/<img\b[^>]*>/i', function ($matches) {
                $img = $matches[0];
                if (preg_match('/<\/p>\s*$/i', substr($img, -10)) || preg_match('/^<p[^>]*>/i', substr($img, 0, 10))) {
                    return $img;
                }
                return "<p>$img</p>";
            }, $narrative);
        } else {
            $narrative = null;
        }

        $doc = FileAttachment::create([
            'user_id'   => auth()->user()->id,
            'section'   => $request->type,
            'file_id'   => $request->file_id,
            'narrative' => $narrative,
            'order'     => $request->order,
            'forClaim'  => $request->forClaim ? '1' : '0',

        ]);

        session(['specific_file_attach' => $doc->id]);

        return redirect('/project/file/' . $doc->file->slug . '/attachments/' . $doc->section)->with('success', 'File Attachment saved successfully.');

    }

    public function attachment($id)
    {
        $file_attachment = FileAttachment::where('id', $id)->first();
        session(['specific_file_attach' => $file_attachment->id]);

        return view('project_dashboard.file_attachments.attachment_analyses', compact('file_attachment'));

    }

    public function Update_file_attachment(Request $request, $id)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        if ($this->hasContent($request->narrative)) {
            $narrative = $request->narrative;
            $narrative = preg_replace('/<p>\s*(<img\b[^>]*>)\s*<\/p>/i', '$1', $narrative);

            $narrative = preg_replace_callback('/<img\b[^>]*>/i', function ($matches) {
                $img = $matches[0];
                if (preg_match('/<\/p>\s*$/i', substr($img, -10)) || preg_match('/^<p[^>]*>/i', substr($img, 0, 10))) {
                    return $img;
                }
                return "<p>$img</p>";
            }, $narrative);
        } else {
            $narrative = null;
        }
        $doc = FileAttachment::findOrFail($id);
        $doc->update([
            'narrative' => $narrative,
            'order'     => $request->order,
            'forClaim'  => $request->forClaim ? '1' : '0',

        ]);

        if ($request->action == 'save') {
            return redirect('/project/files_file/attachment/' . $doc->id)->with('success', 'File attachment saved successfully.');
        } else {
            return redirect('/project/file/' . $doc->file->slug . '/attachments/' . $doc->section)->with('success', 'File Attachment saved successfully.');
        }

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

    public function exportWordClaimAttachments(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        $chapter       = $request->Chapter; // Dynamic chapter number
        $sectionNumber = $request->Section; // Dynamic section number
        $phpWord->addNumberingStyle(
            'multilevel',
            [
                'type'     => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels'   => [
                    ['Heading0', 'format' => 'decimal', 'text' => '%1.', 'start' => (int) $chapter],
                    ['Heading1', 'format' => 'decimal', 'text' => '%1.%2', 'start' => (int) $sectionNumber],
                    ['Heading2', 'format' => 'decimal', 'text' => '%1.%2.%3', 'start' => 1],
                    ['Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.%4', 'start' => 1],
                    ['Heading3', 'format' => 'decimal', 'text' => ''],
                ],
            ]
        );

        $phpWord->addNumberingStyle(
            'multilevel2',
            [
                'type'     => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels'   => [
                    ['Heading5', 'format' => 'decimal', 'text' => '%1.'],
                    ['Heading6', 'format' => 'decimal', 'text' => '%1.%2.'],
                    ['Heading7', 'format' => 'decimal', 'text' => '%1.%2.%3.'],

                    // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                    // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                ],
            ]
        );
        $phpWord->addNumberingStyle(
            'unordered',
            [
                'type'   => 'multilevel', // Use 'multilevel' for bullet points
                'levels' => [
                    ['format' => 'bullet', 'text' => '•', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '◦', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '▪', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '■', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '☑', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➤', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➥', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➟', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➡', 'alignment' => 'left'],

                ],
            ]
        );
        $formate = ExportFormate::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->first();
        if ($formate) {
            $formate_values = $formate->value = json_decode($formate->value, true);
        } else {
            $formate_values = null;
        }
        // Define styles for headings
        $GetStandardStylesH1 = [
            'name'      => $formate_values ? $formate_values['h1']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h1']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['h1']['standard']['size']) : 24,
            'bold'      => $formate_values ? ($formate_values['h1']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['h1']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h1']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH1 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h1']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h1']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h1']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h1']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['h1']['paragraph']['indentation']['hanging'] * 1800) : 1000,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h1']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h1']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h1']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h1']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];
        $GetStandardStylesH2 = [
            'name'      => $formate_values ? $formate_values['h2']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h2']['standard']['alignment'] : 'left',
            'size'      => $formate_values ? intval($formate_values['h2']['standard']['size']) : 16,
            'bold'      => $formate_values ? ($formate_values['h2']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['h2']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h2']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH2 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h2']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h2']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h2']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h2']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['h2']['paragraph']['indentation']['hanging'] * 1800) : 1000,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h2']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h2']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h2']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h2']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];

        $GetStandardStylesH3 = [
            'name'      => $formate_values ? $formate_values['h3']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h3']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['h3']['standard']['size']) : 14,
            'bold'      => $formate_values ? ($formate_values['h3']['standard']['bold'] == '1' ? true : false) : false,
            'italic'    => $formate_values ? ($formate_values['h3']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h3']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH3 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h3']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h3']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h3']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h3']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['h3']['paragraph']['indentation']['hanging'] * 1800) : 1000,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h3']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h3']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h3']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h3']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];

        $GetStandardStylesSubtitle = [
            'name'      => $formate_values ? $formate_values['subtitle']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['subtitle']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['subtitle']['standard']['size']) : 14,
            'bold'      => $formate_values ? ($formate_values['subtitle']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['subtitle']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['subtitle']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleSubtitle = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['subtitle']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['subtitle']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['subtitle']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['subtitle']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['subtitle']['paragraph']['indentation']['hanging'] * 1800) : 0,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['subtitle']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['subtitle']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['subtitle']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['subtitle']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];
        $GetStandardStylesP = [
            'name'      => $formate_values ? $formate_values['body']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['body']['standard']['size']) : 11,
            'bold'      => false,
            'italic'    => false,
            'underline' => 'none',
        ];

        $phpWord->addParagraphStyle('listParagraphStyle', [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['hanging'] * 1800) : 1000,
                'firstLine' => 0,
            ],
            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ]);

        $phpWord->addParagraphStyle('listParagraphStyle2', [
            'spaceBefore'       => 0,
            'spaceAfter'        => 20,
            'lineHeight'        => 1,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1800) + 350 : 1350,
                'hanging'   => 337.5,
                'firstLine' => 0,
            ],
            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ]);

        $phpWord->addTitleStyle(1, $GetStandardStylesH1, $GetParagraphStyleH1);
        $phpWord->addTitleStyle(2, $GetStandardStylesH2, array_merge($GetParagraphStyleH2, ['numStyle' => 'multilevel', 'numLevel' => 1]));
        $phpWord->addTitleStyle(3, $GetStandardStylesH3, $GetParagraphStyleH3);

        $file = ProjectFile::where('slug', $request->file_id111)->first();
        // Header (Level 1 Outline)
        $header = $file->name;
        $header = str_replace('&', '&amp;', $header);
        $section->addTitle($header, 2);

        // $section->addListItem($header, 0, ['size' => 16,'bold' => true,], 'multilevel');
        // dd($section);

        $subtitle = $request->subtitle;
        $subtitle = str_replace('&', '&amp;', $subtitle);
        $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);

        // Paragraphs
        // $paragraphs=FileDocument::where('file_id',$file->id)->where('forClaim','1')->orderBy()->get();
        $paragraphs = FileAttachment::where('section', $request->attach_type)
            ->where('file_id', $file->id);
        if ($request->forclaimdocs) {
            $paragraphs->where('forClaim', '1');
        }

        $paragraphs = $paragraphs->orderBy('order', 'asc')->get();

        foreach ($paragraphs as $index => $paragraph) {
            // dd($paragraphs);
            $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
            $existedList = false;
            // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
            $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;

            if ($paragraph->narrative == null) {
                $listItemRun->addText('____________.');
            } else {
                if (! $containsHtml) {
                    $listItemRun->addText($paragraph->narrative . '.');
                } else {

                    $paragraph_ = $this->fixParagraphsWithImages($paragraph->narrative);

                    $paragraphWithoutImagesAndBreaks = preg_replace('/<(br)[^>]*>/i', '', $paragraph_);

                    // Step 2: Remove empty <p></p> tags
                    $paragraphWithoutEmptyParagraphs = preg_replace('/<p>\s*<\/p>/i', '', $paragraphWithoutImagesAndBreaks);

                    $paragraphsArray = $this->splitHtmlToArray($paragraphWithoutEmptyParagraphs);

                    $paragraphsArray = array_filter($paragraphsArray, function ($item) {
                        return ! empty(trim($item));
                    });

                    // Step 5: Add each <p> tag to the document with a newline after it
                    foreach ($paragraphsArray as $index2 => $pTag) {
                        // dd($paragraphsArray);
                        if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                            $imgPath       = $matches[1];                                 // Extract image path
                            $altText       = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                            $fullImagePath = public_path($imgPath);                       // Convert relative path to absolute

                            if ($existedList) {
                                if (file_exists($fullImagePath)) {
                                    $textRun = $section->addTextRun([
                                        'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                        'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                        'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                        'indentation' => [
                                            'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1800) : 1000,
                                        ],
                                    ]);

                                    // Add Image
                                    $shape = $textRun->addImage($fullImagePath, [
                                        'width'     => 100,
                                        'height'    => 80,
                                        'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                    ]);

                                    // Add Caption (Alt text)
                                    if (! empty($altText)) {
                                        $textRun->addTextBreak(); // New line
                                        $textRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                            'alignment'                               => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                            'size'                                    => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                            'bold'                                    => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                            'italic'                                  => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                            'underline'                               => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                    }

                                    if ($index2 < count($paragraphsArray) - 1) {

                                        if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                            $textRun->addTextBreak();
                                        }

                                    }
                                }
                            } else {
                                if (file_exists($fullImagePath)) {
                                    // Add Image
                                    $listItemRun->addImage($fullImagePath, [
                                        'width'     => 100,
                                        'height'    => 80,
                                        'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                    ]);

                                    // Add Caption (Alt text)
                                    if (! empty($altText)) {
                                        $listItemRun->addTextBreak(); // New line
                                        $listItemRun->addText($altText . '.', ['name' => $formate_values ? $formate_values['figure']['standard']['name'] : 'Calibri',
                                            'alignment'                                   => $formate_values ? $formate_values['figure']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                                            'size'                                        => $formate_values ? intval($formate_values['figure']['standard']['size']) : 9,
                                            'bold'                                        => $formate_values ? ($formate_values['figure']['standard']['bold'] == '1' ? true : false) : false,
                                            'italic'                                      => $formate_values ? ($formate_values['figure']['standard']['italic'] == '1' ? true : false) : true,
                                            'underline'                                   => $formate_values ? ($formate_values['figure']['standard']['underline'] == '1' ? 'single' : 'none') : 'none']); // Add caption in italics
                                    }

                                    if ($index2 < count($paragraphsArray) - 1) {

                                        if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                            $listItemRun->addTextBreak();
                                        }

                                    }
                                }
                            }
                        } elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                            $imgPath = $matches[1]; // Extract image path

                            $fullImagePath = public_path($imgPath); // Convert relative path to absolute

                            if ($existedList) {
                                if (file_exists($fullImagePath)) {
                                    $textRun = $section->addTextRun([
                                        'spaceBefore' => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                        'spaceAfter'  => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                        'lineHeight'  => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1,
                                        'indentation' => [
                                            'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1800) : 1000,
                                        ],
                                    ]);

                                    // Add Image
                                    $textRun->addImage($fullImagePath, [
                                        'width'     => 100,
                                        'height'    => 80,
                                        'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                    ]);

                                    if ($index2 < count($paragraphsArray) - 1) {

                                        if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                            $textRun->addTextBreak();
                                        }

                                    }
                                }
                            } else {
                                if (file_exists($fullImagePath)) {
                                    // Add Image
                                    $listItemRun->addImage($fullImagePath, [
                                        'width'     => 100,
                                        'height'    => 80,
                                        'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left',
                                    ]);
                                    if ($index2 < count($paragraphsArray) - 1) {

                                        if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                            $listItemRun->addTextBreak();
                                        }

                                    }

                                }
                            }
                        } elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                            $phpWord->addNumberingStyle(
                                'multilevel_1' . $index . $index2,
                                [
                                    'type'     => 'multilevel',
                                    'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                    'levels'   => [
                                        ['Heading5', 'format' => 'decimal', 'text' => '%1.'],

                                        // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                        // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                    ],
                                ]
                            );
                            if (preg_match_all('/<li>(.*?)<\/li>/', $olMatches[1], $liMatches)) {
                                $listItems = $liMatches[1] ?? [];

                                // Add each list item as a nested list item
                                foreach ($listItems as $item) {
                                                                                                                                                // Add a nested list item
                                    $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1' . $index . $index2, 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                                // $nestedListItemRun->addText($item);
                                    $item = str_replace('&', '&amp;', $item);
                                    $item = '<span style="font-size:'
                                        . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                        . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                        . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                        . $item . '</span>';
                                    Html::addHtml($nestedListItemRun, $item, false, false);
                                }
                            }
                            $existedList = true;
                        } elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                            if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                $listItems = $liMatches[1] ?? [];

                                // Add each list item as a nested list item
                                foreach ($listItems as $item) {
                                                                                                                            // Add a nested list item
                                                                                                                            // dd($listItems);
                                    $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                            // $unNestedListItemRun->addText($item);
                                    $item = str_replace('&', '&amp;', $item);
                                    $item = '<span style="font-size:'
                                        . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                        . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                        . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                        . $item . '</span>';
                                    Html::addHtml($unNestedListItemRun, $item, false, false);
                                }
                            }

                            $existedList = true;
                        } else {

                            // If the paragraph contains only text (including <span>, <strong>, etc.)
                            try {
                                if ($existedList) {

                                    $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                        'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                        'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                        'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                                        'indentation'       => [
                                            'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1800) : 1000,

                                        ],
                                        'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                        'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                        'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                        'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                        'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                        'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                                    ]);
                                    $pTag = $this->lowercaseFirstCharOnly($pTag);
                                    $pTag = str_replace('&', '&amp;', $pTag);
                                    $pTag = '<span style="font-size:'
                                        . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                        . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                        . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                        . $pTag . '</span>';
                                    Html::addHtml($listItemRun2, $pTag, false, false);
                                    if ($index2 < count($paragraphsArray) - 1) {

                                        if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                            $listItemRun2->addTextBreak();
                                        }

                                    }
                                } else {
                                    // $pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');

                                    $pTag = $this->lowercaseFirstCharOnly($pTag);
                                    $pTag = str_replace('&', '&amp;', $pTag);
                                    $pTag = '<span style="font-size:'
                                        . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                        . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                        . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                        . $pTag . '</span>';
                                    Html::addHtml($listItemRun, $pTag, false, false);

                                    if ($index2 < count($paragraphsArray) - 1) {

                                        if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                            $listItemRun->addTextBreak();
                                        }

                                    }
                                }

                            } catch (\Exception $e) {
                                error_log('Error adding HTML: ' . $e->getMessage());
                            }
                        }

                    }

                }
            }

        }
        if (count($paragraphs) > 0 && $request->attach_type == 2 && $request->add_note == 'on') {
            $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
            $existedList = false;
            $pTag        = "<p>" . $request->note . "</p>";
            $pTag        = $this->lowercaseFirstCharOnly($pTag);
            $pTag        = str_replace('&', '&amp;', $pTag);

            Html::addHtml($listItemRun, $pTag, false, false);

            $tags_array = $request->tags;
            $documents  = FileDocument::whereHas('document')
                ->where('file_id', $file->id)->
                with('tags')->whereHas('tags', function ($query) use ($tags_array) {
                $query->whereIn('contract_tag_id', $tags_array);
            })->get()->sortBy([
                fn($a, $b) => ($a->document->start_date ?? '9999-12-31') <=> ($b->document->start_date ?? '9999-12-31'),
                fn($a, $b) => $a->sn <=> $b->sn,
            ])
                ->values();

            if (count($documents) > 0) {
                $table = $section->addTable();

                $table->addRow();
                $table->addCell(5000)->addText('Reference', ['name' => 'Arial', 'size' => 10, 'bold' => true], $GetParagraphStyleSubtitle);
                $table->addCell(3000)->addText('Date', ['name' => 'Arial', 'size' => 10, 'bold' => true], $GetParagraphStyleSubtitle);

                foreach ($documents as $doc) {
                    $ref  = str_replace('&', '&amp;', $doc->document->reference);
                    $date = date('d.M.Y', strtotime($doc->document->start_date));

                    $table->addRow();
                    $table->addCell(5000)->addText($ref, ['name' => 'Arial', 'size' => 10], $GetParagraphStyleSubtitle);
                    $table->addCell(3000)->addText($date, ['name' => 'Arial', 'size' => 10], $GetParagraphStyleSubtitle);
                }
            }

        }

        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
        $path          = public_path($projectFolder);
        if (! file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $code      = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

        if (! file_exists($directory)) {
            mkdir($directory, 0755, true); // true = create nested directories
        }
        // Save document
        // Define file path in public folder
        $fileName = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $file->code . '_' . $header . '.docx';
        $filePath = public_path($fileName);

        // Save document to public folder
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);
        session(['zip_file' => $code]);

        return response()->json(['download_url' => asset($fileName)]);
        // Return file as a response and delete after download
        // return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function lowercaseFirstCharOnly($html)
    {
        return preg_replace_callback(
            '/(?:^|>)(T)/u', // Match only "T" after start or closing tag
            function ($matches) {
                return str_replace('T', 't', $matches[0]);
            },
            $html,
            1// Only first match
        );
    }

    public function splitHtmlToArray($html)
    {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true); // Prevent warnings from invalid HTML
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $resultArray = [];
        $xpath       = new \DOMXPath($dom);
        $elements    = $xpath->query('//p | //ul | //ol'); // Select only <p>, <ul>, and <ol> elements

        foreach ($elements as $element) {
            $resultArray[] = $dom->saveHTML($element); // Store each element as a separate string
        }

        return $resultArray;
    }

    public function fixParagraphsWithImages($html)
    {
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath     = new \DOMXPath($dom);
        $pElements = $xpath->query('//p');

        foreach ($pElements as $p) {
            $newNodes        = [];
            $currentFragment = new \DOMDocument;
            $newP            = $dom->createElement('p'); // Use the original document to avoid Wrong Document Error

            foreach (iterator_to_array($p->childNodes) as $child) {
                if ($child->nodeName === 'img') {
                    // If the current <p> already has text, save it
                    if ($newP->hasChildNodes()) {
                        $newNodes[] = $newP;
                        $newP       = $dom->createElement('p');
                    }

                    // Create a new <p> for the image
                    $imgP        = $dom->createElement('p');
                    $importedImg = $dom->importNode($child, true); // Import the image to avoid Wrong Document Error
                    $imgP->appendChild($importedImg);
                    $newNodes[] = $imgP;

                    // Start a new <p> for the remaining content
                    $newP = $dom->createElement('p');
                } else {
                    $importedNode = $dom->importNode($child, true); // Import text nodes to avoid errors
                    $newP->appendChild($importedNode);
                }
            }

            // If there's leftover text, add it as a new <p>
            if ($newP->hasChildNodes()) {
                $newNodes[] = $newP;
            }

            // Replace original <p> with the new structured <p> elements
            $parent = $p->parentNode;
            foreach ($newNodes as $newNode) {
                $parent->insertBefore($newNode, $p);
            }
            $parent->removeChild($p);
        }

        // Clean up the output and return formatted HTML
        $cleanHtml = $dom->saveHTML();

        return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<html>', '</html>', '<body>', '</body>'], '', $cleanHtml));
    }

    public function get_attachment_narrative(Request $request)
    {
        $doc  = FileAttachment::findOrFail($request->document_id);
        $html = $doc->narrative;

        return response()->json([
            'html' => $html,
        ]);
    }

    public function delete_attachments(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        FileAttachment::whereIn('id', $request->document_ids)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => count($request->document_ids) > 1 ? 'Selected Attachments is deleted successfully.' : 'Attachment is deleted successfully.',
            // 'redirect' => url('/project/file/' . $currentFile . '/documents')
        ]);
    }

    public function change_flag(Request $request)
    {
        $docId = $request->docId;
        $type  = $request->type;

        $record = FileAttachmentFlag::where('user_id', auth()->user()->id)->where('file_attachment_id', $docId)->where('flag', $type)->first();
        if ($record) {
            $record->delete();

            return response()->json([
                'success' => false,
            ]);
        } else {
            FileAttachmentFlag::create(['user_id' => auth()->user()->id, 'file_attachment_id' => $docId, 'flag' => $type]);

            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function change_for_claimOrNoticeOrChart(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        if (count($request->document_ids) > 1) {
            $do = FileAttachment::findOrFail($request->document_ids[0]);
            $ac = $request->action_type;
            if ($do->$ac == '1') {
                $va = '0';
            } else {
                $va = '1';
            }
            FileAttachment::whereIn('id', $request->document_ids)->update([$ac => $va]);

            return response()->json([
                'status' => 'success',
                'value'  => $va,
            ]);
        } else {
            FileAttachment::whereIn('id', $request->document_ids)->update([$request->action_type => $request->val]);

            return response()->json([
                'status' => 'success',
            ]);
        }

    }

    public function copy_move_attachment_to_another_file(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }

        if ($request->actionType == 'Copy') {
            foreach ($request->document_ids as $doc_id) {
                $file_doc = FileAttachment::findOrFail($doc_id);

                $fileDoc = FileAttachment::where('file_id', $request->file_id)->where('narrative', $file_doc->narrative)->where('section', $file_doc->section)->where('order', $file_doc->order)->first();

                if (! $fileDoc) {
                    $doc = FileAttachment::create(['user_id' => auth()->user()->id,
                        'file_id'                                => $request->file_id,
                        'narrative'                              => $file_doc->narrative,
                        'section'                                => $file_doc->section,
                        'order'                                  => $file_doc->order,
                        'forClaim'                               => $file_doc->forClaim,
                    ]);

                }

            }

            return response()->json([
                'status'  => 'success',
                'message' => count($request->document_ids) > 1 ? 'Selected Attachments Copied To Selected File Successfully.' : 'Attachment Copied To Selected File Successfully.',

                // 'redirect' => url('/project/file/' . $file_doc->file->slug . '/documents')
            ]);
        } elseif ($request->actionType == 'Move') {
            foreach ($request->document_ids as $doc_id) {
                $file_doc = FileAttachment::findOrFail($doc_id);
                $fileDoc  = FileAttachment::where('file_id', $request->file_id)->where('narrative', $file_doc->narrative)->where('section', $file_doc->section)->where('order', $file_doc->order)->first();
                if (! $fileDoc) {
                    $file_doc->file_id = $request->file_id;
                    $file_doc->save();
                }
            }

            return response()->json([
                'status'  => 'success',
                'message' => count($request->document_ids) > 1 ? 'Selected Attachments Moved To Selected File Successfully.' : 'Attachment Moved To Selected File Successfully.',
                // 'redirect' => url('/project/file/' . $currentFile . '/documents')
            ]);
        }
    }

    public function export_fill_using_AI(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $phpWord = new PhpWord;
        $section = $phpWord->addSection();

        $chapter       = 1; // Dynamic chapter number
        $sectionNumber = 0; // Dynamic section number
        $phpWord->addNumberingStyle(
            'multilevel',
            [
                'type'     => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels'   => [
                    ['Heading0', 'format' => 'decimal', 'text' => '%1.', 'start' => (int) $chapter],
                    ['Heading1', 'format' => 'decimal', 'text' => '%1.%2', 'start' => (int) $sectionNumber],
                    ['Heading2', 'format' => 'decimal', 'text' => '%1.%2.%3', 'start' => 1],
                    ['Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.%4', 'start' => 1],
                    ['Heading3', 'format' => 'decimal', 'text' => ''],
                ],
            ]
        );

        $phpWord->addNumberingStyle(
            'multilevel2',
            [
                'type'     => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels'   => [
                    ['Heading5', 'format' => 'decimal', 'text' => '%1.'],
                    ['Heading6', 'format' => 'decimal', 'text' => '%1.%2.'],
                    ['Heading7', 'format' => 'decimal', 'text' => '%1.%2.%3.'],

                    // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                    // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                ],
            ]
        );
        $phpWord->addNumberingStyle(
            'unordered',
            [
                'type'   => 'multilevel', // Use 'multilevel' for bullet points
                'levels' => [
                    ['format' => 'bullet', 'text' => '•', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '◦', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '▪', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '■', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '☑', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➤', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➥', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➟', 'alignment' => 'left'],
                    ['format' => 'bullet', 'text' => '➡', 'alignment' => 'left'],

                ],
            ]
        );
        $formate = ExportFormate::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->first();
        if ($formate) {
            $formate_values = $formate->value = json_decode($formate->value, true);
        } else {
            $formate_values = null;
        }
        // Define styles for headings
        $GetStandardStylesH1 = [
            'name'      => $formate_values ? $formate_values['h1']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h1']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['h1']['standard']['size']) : 24,
            'bold'      => $formate_values ? ($formate_values['h1']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['h1']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h1']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH1 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h1']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h1']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h1']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h1']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['h1']['paragraph']['indentation']['hanging'] * 1800) : 1000,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h1']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h1']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h1']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h1']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];
        $GetStandardStylesH2 = [
            'name'      => $formate_values ? $formate_values['h2']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h2']['standard']['alignment'] : 'left',
            'size'      => $formate_values ? intval($formate_values['h2']['standard']['size']) : 16,
            'bold'      => $formate_values ? ($formate_values['h2']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['h2']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h2']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH2 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h2']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h2']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h2']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h2']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['h2']['paragraph']['indentation']['hanging'] * 1800) : 1000,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h2']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h2']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h2']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h2']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];

        $GetStandardStylesH3 = [
            'name'      => $formate_values ? $formate_values['h3']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['h3']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['h3']['standard']['size']) : 14,
            'bold'      => $formate_values ? ($formate_values['h3']['standard']['bold'] == '1' ? true : false) : false,
            'italic'    => $formate_values ? ($formate_values['h3']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['h3']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleH3 = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['h3']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['h3']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['h3']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['h3']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['h3']['paragraph']['indentation']['hanging'] * 1800) : 1000,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['h3']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['h3']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['h3']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['h3']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];

        $GetStandardStylesP = [
            'name'      => $formate_values ? $formate_values['body']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['body']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['body']['standard']['size']) : 11,
            'bold'      => false,
            'italic'    => false,
            'underline' => 'none',
        ];

        $phpWord->addParagraphStyle('listParagraphStyle', [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['hanging'] * 1800) : 1000,
                'firstLine' => 0,
            ],
            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ]);

        $phpWord->addParagraphStyle('listParagraphStyle2', [
            'spaceBefore'       => 0,
            'spaceAfter'        => 20,
            'lineHeight'        => 1,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1800) + 350 : 1350,
                'hanging'   => 337.5,
                'firstLine' => 0,
            ],
            'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
            'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
            'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
            'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ]);

        $phpWord->addTitleStyle(1, $GetStandardStylesH1, $GetParagraphStyleH1);
        $phpWord->addTitleStyle(2, $GetStandardStylesH2, array_merge($GetParagraphStyleH2, ['numStyle' => 'multilevel', 'numLevel' => 1]));
        $phpWord->addTitleStyle(3, $GetStandardStylesH3, $GetParagraphStyleH3);

        $file = ProjectFile::where('slug', $request->fileSlug)->first();
        // Header (Level 1 Outline)
        $header = $file->name;
        $header = str_replace('&', '&amp;', $header);
        if ($request->type == 1) {
            $paragraphs = FileDocument::with(['document', 'note'])
                ->where('file_id', $file->id)->where('forClaim', '1');

            $paragraphs = $paragraphs->get()
                ->sortBy([
                    fn($a, $b) => ($a->document->start_date ?? $a->note->start_date ?? '9999-12-31')
                    <=> ($b->document->start_date ?? $b->note->start_date ?? '9999-12-31'),
                    fn($a, $b) => $a->sn <=> $b->sn,
                ])
                ->values();

            $GetStandardStylesFootNotes = [
                'name'      => $formate_values ? $formate_values['footnote']['standard']['name'] : 'Calibri',
                'alignment' => $formate_values ? $formate_values['footnote']['standard']['alignment'] : 'left', // Options: left, center, right, justify
                'size'      => $formate_values ? intval($formate_values['footnote']['standard']['size']) : 9,
                'bold'      => $formate_values ? ($formate_values['footnote']['standard']['bold'] == '1' ? true : false) : false,
                'italic'    => $formate_values ? ($formate_values['footnote']['standard']['italic'] == '1' ? true : false) : false,
                'underline' => $formate_values ? ($formate_values['footnote']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

            ];
            $GetParagraphStyleFootNotes = [
                'spaceBefore' => $formate_values ? ((int) $formate_values['footnote']['paragraph']['spaceBefore'] * 20) : 0,
                'spaceAfter'  => $formate_values ? ((int) $formate_values['footnote']['paragraph']['spaceAfter'] * 20) : 0,
                'lineHeight'  => $formate_values ? (float) $formate_values['footnote']['paragraph']['lineHeight'] : 1,
                'indentation' => [
                    'left'      => $formate_values ? ((float) $formate_values['footnote']['paragraph']['indentation']['left'] * 1800) : 0,
                    'hanging'   => $formate_values ? ((float) $formate_values['footnote']['paragraph']['indentation']['hanging'] * 1800) : 0,
                    'firstLine' => 0,
                ],
            ];
            $x = 1;
            foreach ($paragraphs as $index => $paragraph) {
                // dd($paragraphs);
                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                $existedList = false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;
                if ($paragraph->document) {
                    $date = date('d F Y', strtotime($paragraph->document->start_date));

                    // Add the main sentence
                    $listItemRun->addText('On ', $GetStandardStylesP);

                    // Add the date with a footnote
                    $listItemRun->addText($date, $GetStandardStylesP);
                    $footnote   = $listItemRun->addFootnote($GetParagraphStyleFootNotes);
                    $hint       = '';
                    $sn         = 2;
                    $prefix     = 'Exhibit 1.0.';
                    $listNumber = "$prefix" . str_pad($x, $sn, '0', STR_PAD_LEFT);
                    $hint       = $listNumber . ': ';
                    $from       = $paragraph->document->fromStakeHolder ? $paragraph->document->fromStakeHolder->narrative . "'s " : '';
                    $type       = $paragraph->document->docType->name;
                    $hint .= $from . $type . ' ';
                    if (str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $paragraph->document->docType->name)), 'email') || str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $paragraph->document->docType->description)), 'email')) {

                        $hint .= ', ';

                    } else {
                        $hint .= 'Ref: ' . $paragraph->document->reference . ', ';
                    }
                    $hint .= 'dated: ' . $date . '.';

                    $footnote->addText($hint, $GetStandardStylesFootNotes);
                    $listItemRun->addText(', ', $GetStandardStylesP);
                    $x++;
                } else {
                    $listItemRun->addText('Note: ', $GetStandardStylesP);
                }

                if ($paragraph->narrative == null) {
                    $listItemRun->addText('____________.', $GetStandardStylesP);
                } else {
                    if (! $containsHtml) {
                        $listItemRun->addText($paragraph->narrative . '.');
                    } else {

                        $paragraph_ = $this->fixParagraphsWithImages($paragraph->narrative);

                        $paragraphWithoutImagesAndBreaks = preg_replace('/<(br)[^>]*>/i', '', $paragraph_);

                        // Step 2: Remove empty <p></p> tags
                        $paragraphWithoutEmptyParagraphs = preg_replace('/<p>\s*<\/p>/i', '', $paragraphWithoutImagesAndBreaks);

                        $paragraphsArray = $this->splitHtmlToArray($paragraphWithoutEmptyParagraphs);

                        $paragraphsArray = array_filter($paragraphsArray, function ($item) {
                            return ! empty(trim($item));
                        });

                        foreach ($paragraphsArray as $index2 => $pTag) {
                            // dd($paragraphsArray);
                            if (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1' . $index . $index2,
                                    [
                                        'type'     => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels'   => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.'],

                                            // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                            // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                        ],
                                    ]
                                );
                                if (preg_match_all('/<li>(.*?)<\/li>/', $olMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                                    // Add a nested list item
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1' . $index . $index2, 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                                    // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList = true;
                            } elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                // Add a nested list item
                                                                                                                                // dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $item . '</span>';

                                        Html::addHtml($unNestedListItemRun, $item, false, false);
                                    }
                                }

                                $existedList = true;
                            } else {

                                // If the paragraph contains only text (including <span>, <strong>, etc.)
                                try {
                                    if ($existedList) {

                                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                            'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                        'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                        'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                                        'indentation'       => [
                                            'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1800) : 1000,

                                        ],
                                        'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                        'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                        'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                        'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                        'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                        'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                                        ]);
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun2->addTextBreak();
                                            }

                                        }
                                    } else {
                                        // $pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun, $pTag, false, false);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }

                                } catch (\Exception $e) {
                                    error_log('Error adding HTML: ' . $e->getMessage());
                                }
                            }
                        }
                    }
                }
            }

            $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
            $path          = public_path($projectFolder);
            if (! file_exists($path)) {

                mkdir($path, 0755, true);
            }
            $code      = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

            if (! file_exists($directory)) {
                mkdir($directory, 0755, true); // true = create nested directories
            }
            // Save document
            // Define file path in public folder
            $fileName = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $file->code . '_' . $header . '.docx';
            $filePath = public_path($fileName);

            // Save document to public folder
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($filePath);
            session(['zip_file' => $code]);
            $phpWord2 = IOFactory::load($filePath, 'Word2007');

            $text = '';
            foreach ($phpWord2->getSections() as $section) {
                $elements = $section->getElements();
                foreach ($elements as $element) {
                    if (method_exists($element, 'getText')) {
                        if ($element->getText() != 'Note: ____________.') {
                            $text .= $element->getText() . "\n";

                        }
                    }
                }
            }

            $pattern = '/(?=On\s\d{2}\s(?:January|February|March|April|May|June|July|August|September|October|November|December)\s\d{4},)|(?=Note:\s)/';

            $documents = preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

            $formattedText = implode("\n\n\n", array_map('trim', $documents));

            $formattedText .= "\n\n\n\n\n\n";
            $formattedText .= 'Please follow the following rules in your response:
            1 - Provide your response in paragraphs. Do not use bullet points.
            2 - Make your response as short as possible.
            3 - Provide key dates in the format “D MMMM YYYY”.
            Based on the above rules please provide a synopsis about the Causes that delayed the ' . $request->claimant . ', and based on the following chronology of events:';
        } elseif ($request->type == 2) {
            $paragraphs = FileAttachment::where('section', '1')
                ->where('file_id', $file->id)->where('forClaim', '1');

            $paragraphs = $paragraphs->orderBy('order', 'asc')->get();

            foreach ($paragraphs as $index => $paragraph) {
                // dd($paragraphs);
                $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                $existedList = false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;

                if ($paragraph->narrative == null) {
                    $listItemRun->addText('____________.');
                } else {
                    if (! $containsHtml) {
                        $listItemRun->addText($paragraph->narrative . '.');
                    } else {

                        $paragraph_ = $this->fixParagraphsWithImages($paragraph->narrative);

                        $paragraphWithoutImagesAndBreaks = preg_replace('/<(br)[^>]*>/i', '', $paragraph_);

                        // Step 2: Remove empty <p></p> tags
                        $paragraphWithoutEmptyParagraphs = preg_replace('/<p>\s*<\/p>/i', '', $paragraphWithoutImagesAndBreaks);

                        $paragraphsArray = $this->splitHtmlToArray($paragraphWithoutEmptyParagraphs);

                        $paragraphsArray = array_filter($paragraphsArray, function ($item) {
                            return ! empty(trim($item));
                        });

                        // Step 5: Add each <p> tag to the document with a newline after it
                        foreach ($paragraphsArray as $index2 => $pTag) {
                            // dd($paragraphsArray);
                            if (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1' . $index . $index2,
                                    [
                                        'type'     => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels'   => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.'],

                                            // array_merge([$this->paragraphStyleName => 'Heading3', 'format' => 'decimal', 'text' => '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                            // array_merge(['format' => 'decimal', 'text' =>   '%1.%2.%3.'], $this->PageParagraphFontStyle),
                                        ],
                                    ]
                                );
                                if (preg_match_all('/<li>(.*?)<\/li>/', $olMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                                    // Add a nested list item
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1' . $index . $index2, 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                                    // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                         $item = '<span style="font-size:'
                                        . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                        . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                        . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                        . $item . '</span>';
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList = true;
                            } elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                                                                                                                // Add a nested list item
                                                                                                                                // dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                         $item = '<span style="font-size:'
                                        . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                        . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                        . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                        . $item . '</span>';
                                        Html::addHtml($unNestedListItemRun, $item, false, false);
                                    }
                                }

                                $existedList = true;
                            } else {

                                // If the paragraph contains only text (including <span>, <strong>, etc.)
                                try {
                                    if ($existedList) {

                                        $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                           'spaceBefore'       => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceBefore'] * 20) : 0,
                                        'spaceAfter'        => $formate_values ? ((int) $formate_values['body']['paragraph']['spaceAfter'] * 20) : 240,
                                        'lineHeight'        => $formate_values ? (float) $formate_values['body']['paragraph']['lineHeight'] : 1.5,
                                        'indentation'       => [
                                            'left' => $formate_values ? ((float) $formate_values['body']['paragraph']['indentation']['left'] * 1800) : 1000,

                                        ],
                                        'keepLines'         => $formate_values ? ($formate_values['body']['paragraph']['keepLines'] == '1' ? true : false) : true,
                                        'hyphenation'       => $formate_values ? ($formate_values['body']['paragraph']['hyphenation'] == '1' ? true : false) : false,
                                        'contextualSpacing' => $formate_values ? ($formate_values['body']['paragraph']['contextualSpacing'] == '1' ? true : false) : false,
                                        'keepNext'          => $formate_values ? ($formate_values['body']['paragraph']['keepNext'] == '1' ? true : false) : true,
                                        'widowControl'      => $formate_values ? ($formate_values['body']['paragraph']['widowControl'] == '1' ? true : false) : true,
                                        'pageBreakBefore'   => $formate_values ? ($formate_values['body']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

                                        ]);
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun2->addTextBreak();
                                            }

                                        }
                                    } else {
                                        // $pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:'
                                            . ($formate_values ? intval($formate_values['body']['standard']['size']) : 11) . 'pt; '
                                            . 'font-family:' . ($formate_values ? $formate_values['body']['standard']['name'] : 'Arial') . '; '
                                            . 'text-align:' . ($formate_values ? $formate_values['body']['standard']['alignment'] : 'left') . ';">'
                                            . $pTag . '</span>';
                                        Html::addHtml($listItemRun, $pTag, false, false);

                                        if ($index2 < count($paragraphsArray) - 1) {

                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                                $listItemRun->addTextBreak();
                                            }

                                        }
                                    }

                                } catch (\Exception $e) {
                                    error_log('Error adding HTML: ' . $e->getMessage());
                                }
                            }

                        }

                    }
                }

            }
            $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
            $path          = public_path($projectFolder);
            if (! file_exists($path)) {

                mkdir($path, 0755, true);
            }
            $code      = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

            if (! file_exists($directory)) {
                mkdir($directory, 0755, true); // true = create nested directories
            }
            // Save document
            // Define file path in public folder
            $fileName = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $file->code . '_' . $header . '.docx';
            $filePath = public_path($fileName);

            // Save document to public folder
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($filePath);
            session(['zip_file' => $code]);
            $phpWord2 = IOFactory::load($filePath, 'Word2007');

            $text = '';
            foreach ($phpWord2->getSections() as $section) {
                $elements = $section->getElements();
                foreach ($elements as $element) {
                    if (method_exists($element, 'getText')) {
                        if ($element->getText() != 'Note: ____________.') {
                            $text .= $element->getText() . "\n";

                        }
                    }
                }
            }

            $pattern = '/(?=On\s\d{2}\s(?:January|February|March|April|May|June|July|August|September|October|November|December)\s\d{4},)|(?=Note:\s)/';

            $documents = preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

            $Text            = implode("\n\n\n", array_map('trim', $documents));
            $current_project = Project::where('id', auth()->user()->current_project_id)->first();
            $formattedText   = 'Given that the Conditions of Contract of this Construction Project is ';
            $formattedText .= '"' . $current_project->condation_contract . '",';
            $formattedText .= 'and the ' . $request->claimant . ' was delayed due to a Delay Event, which was beyond his control as explained in the following synopsis about the Delay Event: \n';
            $formattedText .= $Text . ' \n\n';
            $formattedText .= 'Please provide the contractual position of the ' . $request->claimant . ' supporting his entitlement to Extension of Time for Completion and the associated cost. \n';
            $formattedText .= 'Please provide your answer without introduction or padding';
        }
        $response = Http::withHeaders([
            'Content-Type'   => 'application/json',
            'X-goog-api-key' => config('openai.api_key_2'), // Replace with your actual Gemini API key
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent', [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $formattedText],
                    ],
                ],
            ],
        ]);

        $data     = $response->json();
        $result   = $data['candidates'][0]['content']['parts'][0]['text'];
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }

        $phpWord                   = new PhpWord;
        $section                   = $phpWord->addSection();
        $GetStandardStylesSubtitle = [
            'name'      => $formate_values ? $formate_values['subtitle']['standard']['name'] : 'Arial',
            'alignment' => $formate_values ? $formate_values['subtitle']['standard']['alignment'] : 'left', // Options: left, center, right, justify
            'size'      => $formate_values ? intval($formate_values['subtitle']['standard']['size']) : 14,
            'bold'      => $formate_values ? ($formate_values['subtitle']['standard']['bold'] == '1' ? true : false) : true,
            'italic'    => $formate_values ? ($formate_values['subtitle']['standard']['italic'] == '1' ? true : false) : false,
            'underline' => $formate_values ? ($formate_values['subtitle']['standard']['underline'] == '1' ? 'single' : 'none') : 'none',

        ];
        $GetParagraphStyleSubtitle = [
            'spaceBefore'       => $formate_values ? ((int) $formate_values['subtitle']['paragraph']['spaceBefore'] * 20) : 0,
            'spaceAfter'        => $formate_values ? ((int) $formate_values['subtitle']['paragraph']['spaceAfter'] * 20) : 240,
            'lineHeight'        => $formate_values ? (float) $formate_values['subtitle']['paragraph']['lineHeight'] : 1.5,
            'indentation'       => [
                'left'      => $formate_values ? ((float) $formate_values['subtitle']['paragraph']['indentation']['left'] * 1800) : 1000,
                'hanging'   => $formate_values ? ((float) $formate_values['subtitle']['paragraph']['indentation']['hanging'] * 1800) : 0,
                'firstLine' => 0,
            ],
            'contextualSpacing' => $formate_values ? ($formate_values['subtitle']['paragraph']['contextualSpacing'] == '1' ? true : false) : true,
            'keepNext'          => $formate_values ? ($formate_values['subtitle']['paragraph']['keepNext'] == '1' ? true : false) : true,
            'widowControl'      => $formate_values ? ($formate_values['subtitle']['paragraph']['widowControl'] == '1' ? true : false) : true,
            'pageBreakBefore'   => $formate_values ? ($formate_values['subtitle']['paragraph']['pageBreakBefore'] == '1' ? true : false) : false,

        ];
        $result = str_replace('&', '&amp;', $result);
        $section->addText($result, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
        $path          = public_path($projectFolder);
        if (! file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $code      = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

        if (! file_exists($directory)) {
            mkdir($directory, 0755, true); // true = create nested directories
        }
        // Save document
        // Define file path in public folder
        $fileName = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $file->code . '_' . $header . '.docx';
        $filePath = public_path($fileName);

        // Save document to public folder
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);
        session(['zip_file' => $code]);

        return response()->json(['download_url' => asset($fileName)]);
    }
}
