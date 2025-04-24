<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\StorageFile;
use App\Models\Document;
use App\Models\ProjectFolder;
use App\Models\ProjectFile;
use App\Models\ContractTag;
use App\Models\FileDocument;
use Illuminate\Validation\Rule;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use Illuminate\Http\Response;
use SebastianBergmann\Type\FalseType;

class FileDocumentController extends ApiController
{
    public function index($id){
        $file=ProjectFile::where('slug',$id)->first();
        $documents = FileDocument::with('document')
    ->where('file_id', $file->id)
    ->get()
    ->sortBy([
        fn ($a, $b) => ($a->document->start_date ?? '9999-12-31') <=> ($b->document->start_date ?? '9999-12-31'),
        fn ($a, $b) => $a->sn <=> $b->sn,
    ])
    ->values();
        $specific_file_doc= session('specific_file_doc');
        session()->forget('specific_file_doc');
        $folders = ProjectFolder::where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive','Recycle Bin'])->pluck('name', 'id');

        return view('project_dashboard.file_documents.index',compact('documents','folders','file','specific_file_doc'));
    }

    public function exportWordClaimDocs($id){
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        $chapter = '4'; // Dynamic chapter number
        $sectionNumber = '2'; // Dynamic section number
        $phpWord->addNumberingStyle(
            'multilevel',
            [
                'type' => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels' => [
                    ['Heading0', 'format' => 'decimal', 'text' => "%1.", 'start' => (int)$chapter],
                    ['Heading1', 'format' => 'decimal', 'text' => "%1.%2", 'start' => (int)$sectionNumber],
                    ['Heading2', 'format' => 'decimal', 'text' => "%1.%2.%3", 'start' => 1,],
                    ['Heading3', 'format' => 'decimal', 'text' => "%1.%2.%3.%4", 'start' => 1,],
                    ['Heading3', 'format' => 'decimal', 'text' => '']
                ],
            ]
        );
        
