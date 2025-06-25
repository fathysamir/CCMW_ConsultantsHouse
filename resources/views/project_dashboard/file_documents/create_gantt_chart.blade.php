 <style>
     .date {
         background-color: #fff !important;
     }

     .date2 {
         padding-left: 5px;
         padding-right: 5px;
     }
 </style>
 <form id="ganttChartForm">
     <input type="hidden" id="FD_ID" name="document_id" value="{{ $id }}">
     <div class="row">
         <div class="col-md-4" style="border-right: 1px solid #d6d4d4;">
             <h4 style="text-align: center;">Current Bar</h4>
          
             <div class="custom-control custom-checkbox mb-3">
                 <input type="checkbox" class="custom-control-input"id="show_cur" name="show_cur">
                 <label class="custom-control-label" for="show_cur">Show Current Bar</label>
             </div>
             <div class="form-group mb-3">
                 <label for="cur_type">Type <span style="color: red">*</span></label>
                 <select class="form-control" id="cur_type" required name="cur_type">
                     <option value="" disabled selected>please select</option>
                     @if ($end_date != null)
                         <option value="SB">Single Bar</option>
                         <option value="DA">Double Arrow</option>
                         <option value="MS">Multi-Section Bar</option>
                     @else
                         <option value="M">Milestone</option>
                         <option value="S">Star</option>
                     @endif
                 </select>
             </div>
             <div class="form-group mb-3 d-none">
                 <label for="cur_sd">Start Date</label>
                 <input disabled type="date"style="background-color:#fff;" name="cur_sd" id="cur_sd"
                     class="date form-control date2" placeholder="Start Date" value="{{ $start_date }}">
             </div>
             <div class="form-group mb-3 d-none">
                 <label for="cur_fd">Finish Date</label>
                 <input disabled type="date"style="background-color:#fff;" name="cur_fd" id="cur_fd"
                     class="date form-control date2" placeholder="Finish Date"value="{{ $end_date }}">
             </div>
             <div class="form-group mb-3 d-none">
                 <label for="cu_color">Color</label>
                 <select class="form-control" id="cu_color" name="cu_color">
                     <option value="808080" style="background-color: #808080; color: white;">■ Gray</option>
                     <option selected value="00008B" style="background-color: #00008B; color: white;">■ Dark Blue</option>
                     <option value="FF0000" style="background-color: #FF0000; color: white;">■ Red</option>
                     <option value="008000" style="background-color: #008000; color: white;">■ Green</option>
                     <option value="000000" style="background-color: #000000; color: white;">■ Black</option>
                     <option value="89CFF0" style="background-color: #89CFF0;">■ Baby Blue</option>
                     <option value="FFFF00" style="background-color: #FFFF00;">■ Yellow</option>
                     <option value="8B4513" style="background-color: #8B4513; color: white;">■ Brown</option>
                     <option value="FFFFFF" style="background-color: #FFFFFF;">■ White</option>
                     <option value="FFFFFF00">■ Gap</option>
                 </select>
             </div>
             <div class="form-group mb-3 d-none" id="multi-sec">
                 <label for="cur_sd">Sections</label>
                 <div id="multi-sections-wrapper">
                     <div style="display: flex" class="multi-section mb-2">
                         <div style="padding-left: 0px;padding-right: 3px;width: 35%;">
                             <input disabled type="date"style="background-color:#fff;" name="sections[sd]"
                                 class="date form-control date2" placeholder="S Date" value="{{ $start_date }}">
                         </div>
                         <div style="padding-left: 0px;padding-right: 3px;width: 35%;">
                             <input required type="date"style="background-color:#fff;" name="sections[fd]"
                                 class="date form-control date2" placeholder="F Date">
                         </div>
                         <div style="padding-left: 0px;padding-right: 3px;">
                             <select class="form-control" name="sections[color]">
                                 <option value="808080" style="background-color: #808080; color: white;">■ Gray</option>
                                 <option selected value="00008B" style="background-color: #00008B; color: white;">■ Dark Blue
                                 </option>
                                 <option value="FF0000" style="background-color: #FF0000; color: white;">■ Red</option>
                                 <option value="008000" style="background-color: #008000; color: white;">■ Green
                                 </option>
                                 <option value="000000" style="background-color: #000000; color: white;">■ Black
                                 </option>
                                 <option value="89CFF0" style="background-color: #89CFF0;">■ Baby Blue</option>
                                 <option value="FFFF00" style="background-color: #FFFF00;">■ Yellow</option>
                                 <option value="8B4513" style="background-color: #8B4513; color: white;">■ Brown
                                 </option>
                                 <option value="FFFFFF" style="background-color: #FFFFFF;">■ White</option>
                                 <option value="FFFFFF00">■ Gap</option>
                             </select>
                         </div>
                        
                         <div style="padding-left: 0px;padding-right: 3px;">
                             <button type="button" class="btn btn-sm btn-outline-primary w-100 h-100 add-btnooo"
                                 title="Add" style="border-color: rgb(200, 214, 238);font-size:17px;">
                                 +
                             </button>
                         </div>
                          <div style="padding-left: 0px;padding-right: 0px;width:35.1px;">
                            
                         </div>
                     </div>
                     <div style="display: flex" class="multi-section">
                         <div style="padding-left: 0px;padding-right: 3px;width: 35%;">
                             <input disabled type="date"style="background-color:#fff;" name="sections[sd]"
                                 class="date form-control date2" placeholder="S Date"value="{{ $end_date }}">
                         </div>
                         <div style="padding-left: 0px;padding-right: 3px;width: 35%;">
                             <input disabled type="date"style="background-color:#fff;" name="sections[fd]"
                                 class="date form-control date2" placeholder="F Date"value="{{ $end_date }}">
                         </div>
                         <div style="padding-left: 0px;padding-right: 3px;">
                             <select class="form-control" id="cu_color" name="sections[color]">
                                 <option value="808080" style="background-color: #808080; color: white;">■ Gray
                                 </option>
                                 <option selected value="00008B" style="background-color: #00008B; color: white;">■ Dark Blue
                                 </option>
                                 <option value="FF0000" style="background-color: #FF0000; color: white;">■ Red
                                 </option>
                                 <option value="008000" style="background-color: #008000; color: white;">■ Green
                                 </option>
                                 <option value="000000" style="background-color: #000000; color: white;">■ Black
                                 </option>
                                 <option value="89CFF0" style="background-color: #89CFF0;">■ Baby Blue</option>
                                 <option value="FFFF00" style="background-color: #FFFF00;">■ Yellow</option>
                                 <option value="8B4513" style="background-color: #8B4513; color: white;">■ Brown
                                 </option>
                                 <option value="FFFFFF" style="background-color: #FFFFFF;">■ White</option>
                                 <option value="FFFFFF00">■ Gap</option>
                             </select>
                         </div>
                         <div style="padding-left: 0px;padding-right: 3px;width:35.1px;">
                             
                         </div>
                         <div style="padding-left: 0px;padding-right: 0px;width:35.1px;">

                         </div>
                     </div>
                 </div>
             </div>
             <hr>
             <div class="form-group mb-3">
                 <label for="cur_left_caption">Left Caption</label>
                 <textarea name="cur_left_caption" rows="1" id="cur_left_caption" class="form-control"></textarea>
             </div>
             <div class="form-group mb-3">
                 <label for="cur_right_caption">Right Caption</label>
                 <textarea name="cur_right_caption" rows="1" id="cur_right_caption" class="form-control"></textarea>
             </div>
             <div class="custom-control custom-checkbox mb-1">
                 <input type="checkbox" class="custom-control-input"id="cur_show_sd" name="cur_show_sd">
                 <label class="custom-control-label" for="cur_show_sd">Show Start Date</label>
             </div>
             <div class="custom-control custom-checkbox mb-3">
                 <input type="checkbox" class="custom-control-input"id="cur_show_fd" name="cur_show_fd">
                 <label class="custom-control-label" for="cur_show_fd">Show Finish Date</label>
             </div>
             <div class="form-group mb-3">
                 <label>Show Ref.</label>
                 <div style="display: flex">
                     <div class="custom-control custom-radio" style="width: 33.3%;">
                         <input type="radio" id="l" name="cur_show_ref" value="l"
                             class="custom-control-input" {{ $type=='n' ? 'disabled' : '' }}>
                         <label class="custom-control-label" for="l">Left</label>
                     </div>
                     <div class="custom-control custom-radio"style="width: 33.3%;">
                         <input type="radio" id="r" name="cur_show_ref" class="custom-control-input"
                             value="r" {{ $type=='n' ? 'disabled' : '' }}>
                         <label class="custom-control-label" for="r">Right</label>
                     </div>
                     <div class="custom-control custom-radio"style="width: 33.3%;">
                         <input type="radio" name="cur_show_ref" value="non" id="non"
                             class="custom-control-input" checked>
                         <label class="custom-control-label" for="non">NON</label>
                     </div>
                 </div>
             </div>

            

         </div>
         <div class="col-md-4"style="border-right: 1px solid #d6d4d4;">
             <h4 style="text-align: center;">Planned Bar</h4>
             <div class="custom-control custom-checkbox mb-3">
                 <input type="checkbox" class="custom-control-input"id="show_pl" name="show_pl">
                 <label class="custom-control-label" for="show_pl">Show Planned Bar</label>
             </div>
             <div class="form-group mb-3">
                 <label for="pl_type">Type</label>
                 <select class="form-control" id="pl_type" name="pl_type">
                     <option value="" disabled selected>please select</option>
                     <option value="SB">Single Bar</option>
                     <option value="M">Milestone</option>
                     
                 </select>
             </div>

             <div class="form-group mb-3 d-none">
                 <label for="pl_sd">Start Date</label>
                 <div style="display: flex;" class="flatpickr-container" id="sgd" data-input data-wrap>
                     <input type="date"style="background-color:#fff;" name="pl_sd" id="pl_sd"
                         class="date form-control" placeholder="Start Date"data-input>
                     <button type="button" class="btn btn-sm btn-outline-secondary" title="Clear" data-clear
                         style="border-color: rgb(200, 214, 238)">
                         ✖
                     </button>
                 </div>
             </div>
             <div class="form-group mb-3 d-none">
                 <label for="pl_fd">Finish Date</label>
                 <div style="display: flex;" class="flatpickr-container" id="fgd" data-input data-wrap>
                     <input required type="date"style="background-color:#fff;" name="pl_fd" id="pl_fd"
                         class="date form-control" placeholder="Finish Date"data-input>
                     <button type="button" class="btn btn-sm btn-outline-secondary" title="Clear" data-clear
                         style="border-color: rgb(200, 214, 238)">
                         ✖
                     </button>
                 </div>
             </div>
             <div class="form-group mb-3">
                 <label for="pl_color">Color</label>
                 <select class="form-control" id="pl_color" name="pl_color">
                     <option value="808080" style="background-color: #808080; color: white;">■ Gray</option>
                     <option selected value="00008B" style="background-color: #00008B; color: white;">■ Dark Blue</option>
                     <option value="FF0000" style="background-color: #FF0000; color: white;">■ Red</option>
                     <option value="008000" style="background-color: #008000; color: white;">■ Green</option>
                     <option value="000000" style="background-color: #000000; color: white;">■ Black</option>
                     <option value="89CFF0" style="background-color: #89CFF0;">■ Baby Blue</option>
                     <option value="FFFF00" style="background-color: #FFFF00;">■ Yellow</option>
                     <option value="8B4513" style="background-color: #8B4513; color: white;">■ Brown</option>
                     <option value="FFFFFF" style="background-color: #FFFFFF;">■ White</option>
                     <option value="FFFFFF00">■ Gap</option>
                 </select>
             </div>
             <hr>
             <div class="form-group mb-3">
                 <label for="pl_left_caption">Left Caption</label>
                 <textarea name="pl_left_caption" rows="1" id="pl_left_caption" class="form-control"></textarea>
             </div>
             <div class="form-group mb-3">
                 <label for="pl_right_caption">Right Caption</label>
                 <textarea name="pl_right_caption" rows="1" id="pl_right_caption" class="form-control"></textarea>
             </div>
             <div class="custom-control custom-checkbox mb-1">
                 <input type="checkbox" class="custom-control-input"id="pl_show_sd" name="pl_show_sd">
                 <label class="custom-control-label" for="pl_show_sd">Show Start Date</label>
             </div>
             <div class="custom-control custom-checkbox">
                 <input type="checkbox" class="custom-control-input"id="pl_show_fd" name="pl_show_fd">
                 <label class="custom-control-label" for="pl_show_fd">Show Finish Date</label>
             </div>

         </div>
         <div class="col-md-4">
             <h4 style="text-align: center;">Longest Path Bar</h4>
             <div class="custom-control custom-checkbox mb-3">
                 <input type="checkbox" class="custom-control-input"id="show_lp" name="show_lp">
                 <label class="custom-control-label" for="show_lp">Show Longest Path Bar</label>
             </div>
             <div class="form-group mb-3">
                 <label for="lp_sd">Start Date</label>
                 <div style="display: flex;" class="flatpickr-container" data-input data-wrap>
                     <input @if ($end_date == null) disabled @endif type="date"
                         style="background-color:#fff;" name="lp_sd" id="lp_sd" class="date form-control"
                         placeholder="Start Date" data-input value="{{ $start_date }}"
                         data-document-SD="{{ $start_date }}">
                     <button @if ($end_date == null) disabled @endif type="button"
                         class="btn btn-sm btn-outline-secondary" title="Clear" data-clear
                         style="border-color: rgb(200, 214, 238)">
                         ✖
                     </button>
                 </div>
             </div>
             <div class="form-group mb-3 @if ($end_date == null) d-none @endif">
                 <label for="lp_fd">Finish Date</label>
                 <div style="display: flex;" class="flatpickr-container" data-input data-wrap>
                     <input type="date"style="background-color:#fff;" name="lp_fd" id="lp_fd"
                         class="date form-control" placeholder="Finish Date"data-input value="{{ $end_date }}"
                         data-document-FD="{{ $end_date }}">
                     <button type="button" class="btn btn-sm btn-outline-secondary" title="Clear" data-clear
                         style="border-color: rgb(200, 214, 238)">
                         ✖
                     </button>
                 </div>
             </div>
         </div>

     </div>
 </form>
