<?php
namespace App\Http\Controllers\Dashboard;

use App\Exports\FileDocumentsExport;
use App\Http\Controllers\ApiController;
use App\Models\ContractTag;
use App\Models\DocType;
use App\Models\Document;
use App\Models\FileDocument;
use App\Models\FileDocumentFlags;
use App\Models\GanttChartDocData;
use App\Models\Note;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectFolder;
use App\Models\StorageFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use setasign\Fpdi\Fpdi;
use ZipArchive;

// /////////////////////////////////////////////////////////////////////////

class FileDocumentController extends ApiController
{
    public function index($id)
    {
        $path2 = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf');

        if (file_exists($path2)) {
            unlink($path2);
        }
        $ai_zip_file = session('ai_zip_file');
        if ($ai_zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $ai_zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('ai_zip_file');
        }
        session()->forget('path');
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $file      = ProjectFile::where('slug', $id)->first();
        $documents = FileDocument::with(['document', 'note'])
            ->where('file_id', $file->id)
            ->get()
            ->sortBy([
                fn($a, $b) => ($a->document->start_date ?? $a->note->start_date ?? '9999-12-31')
                <=> ($b->document->start_date ?? $b->note->start_date ?? '9999-12-31'),
                fn($a, $b) => $a->sn <=> $b->sn,
            ])
            ->values();
        $specific_file_doc = session('specific_file_doc');
        session()->forget('specific_file_doc');
        $folders = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive', 'Recycle Bin'])->pluck('name', 'id');
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users   = $project->assign_users;

        $documents_types = DocType::where('account_id', auth()->user()->current_account_id)->where('project_id', auth()->user()->current_project_id)->orderBy('order', 'asc')->get();
        $stake_holders   = $project->stakeHolders;

        $array_red_flags  = FileDocumentFlags::where('user_id', auth()->user()->id)->where('flag', 'red')->pluck('file_document_id')->toArray();
        $array_blue_flags = FileDocumentFlags::where('user_id', auth()->user()->id)->where('flag', 'blue')->pluck('file_document_id')->toArray();

        return view('project_dashboard.file_documents.index', compact('array_red_flags', 'array_blue_flags', 'documents', 'users', 'documents_types', 'stake_holders', 'folders', 'file', 'specific_file_doc'));
    }

    public function get_narrative(Request $request)
    {

        $doc = FileDocument::findOrFail($request->document_id);
        if ($doc->note_id == null) {
            $date = date('d F Y', strtotime($doc->document->start_date));
            $text = 'On ' . $date . ',';
        } else {
            $text = 'Note:';
        }
        $html = $doc->narrative;

        $doc = new \DOMDocument;
        @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new \DOMXPath($doc);
        $body  = $doc->getElementsByTagName('body')->item(0); // ✅ define body here

        $firstP = $xpath->query('//body/p')->item(0);
        if ($firstP && trim($firstP->textContent) !== '') {
            foreach ($firstP->childNodes as $child) {
                if ($child->nodeType === XML_TEXT_NODE) {
                    $textContent = $child->nodeValue;

                    if (isset($textContent[0]) && $textContent[0] === 'T') {
                        $child->nodeValue = 't' . substr($textContent, 1);
                    }

                    break; // Only check and update the first text node
                }
            }
            $fragment = $doc->createDocumentFragment();
            $fragment->appendXML('<strong>' . $text . '</strong> ');
            $firstP->insertBefore($fragment, $firstP->firstChild);
        } else {

            $p      = $doc->createElement('p');
            $strong = $doc->createElement('strong', $text);
            $p->appendChild($strong);
            $body->insertBefore($p, $body->firstChild); // ✅ insert new <p> at top of body
        }

        // Return the body content without <html><body> wrapper
        $newHtml = '';
        foreach ($body->childNodes as $node) {
            $newHtml .= $doc->saveHTML($node);
        }

        return response()->json([
            'html' => $newHtml,
        ]);
    }