        $phpWord->addNumberingStyle(
            'multilevel2',
            [
                'type' => 'multilevel',
                'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                'levels' => [
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
                'type' => 'multilevel', // Use 'multilevel' for bullet points
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
            'name'=>'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size' => 24,
            'bold' => true,
            'italic' => false,
            'underline'=>false
            
        ];
        $GetParagraphStyleH1=[
            'spaceBefore'=> 0,
            'spaceAfter' => 240,
            'lineHeight' => '1.5',
            'indentation' =>[
                'left'=>803.6,
                'hanging'=>803.6,
                'firstLine'=>0
            ],
            'contextualSpacing' => true,
            'next' => true,
            'keepNext' => true,
            'widowControl' => true,
        ];
        $GetStandardStylesH2 = [
            'name'=>'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size' => 16,
            'bold' => true,
            'italic' => false,
            'underline'=>false
            
        ];
        $GetParagraphStyleH2=[
            'spaceBefore'=> 0,
            'spaceAfter' => 240,
            'lineHeight' => '1.5',
            'indentation' =>[
                'left'=>1071.6,
                'hanging'=>1071.6,
                'firstLine'=>0
            ],
            'contextualSpacing' => true,
            'next' => true,
            'keepNext' => true,
            'widowControl' => true,
        ];

        $GetStandardStylesH3 = [
            'name'=>'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size' => 14,
            'bold' => false,
            'italic' => false,
            'underline'=>false
            
        ];
        $GetParagraphStyleH3=[
            'spaceBefore'=> 0,
            'spaceAfter' => 240,
            'lineHeight' => '1.5',
            'indentation' =>[
                'left'=>1071.6,
                'hanging'=>1071.6,
                'firstLine'=>0
            ],
            'contextualSpacing' => true,
            'next' => true,
            'keepNext' => true,
            'widowControl' => true,
        ];

        $GetStandardStylesSubtitle = [
            'name'=>'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size' => 14,
            'bold' => true,
            'italic' => false,
            'underline'=>false
            
        ];
        $GetParagraphStyleSubtitle=[
            'spaceBefore'=> 0,
            'spaceAfter' => 240,
            'lineHeight' => '1.5',
            'indentation' =>[
                'left'=>1071.6,
                'hanging'=>0,
                'firstLine'=>0
            ],
            'contextualSpacing' => true,
            'next' => true,
            'keepNext' => true,
            'widowControl' => true,
        ];
        $GetStandardStylesP = [
            'name'=>'Arial',
            'alignment' => 'left', // Options: left, center, right, justify
            'size' => 11,
            'bold' => false,
            'italic' => false,
            'underline'=>false
            
        ];
       
        $phpWord->addParagraphStyle('listParagraphStyle', [
            'spaceBefore'=> 0,
            'spaceAfter' => 240,
            'lineHeight' => '1.5',
            'indentation' =>[
                'left'=>1071.6,
                'hanging'=>1071.6,
                'firstLine'=>0
            ],
            'contextualSpacing' => false,
            'next' => true,
            'keepNext' => true,
            'widowControl' => true,
            'keepLines' => true,          
            'hyphenation' => false ,
            'pageBreakBefore'=>false
        ]);

        $phpWord->addParagraphStyle('listParagraphStyle2', [
            'spaceBefore'=> 0,
            'spaceAfter' => 10,
            'lineHeight' => '1.5',
            'indentation' =>[
                'left'=>1428.8,
                'hanging'=>357.2,
                'firstLine'=>0
            ],
            'contextualSpacing' => false,
            'next' => true,
            'keepNext' => true,
            'widowControl' => true,
            'keepLines' => true,          
            'hyphenation' => false ,
            'pageBreakBefore'=>false
        ]);
          
        $phpWord->addTitleStyle(1, $GetStandardStylesH1,$GetParagraphStyleH1);
        $phpWord->addTitleStyle(2, $GetStandardStylesH2, array_merge($GetParagraphStyleH2, ['numStyle' => 'multilevel', 'numLevel' => 1]));
        $phpWord->addTitleStyle(3, $GetStandardStylesH3,$GetParagraphStyleH3);

        $file=ProjectFile::where('slug',$id)->first();
        // Header (Level 1 Outline)
        $header = $file->name;
        $section->addTitle($header,2);

        //$section->addListItem($header, 0, ['size' => 16,'bold' => true,], 'multilevel');
        //dd($section);
        
        $subtitle = "Chronology of Event";
        $section->addText($subtitle, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);

        // Paragraphs
        $paragraphs=FileDocument::where('file_id',$file->id)->where('forClaim','1')->get();
       
        
        
        $GetStandardStylesFootNotes = [
            'name'=>'Calibri',
            'alignment' => 'left', // Options: left, center, right, justify
            'size' => 9,
            'bold' => false,
            'italic' => false,
            'underline'=>false
            
        ];
        $GetParagraphStyleFootNotes=[
            'spaceBefore'=> 0,
            'spaceAfter' => 0,
            'lineSpacing' => 240,
            'indentation' =>[
                'left'=>0,
                'hanging'=>0,
                'firstLine'=>0
            ]
        ];
        foreach ($paragraphs as $index => $paragraph) {
            //dd($paragraphs);
            $date=date("d F Y", strtotime($paragraph->document->start_date)); 
           
            // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
            $listNumber = "$chapter.$sectionNumber." . ($index + 1);
            $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;

           
            // Create a List Item Run (allows inline text styling + footnotes inside list items)
            $listItemRun = $section->addListItemRun(2, 'multilevel','listParagraphStyle');

            // Add the main sentence
            $listItemRun->addText("On ",$GetStandardStylesP);
            $existedList=false;
            // Add the date with a footnote
            $listItemRun->addText($date,$GetStandardStylesP);
            $footnote = $listItemRun->addFootnote($GetParagraphStyleFootNotes);
            $Exhibit=true;
            $dated=true;
            $senderAndDocType=true;
            $hint='';
            if($Exhibit){
                $hint="Exhibits " . $listNumber . ": ";
            }
            if($senderAndDocType){
                if($paragraph->document->from_id!=null){
                    $hint .=$paragraph->document->fromStakeHolder->name . "'s ";
                }
                $hint .=$paragraph->document->docType->name . " ";
                
            }
            $hint .="Ref: " . $paragraph->document->reference . ", ";
            if($dated){
                $hint .="dated: " . $date . ".";
            }
            $footnote->addText($hint,$GetStandardStylesFootNotes);
            $listItemRun->addText(", ",$GetStandardStylesP);
            if($paragraph->narrative==null){
                $listItemRun->addText("____________.");
            }else{
                if (!$containsHtml) {
                    $listItemRun->addText($paragraph->narrative . ".");
                }else{
                   
                    $paragraph_=$this->fixParagraphsWithImages($paragraph->narrative);
                   
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
                    //$paragraphsArray = preg_split('/(?=<p>)|(?<=<\/p>)/', $paragraphWithoutEmptyParagraphs);
    
                    // Step 4: Filter out empty elements
                    $paragraphsArray = array_filter($paragraphsArray, function($item) {
                        return !empty(trim($item));
                    });
                    
                    
                    // Step 5: Add each <p> tag to the document with a newline after it
                    foreach ($paragraphsArray as $index => $pTag) {
                        //dd($paragraphsArray);
                        if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {
                            
                            $imgPath = $matches[1]; // Extract image path
                            $altText = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                            $fullImagePath = public_path($imgPath); // Convert relative path to absolute
                        
                            if ($existedList) {
                                if (file_exists($fullImagePath)) {
                                    $textRun = $section->addTextRun([
                                        'spaceBefore' => 0,
                                        'spaceAfter' => 240,
                                        'lineHeight' => 0.9,
                                        'lineSpacing'=>'single',
                                        'indentation' => [
                                            'left' => 1071.6 
                                        ],
                                    ]);
                        
                                    // Add Image
                                    $shape =$textRun->addImage($fullImagePath, [
                                        'width' => 100,
                                        'height' => 80,
                                        'alignment' => 'left'
                                    ]);
                                   
                                    // Add Caption (Alt text)
                                    if (!empty($altText)) {
                                        $textRun->addTextBreak(); // New line
                                        $textRun->addText($altText . ".", [ 'name'=>'Calibri',
                                        'alignment' => 'left', // Options: left, center, right, justify
                                        'size' => 9,
                                        'bold' => false,
                                        'italic' => true,
                                        'underline'=>false]); // Add caption in italics
                                    }
                                }
                            } else {
                                if (file_exists($fullImagePath)) {
                                    // Add Image
                                    $listItemRun->addImage($fullImagePath, [
                                        'width' => 100,
                                        'height' => 80,
                                        'alignment' => 'left'
                                    ]);
                        
                                    // Add Caption (Alt text)
                                    if (!empty($altText)) {
                                        $listItemRun->addTextBreak(); // New line
                                        $listItemRun->addText($altText . ".", ['name'=>'Calibri',
                                        'alignment' => 'left', // Options: left, center, right, justify
                                        'size' => 9,
                                        'bold' => false,
                                        'italic' => true,
                                        'underline'=>false]); // Add caption in italics
                                    }
                                }
                            }
                        }elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {
                            
                            $imgPath = $matches[1]; // Extract image path
                            
                            $fullImagePath = public_path($imgPath); // Convert relative path to absolute
                        
                            if ($existedList) {
                                if (file_exists($fullImagePath)) {
                                    $textRun = $section->addTextRun([
                                        'spaceBefore' => 0,
                                        'spaceAfter' => 240,
                                        'lineHeight' => 1.5,
                                        'indentation' => [
                                            'left' => 1071.6 
                                        ],
                                    ]);
                        
                                    // Add Image
                                    $textRun->addImage($fullImagePath, [
                                        'width' => 100,
                                        'height' => 80,
                                        'alignment' => 'left'
                                    ]);
                        
                                   
                                }
                            } else {
                                if (file_exists($fullImagePath)) {
                                    // Add Image
                                    $listItemRun->addImage($fullImagePath, [
                                        'width' => 100,
                                        'height' => 80,
                                        'alignment' => 'left'
                                    ]);
                        
                                
                                }
                            }
                        }elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                            if (preg_match_all('/<li>(.*?)<\/li>/', $olMatches[1], $liMatches)) {
                                $listItems = $liMatches[1] ?? [];
                    
                                // Add each list item as a nested list item
                                foreach ($listItems as $item) {
                                    // Add a nested list item
                                    $nestedListItemRun = $section->addListItemRun(0, 'multilevel2','listParagraphStyle2'); // Use a numbering style
                                    $nestedListItemRun->addText($item);
                                }
                            }
                            $existedList=true;
                        }elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                            if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                $listItems = $liMatches[1] ?? [];
                    
                                // Add each list item as a nested list item
                                foreach ($listItems as $item) {
                                    // Add a nested list item
                                    $unNestedListItemRun = $section->addListItemRun(0, 'unordered','listParagraphStyle2'); // Use a numbering style
                                    $unNestedListItemRun->addText($item);
                                }
                            }
                            $existedList=true;
                        }else {
                            // If the paragraph contains only text (including <span>, <strong>, etc.)
                            try {
                                if($existedList){
                                   
                                    $listItemRun2 = $section->addListItemRun(4, 'multilevel', [
                                        'spaceBefore'=> 0,
                                        'spaceAfter' => 240,
                                        'lineHeight' => '1.5',
                                        'indentation' =>[
                                            'left'=>1071.6,
                                           
                                            
                                        ],
                                        'contextualSpacing' => false,
                                        'next' => true,
                                        'keepNext' => true,
                                        'widowControl' => true,
                                        'keepLines' => true,          
                                        'hyphenation' => false ,
                                        'pageBreakBefore'=>false
                                    ]);
                                    Html::addHtml($listItemRun2, $pTag, false, false);
                                }else{
                                    Html::addHtml($listItemRun, $pTag, false, false);
                                }
                                
                            } catch (\Exception $e) {
                                error_log("Error adding HTML: " . $e->getMessage());
                            }
                        }
                    
                        // Add a paragraph break after each element to separate them
                        if ($index < count($paragraphsArray) - 1) {
                            if($existedList==false){
                                $listItemRun->addTextBreak();
                            }
                                
                        }
                    }
                    
                   
                }
            }
            

        }
        
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/exports';
        $path = public_path($projectFolder);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        // Save document
        // Define file path in public folder
        $fileName = 'projects/' . auth()->user()->current_project_id . '/exports/' . auth()->user()->id . '_' . time() . '_Claim_Report.docx';
        $filePath = public_path($fileName);

        // Save document to public folder
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        // Return file as a response and delete after download
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    public function splitHtmlToArray($html)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Prevent warnings from invalid HTML
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
    
        $resultArray = [];
        $xpath = new \DOMXPath($dom);
        $elements = $xpath->query('//p | //ul | //ol'); // Select only <p>, <ul>, and <ol> elements
    
        foreach ($elements as $element) {
            $resultArray[] = $dom->saveHTML($element); // Store each element as a separate string
        }
    
        return $resultArray;
    }
    public function fixParagraphsWithImages($html)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $pElements = $xpath->query('//p');

        foreach ($pElements as $p) {
            $newNodes = [];
            $currentFragment = new \DOMDocument();
            $newP = $dom->createElement('p'); // Use the original document to avoid Wrong Document Error

            foreach (iterator_to_array($p->childNodes) as $child) {
                if ($child->nodeName === 'img') {
                    // If the current <p> already has text, save it
                    if ($newP->hasChildNodes()) {
                        $newNodes[] = $newP;
                        $newP = $dom->createElement('p');
                    }

                    // Create a new <p> for the image
                    $imgP = $dom->createElement('p');
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
   

    public function file_document_first_analyses($id){
        $user=auth()->user();
        session(['specific_file_doc' => $id]);
        $doc=FileDocument::findOrFail($id);
        $tags=ContractTag::where('account_id',$user->current_account_id)->where('project_id',$user->current_project_id)->orderBy('order','asc')->get();
        return view('project_dashboard.file_documents.doc_first_analyses',compact('doc','tags'));
    }

    public function upload_editor_image(Request $request)
    {

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:51200' // 10MB max
        ]);

        $file = $request->file('image');
        $name = $file->getClientOriginalName();
        $size = $file->getSize();
        $type = $file->getMimeType();

        $storageFile = StorageFile::where('user_id', auth()->user()->id)->where('project_id', auth()->user()->current_project_id)->where('file_name', $name)->where('size', $size)->where('file_type', $type)->first();
        if ($storageFile) {
            return response()->json([
                'success' => true,
                'file' => $storageFile
            ]);
        }
        $fileName = auth()->user()->id . '_' . time() . '_' . $file->getClientOriginalName();

        // Create project-specific folder in public path
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/images';
        $path = public_path($projectFolder);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Move file to public folder
        $file->move($path, $fileName);

        // Save file info to database
        $storageFile = StorageFile::create([
            'user_id' => auth()->user()->id,
            'project_id' => auth()->user()->current_project_id,
            'file_name' => $name,
            'size' => $size,
            'file_type' => $type,
            'path' => $projectFolder . '/' . $fileName
        ]);

        return response()->json([
            'success' => true,
            'file' => $storageFile
        ]);
    }
    private function hasContent($narrative) {
        // Remove all HTML tags except text content
        $text = strip_tags($narrative);
        
        // Remove extra spaces & line breaks
        $text = trim($text);
        
        // Check if there's any actual content
        return !empty($text);
    }
    public function store_file_document_first_analyses(Request $request,$id){
        if ($this->hasContent($request->narrative)) {
            $narrative=$request->narrative;
        } else {
            $narrative=null;
        }

        $doc = FileDocument::findOrFail($id);
        $doc->update([
            'narrative'  => $narrative,
            'notes1'     => $request->notes1,
            'notes2'     => $request->notes2,
            'sn'         => $request->sn,
            'forClaim'   => $request->forClaim ? '1' : '0',
            'forChart'   => $request->forChart ? '1' : '0',
            'forLetter'  => $request->forLetter ? '1' : '0',
        ]);
    
        // Assign tags (assuming many-to-many relationship)
        if ($request->has('tags')) {
            $doc->tags()->sync($request->tags); // Sync tags
        }
        if($request->action=='save'){
            return redirect('/project/file-document-first-analyses/'. $doc->id)->with('success', 'analyses for "' . $doc->document->subject .'" document saved successfully.');
        }else{
            return redirect('/project/file/' . $doc->file->slug . '/documents')->with('success', 'analyses for "' . $doc->document->subject .'" document saved successfully.');
        }

        
        
    }
    public function download_doc(){
            
    }

    public function copy_move_doc_to_another_file(Request $request){
        $file_doc=FileDocument::findOrFail($request->document_id);
        if($request->actionType=='copy'){
            $fileDoc = FileDocument::where('file_id', $request->file_id)->where('document_id', $file_doc->document_id)->first();
            if (!$fileDoc) {
                $doc=FileDocument::create(['user_id' => auth()->user()->id,
                                      'file_id' => $request->file_id,
                                      'narrative'  => $file_doc->narrative,
                                      'notes1'     => $file_doc->notes1,
                                      'notes2'     => $file_doc->notes2,
                                      'sn'         => $file_doc->sn,
                                      'forClaim'   => $file_doc->forClaim ,
                                      'forChart'   => $file_doc->forChart ,
                                      'forLetter'  => $file_doc->forLetter ,
                                      'document_id' => $file_doc->document_id]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Document copied To Selected File Successfully.',
                   // 'redirect' => url('/project/file/' . $file_doc->file->slug . '/documents')
                ]);
            }else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Document is existed in selected file.',
                   // 'redirect' => url('/project/file/' . $file_doc->file->slug . '/documents')
                ]);
            }
        }elseif($request->actionType=='move'){
            $fileDoc = FileDocument::where('file_id', $request->file_id)->where('document_id', $file_doc->document_id)->first();
            $currentFile=$file_doc->file->slug;
            if (!$fileDoc) {
                $file_doc->file_id=$request->file_id;
                $file_doc->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Document Moved To Selected File Successfully.',
                    //'redirect' => url('/project/file/' . $currentFile . '/documents')
                ]);

            }else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Document is existed in selected file.',
                    //'redirect' => url('/project/file/' . $currentFile . '/documents')
                ]);
            }
        }
    }

    public function unassign_doc(Request $request){
        FileDocument::whereIn('id',$request->document_ids)->delete();
        return response()->json([
            'status' => 'success',
            'message' => count($request->document_ids)>1 ? 'Selected documents is unassigned from file.' : 'Document is unassigned from file.',
            //'redirect' => url('/project/file/' . $currentFile . '/documents')
        ]);
    }

    public function delete_doc_from_cmw_entirely(Request $request){
        foreach($request->document_ids as $doc_id){
            $doc=FileDocument::findOrFail($doc_id);
            $document=Document::find($doc->document_id);
            FileDocument::where('document_id',$document->id)->delete();
            $docs = Document::where('storage_file_id', $document->storage_file_id)->where('id', '!=', $document->id)->get();
            if (count($docs) == 0) {
                $path = public_path($document->storageFile->path);

                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $document->delete();
        }
        return response()->json([
            'status' => 'success',
            'message' => count($request->document_ids)>1 ? 'Selected documents is deleted from CMW entirely.' : 'Document is deleted from CMW entirely.',
            //'redirect' => url('/project/file/' . $currentFile . '/documents')
        ]);
        
    }

    public function change_for_claimOrNoticeOrChart(Request $request){
        FileDocument::whereIn('id',$request->document_ids)->update([$request->action_type=>'1']);
        return response()->json([
            'status' => 'success',
        ]);
    }
}