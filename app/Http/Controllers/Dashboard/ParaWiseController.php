<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use App\Models\Document;
use App\Models\Paragraph;
use App\Models\ParaWise;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

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

        return view('project_dashboard.para_wise_analysis.edit_paragraph', compact('next', 'previous', 'paragraph', 'para_wise', 'docs', 'paragraphs'));
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
            $array = explode(",", $paragraph->para_numbers);
            Paragraph::whereIn('id', $array)->where('reply', null)
                ->update(['replyed' => "0", 'reply_user_id' => null]);
        }
        if ($request->para_numbers) {
            if ($reply) {
                Paragraph::whereIn('id', $request->para_numbers)
                    ->update(['replyed' => "1", 'reply_user_id' => auth()->user()->id]);
                $para_numbers = implode(",", $request->para_numbers);
            } else {
                $para_numbers = null;
            }

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

        } else {
            $paragraph->replyed       = "0";
            $paragraph->reply_user_id = null;
        }
        $paragraph->save();

        if ($request->action == 'save') {
            return redirect('/project/para-wise-analysis/paragraphs/' . $slug . '/edit')->with('success', 'Paragraph Updated successfully.');
        } else {
            return redirect('/project/para-wise-analysis/paragraphs/' . $paragraph->para_wise->slug)
                ->with('success', 'Paragraph Updated successfully.');
        }
    }

    public function exportWordParaWise(Request $request)
    {
        $zip_file = session('zip_file');
        if ($zip_file != null) {
            $filePath = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $zip_file);
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $phpWord       = new PhpWord;
        $section       = $phpWord->addSection();
        $chapter       = $request->Chapter;
        $sectionNumber = $request->Section;
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
            'bold'      => true,
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
            'size'      => 12,
            'bold'      => true,
            'italic'    => true,
            'underline' => false,

        ];
        $GetParagraphStyleSubtitle = [
            'spaceBefore'       => 0,
            'spaceAfter'        => 240,
            'lineHeight'        => '1',
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
            'lineHeight'        => '1.25',
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
        $phpWord->addTitleStyle(2, $GetStandardStylesH2, array_merge($GetParagraphStyleH2, ['numStyle' => 'multilevel', 'numLevel' => 0]));
        $phpWord->addTitleStyle(3, $GetStandardStylesH3, array_merge($GetParagraphStyleH3, ['numStyle' => 'multilevel', 'numLevel' => 1]));
        $header = $request->header1;
        $header = str_replace('&', '&amp;', $header);
        $section->addTitle($header, 2);

        $header2 = $request->header2;
        $header2 = str_replace('&', '&amp;', $header2);
        $section->addTitle($header2, 3);

        $para_wise  = ParaWise::where('slug', $request->paraWise_ID)->first();
        $x          = intval($request->Start);
        $paragraphs = Paragraph::where('para_wise_id', $para_wise->id)->get();
        foreach ($paragraphs as $index => $paragraph) {

            if ($request->style == 'r_s') {
                if ($paragraph->reply != null) {
                    if ($paragraph->title_above) {
                        $subtitle = $paragraph->title_above;
                        $subtitle = str_replace('&', '&amp;', $subtitle);

                        $textRun   = $section->addTextRun($GetParagraphStyleSubtitle);
                        $lines     = explode("\n", trim($subtitle));
                        $lastIndex = count($lines) - 1;

                        foreach ($lines as $index => $line) {
                            $textRun->addText($line, $GetStandardStylesSubtitle);
                            if ($index !== $lastIndex) {
                                $textRun->addTextBreak();
                            }
                        }
                    }
                    $containsHtml = strip_tags($paragraph->reply) !== $paragraph->reply;
                    if (! $containsHtml) {
                        $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');
                        $listItemRun->addText($paragraph->reply . '.');
                    } else {
                        $paragraph_                      = $this->fixParagraphsWithImages($paragraph->reply);
                        $paragraphWithoutImagesAndBreaks = preg_replace('/<(br)[^>]*>/i', '', $paragraph_);

                        // Step 2: Remove empty <p></p> tags
                        $paragraphWithoutEmptyParagraphs = preg_replace('/<p>\s*<\/p>/i', '', $paragraphWithoutImagesAndBreaks);

                        $paragraphsArray = $this->splitHtmlToArray($paragraphWithoutEmptyParagraphs);
                        $paragraphsArray = array_filter($paragraphsArray, function ($item) {
                            return ! empty(trim($item));
                        });
                        //dd($paragraphsArray);
                        foreach ($paragraphsArray as $index2 => $pTag) {
                            // dd($paragraphsArray);
                            if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath       = $matches[1];                                 // Extract image path
                                $altText       = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                                $fullImagePath = public_path($imgPath);                       // Convert relative path to absolute

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

                                    
                                }

                            } elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {

                                $imgPath = $matches[1]; // Extract image path

                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute

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
                                    foreach ($listItems as $item) {                                                                             // Add a nested list item
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1' . $index . $index2, 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                                    // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:11pt;">' . $item . '</span>';
                                        if (preg_match_all('/<strong[^>]*>\s*\[?\*{3}(.*?)\*{3}\]?\s*<\/strong>/', $item, $matches)) {
                                           
                                            $this->addParagraphWithInlineFootnotes($nestedListItemRun, $item, $request, $x);
                                            $count = preg_match_all(
                                                '/<strong[^>]*>\s*\[?\*{3}(.*?)\*{3}\]?\s*<\/strong>/',
                                                $item,
                                                $matches
                                            );
                                           
                                            $x += $count;
                                            
                                        } else {
                                            Html::addHtml($nestedListItemRun, $item, false, false);

                                        }
                                    }
                                }

                            } elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];

                                                                                                                            // Add each list item as a nested list item
                                    foreach ($listItems as $item) {                                                         // Add a nested list item                                                                                            // dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered', 'listParagraphStyle2'); // Use a numbering style
                                                                                                                                // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        $item = '<span style="font-size:11pt;">' . $item . '</span>';
                                        if (preg_match_all('/<strong[^>]*>\s*\[?\*{3}(.*?)\*{3}\]?\s*<\/strong>/', $item, $matches)) {
                                            $this->addParagraphWithInlineFootnotes($unNestedListItemRun, $item, $request, $x);
                                            $count = preg_match_all(
                                                '/<strong[^>]*>\s*\[?\*{3}(.*?)\*{3}\]?\s*<\/strong>/',
                                                $item,
                                                $matches
                                            );
                                            $x += $count;
                                        } else {
                                            Html::addHtml($unNestedListItemRun, $item, false, false);

                                        }
                                    }
                                }

                            } else {
                                if (preg_match_all('/<strong[^>]*>\s*\[?\*{3}(.*?)\*{3}\]?\s*<\/strong>/', $pTag, $matches)) {
                                    $this->addParagraphWithInlineFootnotes($section, $pTag, $request, $x);
                                    $count = preg_match_all(
                                        '/<strong[^>]*>\s*\[?\*{3}(.*?)\*{3}\]?\s*<\/strong>/',
                                        $pTag,
                                        $matches
                                    );
                                    $x += $count;
                                } else {
                                    try {
                                        $listItemRun = $section->addListItemRun(2, 'multilevel', 'listParagraphStyle');

                                        // $pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag = $this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        $pTag = '<span style="font-size:11pt;">' . $pTag . '</span>';
                                        Html::addHtml($listItemRun, $pTag, false, false);

                                        // if ($index2 < count($paragraphsArray) - 1) {

                                        //     if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2 + 1], '<ol>') === false && stripos($paragraphsArray[$index2 + 1], '<ul>') === false) {

                                        //         $listItemRun->addTextBreak();
                                        //     }

                                        // }

                                    } catch (\Exception $e) {
                                        error_log('Error adding HTML: ' . $e->getMessage());
                                    }
                                }
                                // If the paragraph contains only text (including <span>, <strong>, etc.)

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
        $fileName = 'projects/' . auth()->user()->current_project_id . '/temp/' . $code . '/' . $para_wise->slug . '_' . $header . '.docx';
        $filePath = public_path($fileName);

        // Save document to public folder
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);
        session(['zip_file' => $code]);

        return response()->json(['download_url' => asset($fileName)]);
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

    public function addParagraphWithInlineFootnotes($container, $htmlLine, Request $request, $x, $listLevel = 2, $numberingStyle = 'multilevel', $paragraphStyle = 'listParagraphStyle')
    {
        $y=$x;
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

        // helper: close unclosed occurrences of common tags (best-effort)
        $closeTags = function (string $html) {
            $tags = ['p', 'span', 'strong', 'em', 'b', 'i', 'u', 'ul', 'ol', 'li', 'div'];

            foreach ($tags as $tag) {
                preg_match_all("/<{$tag}\b[^>]*>/i", $html, $openMatches);
                preg_match_all("/<\/{$tag}>/i", $html, $closeMatches);

                $openCount  = count($openMatches[0]);
                $closeCount = count($closeMatches[0]);

                // لو فيه closing أكتر من opening → نضيف opening في البداية
                if ($closeCount > $openCount) {
                    $html = str_repeat("<{$tag}>", $closeCount - $openCount) . $html;
                }

                // لو فيه opening أكتر من closing → نضيف closing في الآخر
                if ($openCount > $closeCount) {
                    $html .= str_repeat("</{$tag}>", $openCount - $closeCount);
                }
            }

            return $html;
        };

        // 1) ensure well-formedness
        $htmlLine = $closeTags($htmlLine);

        // 2) split around <strong ...>...</strong> blocks (capture them)
        $parts = preg_split('/(<strong\b[^>]*>.*?<\/strong>)/is', $htmlLine, -1, PREG_SPLIT_DELIM_CAPTURE);

        // 3) decide container type
        if ($container instanceof \PhpOffice\PhpWord\Element\Section) {
            // لو section → أنشئ listItemRun
            $listItemRun = $container->addListItemRun($listLevel, $numberingStyle, $paragraphStyle);
        } else {
            // لو container جاهز (listItemRun) → استخدمه مباشرة
            $listItemRun = $container;
        }

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            // If this part is a <strong>...</strong> that contains a reference between ***...***
            if (preg_match('/^<strong\b[^>]*>\s*\[?\*{3}(.*?)\*{3}\]?\s*<\/strong>$/is', $part, $m)) {
                $refText  = trim($m[1]);
                $document = Document::where('project_id', auth()->user()->current_project_id)->where('reference', $refText)->first();
                if(!$document){
                    dd($refText);
                }
                $date = date('d F Y', strtotime($document->start_date));
                // Add the footnote right here (inline)
                try {
                    $footnote         = $listItemRun->addFootnote($GetParagraphStyleFootNotes);
                     $hint             = '';
                if ($request->formate_type2 == 'reference') {
                    $hint = $document->reference . '.';
                } elseif ($request->formate_type2 == 'dateAndReference') {

                    $date2 = date('y_m_d', strtotime($document->start_date));
                    $hint  = preg_replace('/_/', '', $date2) . ' - ' . $document->reference . '.';
                } elseif ($request->formate_type2 == 'formate') {
                    $sn         = $request->sn2;
                    $prefix     = $request->prefix2;
                    $listNumber = "$prefix" . str_pad($y, $sn, '0', STR_PAD_LEFT);
                    $hint       = $listNumber . ': ';
                    $from       = $document->fromStakeHolder ? $document->fromStakeHolder->narrative . "'s " : '';
                    $type       = $document->docType->name;
                    $hint .= $from . $type . ' ';
                    if (str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $document->docType->name)), 'email') || str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $document->docType->description)), 'email')) {
                        $ref_part = $request->ref_part2;
                        if ($ref_part == 'option1') {
                            $hint .= ', ';
                        } elseif ($ref_part == 'option2') {

                            $hint .= 'From: ' . $document->reference . ', ';
                        } elseif ($ref_part == 'option3') {
                            $hint .= 'Ref: ' . $document->reference . ', ';
                        }
                    } else {
                        $hint .= 'Ref: ' . $document->reference . ', ';
                    }
                    $hint .= 'dated: ' . $date . '.';

                }
                $footnote->addText($hint, $GetStandardStylesFootNotes);
                $y++;
                } catch (\Exception $e) {
                    $listItemRun->addText(" [{$refText}]");
                }
                continue;
            }

            // Otherwise it's normal HTML
            $part = $closeTags($part);
            $part = str_replace('&', '&amp;', $part);

            try {
                \PhpOffice\PhpWord\Shared\Html::addHtml($listItemRun, $part, false, false);
            } catch (\Exception $e) {
                $clean = trim(strip_tags($part));
                if ($clean !== '') {
                    $listItemRun->addText($clean, ['name' => 'Calibri', 'size' => 11]);
                }
                error_log('Html::addHtml failed for fragment: ' . substr($part, 0, 200) . ' Error: ' . $e->getMessage());
            }
        }
    }

}