    public function exportWordClaimDocs(Request $request)
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
        $paragraphs = FileDocument::with(['document', 'note'])
            ->where('file_id', $file->id);
        if ($request->forclaimdocs) {
            $paragraphs->where('forClaim', '1');
        }

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
        $x = intval($request->Start);
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
                $footnote         = $listItemRun->addFootnote($GetParagraphStyleFootNotes);
                $Exhibit          = true;
                $dated            = true;
                $senderAndDocType = true;
                $hint             = '';
                if ($request->formate_type2 == 'reference') {
                    $hint = $paragraph->document->reference . '.';
                } elseif ($request->formate_type2 == 'dateAndReference') {

                    $date2 = date('y_m_d', strtotime($paragraph->document->start_date));
                    $hint  = preg_replace('/_/', '', $date2) . ' - ' . $paragraph->document->reference . '.';
                } elseif ($request->formate_type2 == 'formate') {
                    $sn         = $request->sn2;
                    $prefix     = $request->prefix2;
                    $listNumber = "$prefix" . str_pad($x, $sn, '0', STR_PAD_LEFT);
                    $hint       = $listNumber . ': ';
                    $from       = $paragraph->document->fromStakeHolder ? $paragraph->document->fromStakeHolder->narrative . "'s " : '';
                    $type       = $paragraph->document->docType->name;
                    $hint .= $from . $type . ' ';
                    if (str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $paragraph->document->docType->name)), 'email') || str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $paragraph->document->docType->description)), 'email')) {
                        $ref_part = $request->ref_part2;
                        if ($ref_part == 'option1') {
                            $hint .= ', ';
                        } elseif ($ref_part == 'option2') {

                            $hint .= 'From: ' . $paragraph->document->reference . ', ';
                        } elseif ($ref_part == 'option3') {
                            $hint .= 'Ref: ' . $paragraph->document->reference . ', ';
                        }
                    } else {
                        $hint .= 'Ref: ' . $paragraph->document->reference . ', ';
                    }
                    $hint .= 'dated: ' . $date . '.';

                }
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

                        // Add a paragraph break after each element to separate them
                        // if ($index2 < count($paragraphsArray) - 1) {

                        //     if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {

                        //         $listItemRun->addTextBreak();
                        //     }

                        // }

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

    public function file_document_first_analyses($id)
    {
        $path2 = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf');

        if (file_exists($path2)) {
            unlink($path2);
        }
        session()->forget('path');
        $user = auth()->user();
        session(['specific_file_doc' => $id]);
        $doc  = FileDocument::findOrFail($id);
        $tags = ContractTag::where('account_id', $user->current_account_id)->where('project_id', $user->current_project_id)->orderBy('order', 'asc')->get();
        if ($doc->document) {
            session(['path' => $doc->document->storageFile->path]);
        }

        return view('project_dashboard.file_documents.doc_first_analyses', compact('doc', 'tags'));
    }

    public function upload_editor_image(Request $request)
    {

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:51200', // 10MB max
        ]);

        $file = $request->file('image');
        $name = $file->getClientOriginalName();
        $size = $file->getSize();
        $type = $file->getMimeType();

        $storageFile = StorageFile::where('user_id', auth()->user()->id)->where('project_id', auth()->user()->current_project_id)->where('file_name', $name)->where('size', $size)->where('file_type', $type)->first();
        if ($storageFile) {
            return response()->json([
                'success' => true,
                'file'    => $storageFile,
            ]);
        }
        $fileName = auth()->user()->id . '_' . time() . '_' . $file->getClientOriginalName();

        // Create project-specific folder in public path
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/images';
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
        ]);
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

    public function store_file_document_first_analyses(Request $request, $id)
    {
        session()->forget('path');
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

        $doc = FileDocument::findOrFail($id);
        $doc->update([
            'narrative' => $narrative,
            'notes1'    => $request->notes1,
            'notes2'    => $request->notes2,
            'sn'        => $request->sn,
            'forClaim'  => $request->forClaim ? '1' : '0',
            'forChart'  => $request->forChart ? '1' : '0',
            'forLetter' => $request->forLetter ? '1' : '0',
        ]);

        // Assign tags (assuming many-to-many relationship)
        if ($request->has('tags')) {
            $doc->tags()->sync($request->tags ?? []);
        } else {
            // Remove all old tags if no tags are sent
            $doc->tags()->sync([]);
        }
        if ($request->action == 'save') {
            return redirect('/project/file-document-first-analyses/' . $doc->id)->with('success', $doc->document ? 'analyses for "' . $doc->document->subject . '" document saved successfully.' : 'analyses for "' . $doc->note->subject . '" document saved successfully.');
        } else {

            $ai_zip_file = session('ai_zip_file');
            if ($ai_zip_file != null) {
                $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $ai_zip_file);
                if (File::exists($filePath)) {
                    File::deleteDirectory($filePath);
                }
                session()->forget('ai_zip_file');
            }
            return redirect('/project/file/' . $doc->file->slug . '/documents')->with('success', $doc->document ? 'analyses for "' . $doc->document->subject . '" document saved successfully.' : 'analyses for "' . $doc->note->subject . '" document saved successfully.');
        }

    }

    public function copy_move_doc_to_another_file(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }

        if ($request->actionType == 'copy') {
            foreach ($request->document_ids as $doc_id) {
                $sections = [];
                $file_doc = FileDocument::findOrFail($doc_id);
                if ($file_doc->document) {
                    $fileDoc = FileDocument::where('file_id', $request->file_id)->where('document_id', $file_doc->document_id)->first();
                } else {
                    $fileDoc = FileDocument::where('file_id', $request->file_id)->where('note_id', $file_doc->note_id)->first();
                }
                if (! $fileDoc) {
                    $fileDoc = FileDocument::create(['user_id' => auth()->user()->id,
                        'file_id'                                  => $request->file_id,
                        'narrative'                                => $file_doc->narrative,
                        'notes1'                                   => $file_doc->notes1,
                        'notes2'                                   => $file_doc->notes2,
                        'sn'                                       => $file_doc->sn,
                        'forClaim'                                 => $file_doc->forClaim,
                        'forChart'                                 => $file_doc->forChart,
                        'forLetter'                                => $file_doc->forLetter,
                        'document_id'                              => $file_doc->document_id,
                        'note_id'                                  => $file_doc->note_id]);
                    if ($fileDoc->document) {
                        $start_date = $fileDoc->document->start_date;
                        $end_date   = $fileDoc->document->end_date;
                    } else {
                        $start_date = $fileDoc->note->start_date;
                        $end_date   = $fileDoc->note->end_date;
                    }
                    $start_date  = $fileDoc->document->start_date;
                    $end_date    = $fileDoc->document->end_date;
                    $gantt_chart = GanttChartDocData::create(['file_document_id' => $fileDoc->id]);

                    $gantt_chart->lp_sd = $start_date;
                    $gantt_chart->lp_fd = $end_date;

                    $sections[] = [
                        'sd'    => $start_date,
                        'fd'    => $end_date,
                        'color' => '00008B',
                    ];

                    $gantt_chart->cur_sections = json_encode($sections);
                    if ($end_date == null) {
                        $gantt_chart->cur_type = 'M';
                    }
                    $gantt_chart->save();

                }

            }

            return response()->json([
                'status'  => 'success',
                'message' => count($request->document_ids) > 1 ? 'Selected Documents Copied To Selected File Successfully.' : 'Document Copied To Selected File Successfully.',

                // 'redirect' => url('/project/file/' . $file_doc->file->slug . '/documents')
            ]);
        } elseif ($request->actionType == 'move') {
            foreach ($request->document_ids as $doc_id) {
                $file_doc = FileDocument::findOrFail($doc_id);
                if ($file_doc->document) {
                    $fileDoc = FileDocument::where('file_id', $request->file_id)->where('document_id', $file_doc->document_id)->first();
                } else {
                    $fileDoc = FileDocument::where('file_id', $request->file_id)->where('note_id', $file_doc->note_id)->first();
                }
                if (! $fileDoc) {
                    $file_doc->file_id = $request->file_id;
                    $file_doc->save();

                }
            }

            return response()->json([
                'status'  => 'success',
                'message' => count($request->document_ids) > 1 ? 'Selected Documents Moved To Selected File Successfully.' : 'Document Moved To Selected File Successfully.',
                // 'redirect' => url('/project/file/' . $currentFile . '/documents')
            ]);
        }
    }

    public function unassign_doc(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }

        GanttChartDocData::whereIn('file_document_id', $request->document_ids)->delete();
        FileDocument::whereIn('id', $request->document_ids)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => count($request->document_ids) > 1 ? 'Selected documents is unassigned from file.' : 'Document is unassigned from file.',
            // 'redirect' => url('/project/file/' . $currentFile . '/documents')
        ]);
    }

    public function delete_doc_from_cmw_entirely(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        foreach ($request->document_ids as $doc_id) {
            $doc = FileDocument::findOrFail($doc_id);

            GanttChartDocData::where('file_document_id', $doc_id)->delete();
            if ($doc->note_id == null) {
                $document = Document::find($doc->document_id);
                FileDocument::where('document_id', $document->id)->delete();
                $docs = Document::where('storage_file_id', $document->storage_file_id)->where('id', '!=', $document->id)->get();
                if (count($docs) == 0) {
                    $path = public_path($document->storageFile->path);

                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                $document->delete();
            } else {
                $note = Note::find($doc->note_id);
                FileDocument::where('note_id', $note->id)->delete();
                $note->delete();
            }

        }

        return response()->json([
            'status'  => 'success',
            'message' => count($request->document_ids) > 1 ? 'Selected documents is deleted from CMW entirely.' : 'Document is deleted from CMW entirely.',
            // 'redirect' => url('/project/file/' . $currentFile . '/documents')
        ]);

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
            $do = FileDocument::findOrFail($request->document_ids[0]);
            $ac = $request->action_type;
            if ($do->$ac == '1') {
                $va = '0';
            } else {
                $va = '1';
            }
            FileDocument::whereIn('id', $request->document_ids)->update([$ac => $va]);

            return response()->json([
                'status' => 'success',
                'value'  => $va,
            ]);
        } else {
            FileDocument::whereIn('id', $request->document_ids)->update([$request->action_type => $request->val]);

            return response()->json([
                'status' => 'success',
            ]);
        }

    }

    public function download_documents(Request $request)
    {
        // dd($request->all());
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $file = ProjectFile::where('slug', $request->file_id_)->first();
        // $fileDocuments=FileDocument::where('file_id',$file->id);
        // if($request->forclaimdocs2){
        //     $fileDocuments->where('forClaim','1');
        // }

        // $fileDocuments=$fileDocuments->get();

        $fileDocuments = FileDocument::where('note_id', null)->with('document')
            ->where('file_id', $file->id);
        if ($request->forclaimdocs2) {
            $fileDocuments->where('forClaim', '1');
        }

        $fileDocuments = $fileDocuments->get()
            ->sortBy([
                fn($a, $b) => ($a->document->start_date ?? '9999-12-31') <=> ($b->document->start_date ?? '9999-12-31'),
                fn($a, $b) => $a->sn <=> $b->sn,
            ])
            ->values();
        if (count($fileDocuments) > 0) {
            $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp');

            if (! file_exists($directory)) {
                mkdir($directory, 0755, true); // true = create nested directories
            }
            $zip = new ZipArchive;

            $code      = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

            if (! file_exists($directory)) {
                mkdir($directory, 0755, true); // true = create nested directories
            }
            $zipFileName = $file->code . '-' . $file->name . '.zip';
            $zipFilePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/') . $zipFileName;
            if ($zip->open($zipFilePath, ZipArchive::CREATE) !== true) {

                return redirect()->back()->with('error', 'Could not create ZIP file');
            }
            $counter = intval($request->Start);
            foreach ($fileDocuments as $document) {
                $filePath = public_path($document->document->storageFile->path);
                if ($request->formate_type == 'reference') {
                    $sanitizedFilename = preg_replace('/[\\\\\/:;*?"+.<>|{}\[\]`]/', '-', $document->document->reference);
                    $sanitizedFilename = trim($sanitizedFilename, '-');
                    // $date = date('y_m_d', strtotime($document->document->start_date));
                    $fileName = $sanitizedFilename . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                } elseif ($request->formate_type == 'dateAndReference') {
                    $sanitizedFilename = preg_replace('/[\\\\\/:;*?"+.<>|{}\[\]`]/', '-', $document->document->reference);
                    $sanitizedFilename = trim($sanitizedFilename, '-');
                    $date              = date('y_m_d', strtotime($document->document->start_date));
                    $fileName          = preg_replace('/_/', '', $date) . ' - ' . $sanitizedFilename . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                } elseif ($request->formate_type == 'formate') {

                    $prefix = $request->prefix;
                    $sn     = $request->sn;

                    $date              = date('d-M-y', strtotime($document->document->start_date));
                    $from              = $document->document->fromStakeHolder ? $document->document->fromStakeHolder->narrative . "'s " : '';
                    $type              = $document->document->docType->name;
                    $sanitizedFilename = preg_replace('/[\\\\\/:;*?"+.<>|{}\[\]`]/', '-', $document->document->reference);
                    $sanitizedFilename = trim($sanitizedFilename, '-');
                    $number_prefix     = str_pad($counter, $sn, '0', STR_PAD_LEFT);
                    $fileName          = $prefix . $number_prefix . ' - ' . $from . $type . ' ';
                    if (str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $document->document->docType->name)), 'email') || str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $document->document->docType->description)), 'email')) {
                        $ref_part = $request->ref_part;
                        if ($ref_part == 'option1') {
                            $fileName .= '- ';
                        } elseif ($ref_part == 'option2') {

                            $fileName .= 'From- ' . $sanitizedFilename . ' - ';
                        } elseif ($ref_part == 'option3') {
                            $fileName .= 'Ref- ' . $sanitizedFilename . ' - ';
                        }
                    } else {
                        $fileName .= 'Ref- ' . $sanitizedFilename . ' - ';
                    }
                    $fileName .= 'dated ' . $date . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                    $counter++;
                }

                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $fileName);
                }
            }

            $zip->close();

            // Return the zip file as a download
            if (file_exists($zipFilePath)) {
                session(['zip_file' => $code]);
                $relativePath = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $zipFileName;

                return response()->json(['download_url' => asset($relativePath)]);
                // return response()->download($zipFilePath,null, [
                //     'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                //     'Pragma' => 'no-cache',
                //     'Expires' => '0',
                // ]);
            }
        }

        return redirect()->back()->with('error', 'No files found to download.');

    }

    public function download_excel_specific_documents(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $file          = ProjectFile::where('slug', $request->file_id)->first();
        $fileDocuments = FileDocument::with(['document', 'note'])
            ->whereIn('id', $request->document_ids)
            ->whereNull('note_id')
            ->get()
            ->sortBy([
                fn($a, $b) => ($a->document->start_date ?? $a->note->start_date ?? '9999-12-31')
                <=> ($b->document->start_date ?? $b->note->start_date ?? '9999-12-31'),
                fn($a, $b) => $a->sn <=> $b->sn,
            ])
            ->values();
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
        $fileName     = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $file->code . '-' . $file->name . '-documents_' . time() . '.xlsx';
        $filePath     = public_path($fileName);
        $excelContent = Excel::raw(new FileDocumentsExport($fileDocuments), \Maatwebsite\Excel\Excel::XLSX);

        file_put_contents($filePath, $excelContent);
        session(['zip_file' => $code]);
        return response()->json(['message' => 'Selected Documents Exported successfully', 'download_url' => asset($fileName)]);
    }

    public function download_specific_documents(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $file          = ProjectFile::where('slug', $request->file_id)->first();
        $fileDocuments = FileDocument::whereIn('id', $request->document_ids)->get();
        if (count($fileDocuments) > 0) {
            $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp');

            if (! file_exists($directory)) {
                mkdir($directory, 0755, true); // true = create nested directories
            }
            $zip = new ZipArchive;

            $code      = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

            if (! file_exists($directory)) {
                mkdir($directory, 0755, true); // true = create nested directories
            }
            $zipFileName = $file->code . '-' . $file->name . ' - selected documents ' . '.zip';
            $zipFilePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/') . $zipFileName;
            if ($zip->open($zipFilePath, ZipArchive::CREATE) !== true) {

                return redirect()->back()->with('error', 'Could not create ZIP file');
            }

            foreach ($fileDocuments as $document) {
                $filePath = public_path($document->document->storageFile->path);
                if (str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $document->document->docType->name)), 'email') || str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $document->document->docType->description)), 'email')) {
                    $sanitizedFilename = $document->document->fromStakeHolder->narrative . "'s e-mail dated ";
                    $date              = date('y_m_d', strtotime($document->document->start_date));
                    $date2             = date('d-M-y', strtotime($document->document->start_date));
                    $fileName          = preg_replace('/_/', '', $date) . ' - ' . $sanitizedFilename . $date2 . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                } else {
                    $sanitizedFilename = preg_replace('/[\\\\\/:*?"+.<>|{}\[\]`]/', '-', $document->document->reference);
                    $sanitizedFilename = trim($sanitizedFilename, '-');
                    $date              = date('y_m_d', strtotime($document->document->start_date));
                    $fileName          = preg_replace('/_/', '', $date) . ' - ' . $sanitizedFilename . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                }

                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $fileName);
                }
            }

            $zip->close();

            // Return the zip file as a download
            if (file_exists($zipFilePath)) {
                session(['zip_file' => $code]);
                $relativePath = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $zipFileName;

                return response()->json(['message' => 'Selected Documents Downloaded successfully', 'download_url' => asset($relativePath)]);
                // return response()->download($zipFilePath,null, [
                //     'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                //     'Pragma' => 'no-cache',
                //     'Expires' => '0',
                // ]);
            }

        }

        return redirect()->back()->with('error', 'No files found to download.');

    }

    public function edit_docs_info(Request $request)
    {
        foreach ($request->document_ids as $id) {
            $file_doc = FileDocument::findOrFail($id);
            $doc      = $file_doc->document;
            if ($request->doc_type) {
                $doc->doc_type_id = $request->doc_type;
            }
            if ($request->from) {
                $doc->from_id = $request->from;
            }
            if ($request->to) {
                $doc->to_id = $request->to;
            }
            if ($request->owner) {
                $doc->user_id = $request->owner;
            }
            $doc->save();
        }

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function change_flag(Request $request)
    {
        $docId = $request->docId;
        $type  = $request->type;

        $record = FileDocumentFlags::where('user_id', auth()->user()->id)->where('file_document_id', $docId)->where('flag', $type)->first();
        if ($record) {
            $record->delete();

            return response()->json([
                'success' => false,
            ]);
        } else {
            FileDocumentFlags::create(['user_id' => auth()->user()->id, 'file_document_id' => $docId, 'flag' => $type]);

            return response()->json([
                'success' => true,
            ]);
        }
    }

    public function create_note(Request $request)
    {

        do {
            $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (Note::where('slug', $slug)->exists());

        $file = ProjectFile::where('slug', $request->file_slug)->first();
        $note = Note::create(['slug' => $slug, 'user_id' => auth()->user()->id,
            'project_id'                 => auth()->user()->current_project_id,
            'start_date'                 => $request->start_date,
            'subject'                    => $request->subject]);
        $fileDoc     = FileDocument::create(['file_id' => $file->id, 'note_id' => $note->id, 'user_id' => auth()->user()->id, 'forClaim' => '1', 'forChart' => '1']);
        $start_date  = $fileDoc->note->start_date;
        $end_date    = $fileDoc->note->end_date;
        $gantt_chart = GanttChartDocData::create(['file_document_id' => $fileDoc->id]);

        $gantt_chart->lp_sd = $start_date;
        $gantt_chart->lp_fd = $end_date;

        $sections[] = [
            'sd'    => $start_date,
            'fd'    => $end_date,
            'color' => '00008B',
        ];

        $gantt_chart->cur_sections = json_encode($sections);
        if ($end_date == null) {
            $gantt_chart->cur_type = 'M';
        }
        $gantt_chart->save();
        session(['specific_file_doc' => $fileDoc->id]);

        return response()->json([
            'success' => true,
        ]);
    }
/////////////////////////////////////////////////////////////
    public function create_ai_pdf(Request $request)
    {
        $path2 = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf');
        $from  = $request->from - 1;
        $to    = $request->to - 1;
        if (file_exists($path2)) {
            unlink($path2);
        }
        $path       = session('path');
        $sourcePath = public_path($path);
        session()->forget('ai_zip_file');

        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
        $path          = public_path($projectFolder);
        if (! file_exists($path)) {

            mkdir($path, 0755, true);
        }
        $imagick = new \Imagick();
        $imagick->setResolution(300, 300); // زيادة الدقة
        if ($request->to == 1) {
            $imagick->readImage($sourcePath);

        } else {
            $imagick->readImage($sourcePath . '[' . $from . '-' . $to . ']');

        }
        $directoryeee = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id);

        if (! file_exists($directoryeee)) {
            mkdir($directoryeee, 0755, true); // true = create nested directories
        }
        $imagick->setImageFormat('pdf');
        $imagick->setImageCompressionQuality(100);
        $imagick->writeImages(public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf'), true);

        $sourcePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf');
        $code       = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        $directory  = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

        if (! file_exists($directory)) {
            mkdir($directory, 0755, true); // true = create nested directories
        }
        $targetPath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/extracted.pdf');
        $pdf        = new Fpdi;
        $pageCount  = $pdf->setSourceFile($sourcePath);

        // استخراج الصفحات من 3 إلى 10
        for ($i = intval($request->from); $i <= intval($request->to) && $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size       = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
        }

        // حفظ الملف في temp
        $pdf->Output('F', $targetPath);
        session(['ai_zip_file' => $code]);
        session(['ai_pdf_path' => 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/extracted.pdf']);
        $path2 = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . auth()->user()->id . '/' . 'cleaned_gyjt__test_11.pdf');

        if (file_exists($path2)) {
            unlink($path2);
        }

        return response()->json([
            'message'     => 'success',
            'ai_zip_file' => $code,
        ]);
        // Save document
    }

    public function ai_layer($id)
    {
        $ai_zip_file        = session('ai_zip_file');
        $ai_pdf_path        = session('ai_pdf_path');
        $file_doc_id        = session('specific_file_doc');
        $file_doc           = FileDocument::findOrFail($file_doc_id);
        $file_doc->ai_layer = '1';
        $file_doc->save();
        //session()->forget('ai_pdf_path');
        return view('project_dashboard.file_documents.ai_layer', compact('ai_zip_file', 'ai_pdf_path', 'file_doc_id'));
    }

    public function summarize_pdf(Request $request)
    {
        //dd($request->all());
        $file_doc = FileDocument::findOrFail($request->file_doc_id);
        $document = $file_doc->document;
        $message  = "This was a " . $document->docType->description . " " . $document->docType->relevant_word;
        if ($document->fromStakeHolder) {
            $message .= " from " . $document->fromStakeHolder->article . " " . $document->fromStakeHolder->narrative;
        }
        if ($document->toStakeHolder) {
            $message .= " to " . $document->toStakeHolder->article . " " . $document->toStakeHolder->narrative;
        }
        $message .= ". Please summarize and rephrase it in the past tense";
        if ($request->support == 'sender' && $document->fromStakeHolder) {
            $message .= " in a way supporting " . $document->fromStakeHolder->article . " " . $document->fromStakeHolder->narrative;
        } elseif ($request->support == 'receiver' && $document->toStakeHolder) {
            $message .= " in a way supporting " . $document->toStakeHolder->article . " " . $document->toStakeHolder->narrative;
        }
        if ($document->fromStakeHolder) {
            $message .= ". please start the paragraph  with " . $document->fromStakeHolder->article . " " . $document->fromStakeHolder->narrative . " " . $document->docType->relevant_word . " a " . $document->docType->description;
        }
        $message .= ". No need to mention the project name or to repeat the letter subject.";
        if ($request->focus == 'none') {
            $message .= "";
        } elseif ($request->focus == 'note 1' && $file_doc->notes1 != null) {
            $note1 = str_replace('"', '\"', $file_doc->notes1);
            $message .= " Please focus on the following: \"" . $note1 . "\".";
        } elseif ($request->focus == 'note 2' && $file_doc->notes2 != null) {
            $note2 = str_replace('"', '\"', $file_doc->notes2);
            $message .= " Please focus on the following: \"" . $note2 . "\".";
        } elseif ($request->focus == 'document note' && $document->notes != null) {
            $documentNote = str_replace('"', '\"', $document->notes);
            $message .= " Please focus on the following: \"" . $documentNote . "\".";
        } elseif ($request->focus == 'narrative' && $file_doc->narrative != null) {
            $narrative = $this->hasContent($request->narrative);
            $narrative = str_replace('"', '\"', $narrative);
            $message .= " Please focus on the following: \"" . $narrative . "\".";
        } else {
            $focus = str_replace('"', '\"', $request->focus);
            $message .= " Please focus on the following: \"" . $focus . "\".";
        }
        $apiKey = 'sec_rKlDJdNkUf5wBSQmAqPOlzdmssUuUWJW'; // Replace with your actual API key
        if ($request->source_id == null) {
            // src_Hd6khdL0UretnbaqUsUPQ
            //$url = 'https://ccmw.app/projects/7/documents/1748288269_1933-Request-for-Release-of-DEWA-Water-Submeters-Ref-1924.pdf';
            $url = url($request->ai_pdf_path);
            //dd($url);
            $payload = json_encode([
                'url' => $url,
            ]);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => 'https://api.chatpdf.com/v1/sources/add-url',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => $payload,
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                // handle error
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("cURL Error: $error");
            }

            curl_close($ch);

            $data = json_decode($response, true);

            // Access sourceId from response
            $sourceId = $data['sourceId'] ?? null;
            $payload  = json_encode([
                'sourceId' => $sourceId,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => $message,
                    ],
                ],
            ]);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => 'https://api.chatpdf.com/v1/chats/message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => $payload,
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("cURL Error: $error");
            }

            curl_close($ch);

            $data = json_decode($response, true);

            // Get the response content
            $answer = $data['content'] ?? 'No answer found';

            $ai_zip_file = $request->ai_zip_file;
            if ($ai_zip_file != null) {
                $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $ai_zip_file);
                if (File::exists($filePath)) {
                    File::deleteDirectory($filePath);
                }
            }
        } else {
            $sourceId = $request->source_id;
            $payload  = json_encode([
                'sourceId' => $sourceId,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => $message,
                    ],
                ],
            ]);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => 'https://api.chatpdf.com/v1/chats/message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => $payload,
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("cURL Error: $error");
            }

            curl_close($ch);

            $data = json_decode($response, true);

            // Get the response content
            $answer = $data['content'] ?? 'No answer found';
        }
        return response()->json([
            'message'  => 'success',
            'answer'   => $answer,
            'sourceId' => $sourceId,
        ]);
    }

    public function close_summarize_pdf(Request $request)
    {
        $ai_zip_file = $request->ai_zip_file;
        if ($ai_zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $ai_zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('ai_zip_file');
        }
        session()->forget('ai_pdf_path');
        if ($request->source_id != null) {
            $apiKey   = 'sec_rKlDJdNkUf5wBSQmAqPOlzdmssUuUWJW'; // Your API key
            $sourceId = $request->source_id;                    // The source ID you want to delete

            $payload = json_encode([
                'sources' => [$sourceId],
            ]);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => 'https://api.chatpdf.com/v1/sources/delete',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => $payload,
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("cURL Error: $error");
            }

            curl_close($ch);

            $data = json_decode($response, true);
        }

        $file_doc           = FileDocument::findOrFail($request->file_doc_id);
        $file_doc->ai_layer = '0';
        $file_doc->save();
        return response()->json([
            'message' => 'success',
        ]);

    }

    public function cleanupAI(Request $request)
    {
        $sourceId  = $request->source_id;
        $aiZipFile = $request->ai_zip_file;
        if ($aiZipFile != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $aiZipFile);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('ai_zip_file');
        }
        session()->forget('ai_pdf_path');

        // مثلاً:
        if ($sourceId != null) {
            $apiKey = 'sec_rKlDJdNkUf5wBSQmAqPOlzdmssUuUWJW'; // Your API key

            $payload = json_encode([
                'sources' => [$sourceId],
            ]);

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => 'https://api.chatpdf.com/v1/sources/delete',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => $payload,
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("cURL Error: $error");
            }

            curl_close($ch);

            $data = json_decode($response, true);
        }
        $file_doc           = FileDocument::findOrFail($request->fileDoc_id);
        $file_doc->ai_layer = '0';
        $file_doc->save();
        return response()->json([
            'message' => 'success',
        ]);
        return response()->json(['status' => 'cleaned']);
    }

    public function checkDoc_aiLayerUsed(Request $request)
    {
        $file_doc = FileDocument::findOrFail($request->id);
        $result   = $file_doc->ai_layer;
        return response()->json([
            'message' => 'success',
            'result'  => $result,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////////

    public function gantt_chart($id)
    {
        $file_doc    = FileDocument::findOrFail($id);
        $gantt_chart = GanttChartDocData::where('file_document_id', $id)->first();
        if ($file_doc->document) {
            $start_date = $file_doc->document->start_date;
            $end_date   = $file_doc->document->end_date;
            $type       = 'd';
        } else {
            $start_date = $file_doc->note->start_date;
            $end_date   = $file_doc->note->end_date;
            $type       = 'n';
        }
        if ($gantt_chart) {
            if ($gantt_chart->cur_sections != null) {
                $gantt_chart->cur_sections = json_decode($gantt_chart->cur_sections, true);
            }

            $html = view('project_dashboard.file_documents.edit_gantt_chart', compact('id', 'gantt_chart', 'start_date', 'end_date', 'type'))->render();
        } else {
            $html = view('project_dashboard.file_documents.create_gantt_chart', compact('id', 'start_date', 'end_date', 'type'))->render();
        }
        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    public function save_gantt_chart_data(Request $request)
    {
        //dd($request->all());
        $file_doc = FileDocument::findOrFail($request->document_id);
        if ($file_doc->document) {
            $start_date = $file_doc->document->start_date;
            $end_date   = $file_doc->document->end_date;
        } else {
            $start_date = $file_doc->note->start_date;
            $end_date   = $file_doc->note->end_date;
        }
        $gantt_chart = GanttChartDocData::where('file_document_id', $request->document_id)->first();
        if (! $gantt_chart) {
            $gantt_chart = GanttChartDocData::create(['file_document_id' => $request->document_id]);
        }
        $sections = [];
        if ($end_date == null) {
            $gantt_chart->lp_sd = $start_date;
            $gantt_chart->lp_fd = null;
        } else {
            $gantt_chart->lp_sd = $request->lp_sd;
            $gantt_chart->lp_fd = $request->lp_fd;
        }

        if ($request->show_lp == 'on') {
            $gantt_chart->show_lp = '1';
        } else {
            $gantt_chart->show_lp = '0';
        }
        if ($request->show_pl == 'on') {
            $gantt_chart->show_pl = '1';
        } else {
            $gantt_chart->show_pl = '0';
        }
        if ($request->show_cur == 'on') {
            $gantt_chart->show_cur = '1';
        } else {
            $gantt_chart->show_cur = '0';
        }
        if ($request->cur_show_sd == 'on') {
            $gantt_chart->cur_show_sd = '1';
        } else {
            $gantt_chart->cur_show_sd = '0';
        }
        if ($request->cur_show_fd == 'on') {
            $gantt_chart->cur_show_fd = '1';
        } else {
            $gantt_chart->cur_show_fd = '0';
        }
        if ($request->pl_show_sd == 'on') {
            $gantt_chart->pl_show_sd = '1';
        } else {
            $gantt_chart->pl_show_sd = '0';
        }
        if ($request->pl_show_fd == 'on') {
            $gantt_chart->pl_show_fd = '1';
        } else {
            $gantt_chart->pl_show_fd = '0';
        }
        $gantt_chart->pl_color          = $request->pl_color;
        $gantt_chart->cur_type          = $request->cur_type;
        $gantt_chart->pl_left_caption   = $request->pl_left_caption;
        $gantt_chart->pl_right_caption  = $request->pl_right_caption;
        $gantt_chart->cur_left_caption  = $request->cur_left_caption;
        $gantt_chart->cur_right_caption = $request->cur_right_caption;
        $gantt_chart->cur_show_ref      = $request->cur_show_ref;
        $gantt_chart->pl_sd             = $request->pl_sd;
        $gantt_chart->pl_fd             = $request->pl_fd;
        if ($request->pl_type != null) {
            $gantt_chart->pl_type = $request->pl_type;
        }
        if ($request->cur_type == 'SB' || $request->cur_type == 'DA' || $request->cur_type == 'M' || $request->cur_type == 'S') {
            $sections[] = [
                'sd'    => $start_date,
                'fd'    => $end_date,
                'color' => $request->cu_color,
            ];

            $gantt_chart->cur_sections = json_encode($sections);
        } elseif ($request->cur_type == 'MS') {
            $gantt_chart->cur_sections = $request->sections;
        }
        $gantt_chart->save();
        return response()->json([
            'success' => true,

        ]);
    }
}
