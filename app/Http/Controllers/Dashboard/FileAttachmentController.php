<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Document;
use App\Models\FileAttachment;
use App\Models\FileAttachmentFlag;
use App\Models\FileDocument;
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

        return view('project_dashboard.file_attachments.index', compact('Type_Name', 'type', 'array_red_flags', 'array_blue_flags', 'attachments', 'folders', 'file', 'specific_file_attach'));
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
        // Define styles for headings
        $GetStandardStylesH1 = [
            'name'      => 'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 24,
            'bold'      => true,
            'italic'    => false,
            'underline' => false,

        ];
        $GetParagraphStyleH1 = [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 803.6,
                'hanging'   => 803.6,
                'firstLine' => 0,
            ],
            'contextualSpacing' => true,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
        ];
        $GetStandardStylesH2 = [
            'name'      => 'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 16,
            'bold'      => true,
            'italic'    => false,
            'underline' => false,

        ];
        $GetParagraphStyleH2 = [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 1071.6,
                'hanging'   => 1071.6,
                'firstLine' => 0,
            ],
            'contextualSpacing' => true,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
        ];

        $GetStandardStylesH3 = [
            'name'      => 'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 14,
            'bold'      => false,
            'italic'    => false,
            'underline' => false,

        ];
        $GetParagraphStyleH3 = [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 1071.6,
                'hanging'   => 1071.6,
                'firstLine' => 0,
            ],
            'contextualSpacing' => true,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
        ];

        $GetStandardStylesSubtitle = [
            'name'      => 'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 14,
            'bold'      => true,
            'italic'    => false,
            'underline' => false,

        ];
        $GetParagraphStyleSubtitle = [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 1071.6,
                'hanging'   => 0,
                'firstLine' => 0,
            ],
            'contextualSpacing' => true,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
        ];
        $GetStandardStylesP = [
            'name'      => 'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 11,
            'bold'      => false,
            'italic'    => false,
            'underline' => false,

        ];

        $phpWord->addParagraphStyle('listParagraphStyle', [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 1071.6,
                'hanging'   => 1071.6,
                'firstLine' => 0,
            ],
            'contextualSpacing' => false,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
            'keepLines'         => true,
            'hyphenation'       => false,
            'pageBreakBefore'   => false,
        ]);

        $phpWord->addParagraphStyle('listParagraphStyle2', [
            'spaceBefore'       => 0,
            'spaceAfter'        => 10,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 1428.8,
                'hanging'   => 357.2,
                'firstLine' => 0,
            ],
            'contextualSpacing' => false,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
            'keepLines'         => true,
            'hyphenation'       => false,
            'pageBreakBefore'   => false,
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

                    // preg_match('/<ol>.*?<\/ol>/s', $paragraph_, $olMatches);
                    // $olContent = $olMatches[0] ?? ''; // Get the <ol> content if it exists
                    // preg_match('/<ul>.*?<\/ul>/s', $paragraph_, $ulMatches);
                    // $ulContent = $ulMatches[0] ?? ''; // Get the <ol> content if it exists

                    // Step 2: Remove the <ol> content from the main paragraph
                    // $paragraphWithoutOl = preg_replace('/<ol>.*?<\/ol>/s', '', $paragraph);
                    // $paragraphWithoutOlUl = preg_replace('/<ul>.*?<\/ul>/s', '', $paragraphWithoutOl);
                    $paragraphWithoutImagesAndBreaks = preg_replace('/<(br)[^>]*>/i', '', $paragraph_);

                    // Step 2: Remove empty <p></p> tags
                    $paragraphWithoutEmptyParagraphs = preg_replace('/<p>\s*<\/p>/i', '', $paragraphWithoutImagesAndBreaks);

                    $paragraphsArray = $this->splitHtmlToArray($paragraphWithoutEmptyParagraphs);

                    // Step 3: Split into an array of <p> tags
                    // $paragraphsArray = preg_split('/(?=<p>)|(?<=<\/p>)/', $paragraphWithoutEmptyParagraphs);

                    // Step 4: Filter out empty elements
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
                                        'spaceBefore' => 0,
                                        'spaceAfter'  => 240,
                                        'lineHeight'  => 0.9,
                                        'lineSpacing' => 'single',
                                        'indentation' => [
                                            'left' => 1071.6,
                                        ],
                                    ]);

                                    // Add Image
                                    $shape = $textRun->addImage($fullImagePath, [
                                        'width'     => 100,
                                        'height'    => 80,
                                        'alignment' => 'left',
                                    ]);

                                    // Add Caption (Alt text)
                                    if (! empty($altText)) {
                                        $textRun->addTextBreak(); // New line
                                        $textRun->addText($altText . '.', ['name' => 'Calibri',
                                            'alignment'                               => 'left', // Options: left, center, right, justify
                                            'size'                                    => 9,
                                            'bold'                                    => false,
                                            'italic'                                  => true,
                                            'underline'                               => false]); // Add caption in italics
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
                                        'alignment' => 'left',
                                    ]);

                                    // Add Caption (Alt text)
                                    if (! empty($altText)) {
                                        $listItemRun->addTextBreak(); // New line
                                        $listItemRun->addText($altText . '.', ['name' => 'Calibri',
                                            'alignment'                                   => 'left', // Options: left, center, right, justify
                                            'size'                                        => 9,
                                            'bold'                                        => false,
                                            'italic'                                      => true,
                                            'underline'                                   => false]); // Add caption in italics
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
                                        'spaceBefore' => 0,
                                        'spaceAfter'  => 240,
                                        'lineHeight'  => 1.5,
                                        'indentation' => [
                                            'left' => 1071.6,
                                        ],
                                    ]);

                                    // Add Image
                                    $textRun->addImage($fullImagePath, [
                                        'width'     => 100,
                                        'height'    => 80,
                                        'alignment' => 'left',
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
                                        'alignment' => 'left',
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
                                    Html::addHtml($unNestedListItemRun, $item, false, false);
                                }
                            }

                            $existedList = true;
                        } else {

                            // If the paragraph contains only text (including <span>, <strong>, etc.)
                            try {
                                if ($existedList) {

                                    $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                        'spaceBefore'       => 0,
                                        'spaceAfter'        => 240,
                                        'lineHeight'        => '1.5',
                                        'indentation'       => [
                                            'left' => 1071.6,

                                        ],
                                        'contextualSpacing' => false,
                                        'next'              => true,
                                        'keepNext'          => true,
                                        'widowControl'      => true,
                                        'keepLines'         => true,
                                        'hyphenation'       => false,
                                        'pageBreakBefore'   => false,
                                    ]);
                                    $pTag = $this->lowercaseFirstCharOnly($pTag);
                                    $pTag = str_replace('&', '&amp;', $pTag);
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
        // Define styles for headings
        $GetStandardStylesH1 = [
            'name'      => 'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 24,
            'bold'      => true,
            'italic'    => false,
            'underline' => false,

        ];
        $GetParagraphStyleH1 = [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 803.6,
                'hanging'   => 803.6,
                'firstLine' => 0,
            ],
            'contextualSpacing' => true,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
        ];
        $GetStandardStylesH2 = [
            'name'      => 'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 16,
            'bold'      => true,
            'italic'    => false,
            'underline' => false,

        ];
        $GetParagraphStyleH2 = [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 1071.6,
                'hanging'   => 1071.6,
                'firstLine' => 0,
            ],
            'contextualSpacing' => true,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
        ];

        $GetStandardStylesH3 = [
            'name'      => 'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 14,
            'bold'      => false,
            'italic'    => false,
            'underline' => false,

        ];
        $GetParagraphStyleH3 = [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 1071.6,
                'hanging'   => 1071.6,
                'firstLine' => 0,
            ],
            'contextualSpacing' => true,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
        ];

        $GetStandardStylesP = [
            'name'      => 'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 11,
            'bold'      => false,
            'italic'    => false,
            'underline' => false,

        ];

        $phpWord->addParagraphStyle('listParagraphStyle', [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 1071.6,
                'hanging'   => 1071.6,
                'firstLine' => 0,
            ],
            'contextualSpacing' => false,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
            'keepLines'         => true,
            'hyphenation'       => false,
            'pageBreakBefore'   => false,
        ]);

        $phpWord->addParagraphStyle('listParagraphStyle2', [
            'spaceBefore'       => 0,
            'spaceAfter'        => 10,
            'lineHeight'        => '1.5',
            'indentation'       => [
                'left'      => 1428.8,
                'hanging'   => 357.2,
                'firstLine' => 0,
            ],
            'contextualSpacing' => false,
            'next'              => true,
            'keepNext'          => true,
            'widowControl'      => true,
            'keepLines'         => true,
            'hyphenation'       => false,
            'pageBreakBefore'   => false,
        ]);

        $phpWord->addTitleStyle(1, $GetStandardStylesH1, $GetParagraphStyleH1);
        $phpWord->addTitleStyle(2, $GetStandardStylesH2, array_merge($GetParagraphStyleH2, ['numStyle' => 'multilevel', 'numLevel' => 1]));
        $phpWord->addTitleStyle(3, $GetStandardStylesH3, $GetParagraphStyleH3);

        $file = ProjectFile::where('slug', $request->fileSlug)->first();
        // Header (Level 1 Outline)
        $header = $file->name;
        $header = str_replace('&', '&amp;', $header);

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
            'name'      => 'Calibri',
            'alignment' => 'left', // Options: left, center, right, justify
            'size'      => 9,
            'bold'      => false,
            'italic'    => false,
            'underline' => false,

        ];
        $GetParagraphStyleFootNotes = [
            'spaceBefore' => 0,
            'spaceAfter'  => 0,
            'lineSpacing' => 240,
            'indentation' => [
                'left'      => 0,
                'hanging'   => 0,
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
                                    $item = '<span style="font-size:11pt;">' . $item . '</span>';
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
                                    $item = '<span style="font-size:11pt;">' . $item . '</span>';

                                    Html::addHtml($unNestedListItemRun, $item, false, false);
                                }
                            }

                            $existedList = true;
                        } else {

                            // If the paragraph contains only text (including <span>, <strong>, etc.)
                            try {
                                if ($existedList) {

                                    $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                        'spaceBefore'       => 0,
                                        'spaceAfter'        => 240,
                                        'lineHeight'        => '1.5',
                                        'indentation'       => [
                                            'left' => 1071.6,

                                        ],
                                        'contextualSpacing' => false,
                                        'next'              => true,
                                        'keepNext'          => true,
                                        'widowControl'      => true,
                                        'keepLines'         => true,
                                        'hyphenation'       => false,
                                        'pageBreakBefore'   => false,
                                    ]);
                                    $pTag = $this->lowercaseFirstCharOnly($pTag);
                                    $pTag = str_replace('&', '&amp;', $pTag);
                                    $pTag = '<span style="font-size:11pt;">' . $pTag . '</span>';
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
                                    $pTag = '<span style="font-size:11pt;">' . $pTag . '</span>';
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

// Match all sections starting with "On dd Month yyyy" or "Note:"
        $pattern = '/(?=On\s\d{2}\s(?:January|February|March|April|May|June|July|August|September|October|November|December)\s\d{4},)|(?=Note:\s)/';

// Split text into parts
        $documents = preg_split($pattern, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

// Rejoin with 3 line breaks between each part
        $formattedText = implode("\n\n\n", array_map('trim', $documents));

        $formattedText .= "\n\n\n\n\n\n";
        $formattedText .= 'Please follow the following rules in your response:
            1 - Provide your response in paragraphs. Do not use bullet points.
            2 - Make your response as short as possible.
            3 - Provide key dates in the format “D MMMM YYYY”.
            Based on the above rules please provide a synopsis about the Causes that delayed the ' . $request->claimant . ', and based on the following chronology of events:';
        $formattedText;
        // $chatGPT_APIkey = config('openai.api_key');
        // $ch             = curl_init('https://api.openai.com/v1/chat/completions');

        // $data = json_encode([
        //     'model'    => 'gpt-4o',
        //     'messages' => [

        //         ['role' => 'user', 'content' => $formattedText],
        //     ],
        // ]);

        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     'Content-Type: application/json',
        //     'Authorization: Bearer ' . $chatGPT_APIkey,
        // ]);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $response = curl_exec($ch);
        // curl_close($ch);
        // if ($response === false) {

        //     dd(curl_error($ch));
        // } else {
        //     dd($response);
        // }

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

        $data = $response->json();
        dd($data['candidates'][0]['content']['parts'][0]['text']);

    }
}
