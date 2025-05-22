<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\Document;
use App\Models\FileAttachment;
use App\Models\ProjectFolder;
use App\Models\ProjectFile;
use App\Models\FileDocument;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class FileController extends ApiController
{
    public function index(){
        $zip_file= session('zip_file');
        if($zip_file){
            $filePath=public_path('projects/' . auth()->user()->current_project_id . '/temp/'.$zip_file) ;
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $user = auth()->user();
        $folder = ProjectFolder::findOrFail($user->current_folder_id);
        $all_files = ProjectFile::where('folder_id',$folder->id)->orderBy('code', 'asc')->get(); 
        $folders = ProjectFolder::where('id','!=',$folder->id)->where('project_id', auth()->user()->current_project_id)->whereNotIn('name', ['Archive','Recycle Bin'])->pluck('name', 'id');

        return view('project_dashboard.project_files.index', compact('all_files','folders','folder','users'));
    }

    public function create(){
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $user = auth()->user();
        $folder = ProjectFolder::findOrFail($user->current_folder_id);
        $stake_holders = $project->stakeHolders;

        return view('project_dashboard.project_files.create', compact('folder','users','stake_holders'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required', // 10MB max
            'owner_id' => 'required|exists:users,id',
        ]);
        do {
            $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        } while (ProjectFile::where('slug', $invitation_code)->exists());

        $file=ProjectFile::create(['name'=>$request->name,'slug'=>$invitation_code,'code'=>$request->code,
                                    'user_id'=>$request->owner_id,
                                    'project_id'=>auth()->user()->current_project_id,'against_id'=>$request->against_id,'start_date'=>$request->start_date,
                                    'end_date'=>$request->end_date,'folder_id'=>auth()->user()->current_folder_id,
                                    'notes'=>$request->notes]);
        if($request->time){
            $file->time='1';
        }
        if($request->prolongation_cost){
            $file->prolongation_cost='1';
        }
        if($request->disruption_cost){
            $file->disruption_cost='1';
        }
        if($request->variation){
            $file->variation='1';
        }
        if($request->closed){
            $file->closed='1';
        }
        if($request->assess_not_pursue){
            $file->assess_not_pursue='1';
        }
        $file->save();
        return redirect('/project/files')->with('success', 'File Created successfully.');

    }

    public function edit($id){
        $project = Project::findOrFail(auth()->user()->current_project_id);
        $users = $project->assign_users;
        $user = auth()->user();
        $folder = ProjectFolder::findOrFail($user->current_folder_id);
        $stake_holders = $project->stakeHolders;
        $file = ProjectFile::where('slug',$id)->first();
        return view('project_dashboard.project_files.edit', compact('folder','users','stake_holders','file'));
    }

    public function update(Request $request,$id){
        $request->validate([
            'name' => 'required', // 10MB max
            'owner_id' => 'required|exists:users,id',
        ]);
        

        ProjectFile::where('id',$id)->update(['name'=>$request->name,'code'=>$request->code,
                                    'user_id'=>$request->owner_id,
                                    'against_id'=>$request->against_id,'start_date'=>$request->start_date,
                                    'end_date'=>$request->end_date,
                                    'notes'=>$request->notes]);
        $file=ProjectFile::findOrFail($id);
        if($request->time){
            $file->time='1';
        }else{
            $file->time='0';
        }
        if($request->prolongation_cost){
            $file->prolongation_cost='1';
        }else{
            $file->prolongation_cost='0';
        }
        if($request->disruption_cost){
            $file->disruption_cost='1';
        }else{
            $file->disruption_cost='0';
        }
        if($request->variation){
            $file->variation='1';
        }else{
            $file->variation='0';
        }
        if($request->closed){
            $file->closed='1';
        }else{
            $file->closed='0';
        }
        if($request->assess_not_pursue){
            $file->assess_not_pursue='1';
        }else{
            $file->assess_not_pursue='0';
        }
        $file->save();
        return redirect('/project/files')->with('success', 'File Updated successfully.');
    }
    public function changeOwner(Request $request)
    {
        $request->validate([
            'file_id' => 'required|exists:project_files,id',
            'new_owner_id' => 'required|exists:users,id',
        ]);

        $file = ProjectFile::find($request->file_id);
        $file->user_id = $request->new_owner_id;
        $file->save();

        return response()->json(['success' => true]);
    }

    public function delete($id){
        $file = ProjectFile::where('id', $id)->first();
        $user = auth()->user();
        $Archive = ProjectFolder::where('account_id', $user->current_account_id)
        ->where('project_id', $user->current_project_id)->where('name','Archive')
        ->first();
        $Folder = ProjectFolder::where('account_id', $user->current_account_id)
            ->where('project_id', $user->current_project_id)->where('name','Recycle Bin')
            ->first();
        if($file->older_folder_id == null){
            
            $file->older_folder_id = $file->folder_id;
            $file->folder_id = $Folder->id;
            $file->save();
        }elseif($file->folder_id == $Archive->id){
            $file->folder_id = $Folder->id;
            $file->save();
        }else{
            FileAttachment::where('file_id',$file->id)->delete();
            FileDocument::where('file_id',$file->id)->delete();
            $file->delete();
           
        }
        return redirect('/project/files')->with('success', 'File Deleted successfully.');

    }

    public function archive($id){
        $file = ProjectFile::where('id', $id)->first();
        $user = auth()->user();
        $Folder = ProjectFolder::where('account_id', $user->current_account_id)
        ->where('project_id', $user->current_project_id)->where('name','Archive')
        ->first();
        if($file->older_folder_id==null){
            $file->older_folder_id = $file->folder_id;
        }
        $file->folder_id = $Folder->id;
        $file->save();
        return redirect('/project/files')->with('success', 'File Archive successfully.');

    }


    public function exportWordClaimDocs(Request $request){
        
        $zip_file= session('zip_file');
        if($zip_file){
            $filePath=public_path('projects/' . auth()->user()->current_project_id . '/temp/'.$zip_file) ;
            if (File::exists($filePath)) {
                File::deleteDirectory($filePath);
            }
            session()->forget('zip_file');
        }
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        
        $chapter = $request->Chapter; // Dynamic chapter number
        $sectionNumber = $request->Section; // Dynamic section number
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

        $file=ProjectFile::where('slug',$request->file_id111)->first();
        // Header (Level 1 Outline)
        $header = $file->name;
        $header = str_replace('&', '&amp;', $header);
        $section->addTitle($header,2);
        
        
        $paragraphs = FileAttachment::where('section','1')->where('file_id', $file->id);
        if($request->forclaimdocs){
            $paragraphs->where('forClaim','1');
        }
            
        $paragraphs=$paragraphs->orderBy('order','asc')->get();
        
        
        if(count($paragraphs)>0){
            $subtitle1 = $request->subtitle1;
            $subtitle1 = str_replace('&', '&amp;', $subtitle1);
            $section->addText($subtitle1, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
            foreach ($paragraphs as $index => $paragraph) {
                //dd($paragraphs);
                $listItemRun = $section->addListItemRun(2, 'multilevel','listParagraphStyle');
                $existedList1=false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;
            
                
                
                
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
                        foreach ($paragraphsArray as $index2 => $pTag) {
                            //dd($paragraphsArray);
                            if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {
                                
                                $imgPath = $matches[1]; // Extract image path
                                $altText = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute
                            
                                if ($existedList1) {
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
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $textRun->addTextBreak();
                                            }
                                            
                                                
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
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }
                                }
                            }elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {
                                
                                $imgPath = $matches[1]; // Extract image path
                                
                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute
                            
                                if ($existedList1) {
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
                            
                                    
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $textRun->addTextBreak();
                                            }
                                            
                                                
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
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    
                                    }
                                }
                            }elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1'.$index.$index2.'1',
                                    [
                                        'type' => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels' => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.']
                                            
                                            
                                        
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
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1'.$index.$index2.'1','listParagraphStyle2'); // Use a numbering style
                                        // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList1=true;
                            }elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];
                                
                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                        // Add a nested list item
                                        //dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered','listParagraphStyle2'); // Use a numbering style
                                        // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        Html::addHtml($unNestedListItemRun, $item, false, false);
                                    }
                                }
                            
                                $existedList1=true;
                            }else {
                            
                                // If the paragraph contains only text (including <span>, <strong>, etc.)
                                try {
                                    if($existedList1){
                                    
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
                                        $pTag=$this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun2->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }else{
                                        //$pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag=$this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        Html::addHtml($listItemRun, $pTag, false, false);
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }
                                    
                                } catch (\Exception $e) {
                                    error_log("Error adding HTML: " . $e->getMessage());
                                }
                            }
                            
                        }
                        
                    
                    }
                }
                
                
            }
        }
    
        //$paragraphs=FileDocument::where('file_id',$file->id)->where('forClaim','1')->orderBy()->get();
        $paragraphs = FileDocument::with(['document', 'note'])
            ->where('file_id', $file->id);
        if($request->forclaimdocs){
            $paragraphs->where('forClaim','1');
        }
            
        $paragraphs=$paragraphs->get()
                        ->sortBy([
                            fn ($a, $b) => ($a->document->start_date ?? $a->note->start_date ?? '9999-12-31')
                                        <=> ($b->document->start_date ?? $b->note->start_date ?? '9999-12-31'),
                            fn ($a, $b) => $a->sn <=> $b->sn,
                        ])
                        ->values();
        if(count($paragraphs)>0){
            $subtitle2 = $request->subtitle2;
            $subtitle2 = str_replace('&', '&amp;', $subtitle2);
            $section->addText($subtitle2, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
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
            $x=intval($request->Start);
            foreach ($paragraphs as $index => $paragraph) {
                //dd($paragraphs);
                $listItemRun = $section->addListItemRun(2, 'multilevel','listParagraphStyle');
                $existedList=false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;
                if($paragraph->document){
                    $date=date("d F Y", strtotime($paragraph->document->start_date)); 
    
                    // Add the main sentence
                    $listItemRun->addText("On ",$GetStandardStylesP);
                    
                    // Add the date with a footnote
                    $listItemRun->addText($date,$GetStandardStylesP);
                    $footnote = $listItemRun->addFootnote($GetParagraphStyleFootNotes);
                    $Exhibit=true;
                    $dated=true;
                    $senderAndDocType=true;
                    $hint='';
                    if($request->formate_type2=='reference'){
                        $hint=$paragraph->document->reference . ".";
                    }elseif($request->formate_type2=='dateAndReference'){
                       
                        $date2 = date('y_m_d', strtotime($paragraph->document->start_date));
                        $hint = preg_replace('/_/', '', $date2) . ' - ' . $paragraph->document->reference . '.';
                    }elseif($request->formate_type2=='formate'){
                        $sn=$request->sn2;
                        $prefix=$request->prefix2;
                        $listNumber = "$prefix" . str_pad($x, $sn, '0', STR_PAD_LEFT);
                        $hint=$listNumber . ": ";
                        $from=$paragraph->document->fromStakeHolder? $paragraph->document->fromStakeHolder->narrative . "'s " : '';
                        $type=$paragraph->document->docType->name;
                        $hint .=$from . $type . " ";
                        if(str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $paragraph->document->docType->name)),'email') || str_contains(strtolower(preg_replace('/[\\\\\/:*?"+.<>\|{}\[\]`\-]/', '', $paragraph->document->docType->description)),'email')){
                            $ref_part=$request->ref_part2;
                            if($ref_part == 'option1'){
                                $hint .= ', ';
                            }elseif($ref_part == 'option2'){
                               
                                $hint .= 'From: ' . $paragraph->document->reference . ', ';
                            }elseif($ref_part == 'option3'){
                                $hint .= 'Ref: ' . $paragraph->document->reference . ', ';
                            }
                        }else{
                            $hint .= 'Ref: ' . $paragraph->document->reference . ', ';
                        }
                        $hint .= 'dated: ' . $date . '.';
        
                    }
                    $footnote->addText($hint,$GetStandardStylesFootNotes);
                    $listItemRun->addText(", ",$GetStandardStylesP);
                    $x++;
                }else{
                    $listItemRun->addText("Note: ",$GetStandardStylesP);
                }
                
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
                        foreach ($paragraphsArray as $index2 => $pTag) {
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
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                               
                                                $textRun->addTextBreak();
                                            }
                                            
                                                
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
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                               
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
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
                            
                                       
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                               
                                                $textRun->addTextBreak();
                                            }
                                            
                                                
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
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                               
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    
                                    }
                                }
                            }elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1'.$index.$index2.'2',
                                    [
                                        'type' => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels' => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.']
                                            
                                              
                                           
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
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1'.$index.$index2.'2','listParagraphStyle2'); // Use a numbering style
                                        // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList=true;
                            }elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];
                                  
                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                        // Add a nested list item
                                        //dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered','listParagraphStyle2'); // Use a numbering style
                                        // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        Html::addHtml($unNestedListItemRun, $item, false, false);
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
                                        $pTag=$this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                               
                                                $listItemRun2->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }else{
                                        //$pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag=$this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        Html::addHtml($listItemRun, $pTag, false, false);
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                               
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }
                                    
                                } catch (\Exception $e) {
                                    error_log("Error adding HTML: " . $e->getMessage());
                                }
                            }
                        
                            
                            
                        }
                        
                       
                    }
                }
                
                
            }
        }
        

        $paragraphs = FileAttachment::where('section','2')->where('file_id', $file->id);
        if($request->forclaimdocs){
            $paragraphs->where('forClaim','1');
        }
            
        $paragraphs=$paragraphs->orderBy('order','asc')->get();
        
        
        if(count($paragraphs)>0){
            $subtitle3 = $request->subtitle3;
            $subtitle3 = str_replace('&', '&amp;', $subtitle3);
            $section->addText($subtitle3, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
            foreach ($paragraphs as $index => $paragraph) {
                //dd($paragraphs);
                $listItemRun = $section->addListItemRun(2, 'multilevel','listParagraphStyle');
                $existedList2=false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;
            
                
                
                
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
                        foreach ($paragraphsArray as $index2 => $pTag) {
                            //dd($paragraphsArray);
                            if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {
                                
                                $imgPath = $matches[1]; // Extract image path
                                $altText = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute
                            
                                if ($existedList2) {
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
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $textRun->addTextBreak();
                                            }
                                            
                                                
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
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }
                                }
                            }elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {
                                
                                $imgPath = $matches[1]; // Extract image path
                                
                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute
                            
                                if ($existedList2) {
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
                            
                                    
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $textRun->addTextBreak();
                                            }
                                            
                                                
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
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    
                                    }
                                }
                            }elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1'.$index.$index2.'3',
                                    [
                                        'type' => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels' => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.']
                                            
                                            
                                        
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
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1'.$index.$index2.'3','listParagraphStyle2'); // Use a numbering style
                                        // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList2=true;
                            }elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];
                                
                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                        // Add a nested list item
                                        //dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered','listParagraphStyle2'); // Use a numbering style
                                        // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        Html::addHtml($unNestedListItemRun, $item, false, false);
                                    }
                                }
                            
                                $existedList2=true;
                            }else {
                            
                                // If the paragraph contains only text (including <span>, <strong>, etc.)
                                try {
                                    if($existedList2){
                                    
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
                                        $pTag=$this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun2->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }else{
                                        //$pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag=$this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        Html::addHtml($listItemRun, $pTag, false, false);
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }
                                    
                                } catch (\Exception $e) {
                                    error_log("Error adding HTML: " . $e->getMessage());
                                }
                            }
                            
                        }
                        
                    
                    }
                }
                
                
            }
        }
    
        

        
        $paragraphs = FileAttachment::where('section','3')->where('file_id', $file->id);
        if($request->forclaimdocs){
            $paragraphs->where('forClaim','1');
        }
            
        $paragraphs=$paragraphs->orderBy('order','asc')->get();
        
        
        if(count($paragraphs)>0){
            $subtitle4 = $request->subtitle4;
            $subtitle4 = str_replace('&', '&amp;', $subtitle4);
            $section->addText($subtitle4, $GetStandardStylesSubtitle, $GetParagraphStyleSubtitle);
            foreach ($paragraphs as $index => $paragraph) {
                //dd($paragraphs);
                $listItemRun = $section->addListItemRun(2, 'multilevel','listParagraphStyle');
                $existedList3=false;
                // Generate list item number dynamically (e.g., "4.1.1", "4.1.2", etc.)
                $containsHtml = strip_tags($paragraph->narrative) !== $paragraph->narrative;
            
                
                
                
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
                        foreach ($paragraphsArray as $index2 => $pTag) {
                            //dd($paragraphsArray);
                            if (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*alt=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {
                                
                                $imgPath = $matches[1]; // Extract image path
                                $altText = isset($matches[2]) ? trim($matches[2]) : ''; // Extract alt text if exists
                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute
                            
                                if ($existedList3) {
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
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $textRun->addTextBreak();
                                            }
                                            
                                                
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
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }
                                }
                            }elseif (preg_match('/<img[^>]*src=["\'](.*?)["\'][^>]*>/i', $pTag, $matches)) {
                                
                                $imgPath = $matches[1]; // Extract image path
                                
                                $fullImagePath = public_path($imgPath); // Convert relative path to absolute
                            
                                if ($existedList3) {
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
                            
                                    
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $textRun->addTextBreak();
                                            }
                                            
                                                
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
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    
                                    }
                                }
                            }elseif (preg_match('/<ol>(.*?)<\/ol>/is', $pTag, $olMatches)) {
                                $phpWord->addNumberingStyle(
                                    'multilevel_1'.$index.$index2.'4',
                                    [
                                        'type' => 'multilevel',
                                        'listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
                                        'levels' => [
                                            ['Heading5', 'format' => 'decimal', 'text' => '%1.']
                                            
                                            
                                        
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
                                        $nestedListItemRun = $section->addListItemRun(0, 'multilevel_1'.$index.$index2.'4','listParagraphStyle2'); // Use a numbering style
                                        // $nestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        Html::addHtml($nestedListItemRun, $item, false, false);
                                    }
                                }
                                $existedList3=true;
                            }elseif (preg_match('/<ul>(.*?)<\/ul>/is', $pTag, $ulMatches)) {
                                if (preg_match_all('/<li>(.*?)<\/li>/', $ulMatches[1], $liMatches)) {
                                    $listItems = $liMatches[1] ?? [];
                                
                                    // Add each list item as a nested list item
                                    foreach ($listItems as $item) {
                                        // Add a nested list item
                                        //dd($listItems);
                                        $unNestedListItemRun = $section->addListItemRun(0, 'unordered','listParagraphStyle2'); // Use a numbering style
                                        // $unNestedListItemRun->addText($item);
                                        $item = str_replace('&', '&amp;', $item);
                                        Html::addHtml($unNestedListItemRun, $item, false, false);
                                    }
                                }
                            
                                $existedList3=true;
                            }else {
                            
                                // If the paragraph contains only text (including <span>, <strong>, etc.)
                                try {
                                    if($existedList3){
                                    
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
                                        $pTag=$this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        Html::addHtml($listItemRun2, $pTag, false, false);
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun2->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }else{
                                        //$pTagEscaped = htmlspecialchars($pTag, ENT_QUOTES, 'UTF-8');
                                        $pTag=$this->lowercaseFirstCharOnly($pTag);
                                        $pTag = str_replace('&', '&amp;', $pTag);
                                        Html::addHtml($listItemRun, $pTag, false, false);
                                        
                                        if ($index2 < count($paragraphsArray) - 1) {
                                
                                            if (isset($paragraphsArray[$index2 + 1]) && stripos($paragraphsArray[$index2+1], '<ol>') === false && stripos($paragraphsArray[$index2+1], '<ul>') === false) {
                                            
                                                $listItemRun->addTextBreak();
                                            }
                                            
                                                
                                        }
                                    }
                                    
                                } catch (\Exception $e) {
                                    error_log("Error adding HTML: " . $e->getMessage());
                                }
                            }
                            
                        }
                        
                    
                    }
                }
                
                
            }
        }
        $projectFolder = 'projects/' . auth()->user()->current_project_id . '/temp';
        $path = public_path($projectFolder);
        if (!file_exists($path)) {
           
            mkdir($path, 0755, true);
        }
        $code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
        $directory = public_path('projects/' . auth()->user()->current_project_id . '/temp/' . $code);

        if (!file_exists($directory)) {
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
        //return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function lowercaseFirstCharOnly($html) {
        return preg_replace_callback(
            '/(?:^|>)(T)/u',  // Match only "T" after start or closing tag
            function ($matches) {
                return str_replace('T', 't', $matches[0]);
            },
            $html,
            1 // Only first match
        );
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

    public function copy_move_file(Request $request){
        $file=ProjectFile::where('slug',$request->file_id)->first();
        $counter=1;
        $ex='';
        if($request->action_type=='Copy'){
            do {
                $name = $file->name. $ex;
                $ex=' (' . $counter . ')';
                $counter++;
            } while (ProjectFile::where('name', $name)->where('folder_id',$request->folder_id)->exists());
            do {
                $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
            } while (ProjectFile::where('slug', $slug)->exists());
            $new_file=ProjectFile::create(['name'=>$name,'slug'=>$slug,'code'=>$file->code,
                                 'user_id'=>$file->user_id,'project_id'=>$file->project_id,
                                 'against_id'=>$file->against_id,'start_date'=>$file->start_date,
                                 'end_date'=>$file->end_date,'folder_id'=>$request->folder_id,
                                 'notes'=>$file->notes,'time'=>$file->time,'prolongation_cost'=>$file->prolongation_cost,
                                 'disruption_cost'=>$file->disruption_cost,'variation'=>$file->variation,
                                 'closed'=>$file->closed,'assess_not_pursue'=>$file->assess_not_pursue]);
            $file_attachment=FileAttachment::where('file_id',$file->id)->get();
            foreach($file_attachment as $attachment){
                FileAttachment::create(['file_id'=>$new_file->id,'user_id'=>auth()->user()->id,
                                        'order'=>$attachment->order,
                                        'narrative'=>$attachment->narrative,
                                        'forClaim'=>$attachment->forClaim,
                                        'section'=>$attachment->section]);
            }
            $file_documents=FileDocument::where('file_id',$file->id)->get();
             foreach($file_documents as $doc){
                $new_doc=FileDocument::create(['file_id'=>$new_file->id,'user_id' => auth()->user()->id,
                                      'document_id'=>$doc->document_id,
                                      'note_id'=>$doc->note_id,
                                      'sn'=>$doc->sn,'forClaim'=> $doc->forClaim,'narrative'=> $doc->narrative,'notes1'=> $doc->notes1,
                                      'forChart'=> $doc->forChart,'notes2'=> $doc->notes2,
                                      'forLetter'=> $doc->forLetter]);
                $ids=$doc->tags->pluck('id')->toArray();
                if (count($ids)>0) {
                    $new_doc->tags()->sync($ids); // Sync tags
                }
             }
            return response()->json([
                            'status' => 'success',
                            'message' => 'File Copied To Selected Folder Successfully.',

                        // 'redirect' => url('/project/file/' . $file_doc->file->slug . '/documents')
            ]);
        }elseif($request->action_type=='Move'){
            do {
                $name = $file->name. $ex;
                $ex=' (' . $counter . ')';
                $counter++;
            } while (ProjectFile::where('name', $name)->where('folder_id',$request->folder_id)->exists());
            $file->name=$name;
            $file->folder_id = $request->folder_id;
            $file->save();
            return response()->json([
                            'status' => 'success',
                            'message' => 'File Moved To Selected Folder Successfully.',

                        // 'redirect' => url('/project/file/' . $file_doc->file->slug . '/documents')
            ]);
        }
    }

}