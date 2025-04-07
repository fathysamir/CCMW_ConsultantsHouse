@foreach ($uploadedFiles as $key => $id)
    <div class="mb-2 document-container" data-id="{{ $id }}"
        style="border: 1px solid rgba(0, 0, 0, .125);border-radius: .25rem;    box-shadow: 0 5px 15px 0 rgba(0, 0, 0, .15);">
        <div class="custom-control custom-checkbox mt-1 ml-1 mb-1">
            <input type="checkbox" class="custom-control-input docName_checkBox"id="doc_name{{ $id }}">
            <label class="custom-control-label doc_name" for="doc_name{{ $id }}">{{ $key }}</label>
        </div>
        <div class="form-group" style="display: flex; align-items: center;">
            <hr style="flex: 1; margin: 0;">
        </div>
        <div class="row mb-2">
            <div class="col-md-7" style="border-right: 1px solid rgb(151, 149, 149);">
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 70px;font-weight: bold;">ــ Subject : </span>
                    <span class="doc_subject" style="font-weight: bold; font-style: italic;"></span>
                </div>
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 40px;font-weight: bold;">ــ Ref : </span>
                    <span class="doc_ref" style="font-weight: bold; font-style: italic;"></span>
                </div>
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 50px;font-weight: bold;">ــ Type : </span>
                    <span class="doc_type" style="font-weight: bold; font-style: italic;"></span>
                </div>
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 110px;font-weight: bold;">ــ Assign To File : </span>
                    <span class="doc_assign_file" style="font-weight: bold; font-style: italic;"></span>
                </div>
                
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 50px;font-weight: bold;">ــ Note : </span>
                    <span class="document_note" style="font-weight: bold; font-style: italic;"></span>
                </div>
            </div>
            <div class="col-md-5">
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 50px;font-weight: bold;">ــ Date : </span>
                    <span class="doc_date" style="font-weight: bold; font-style: italic;"></span>
                </div>
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 102px;font-weight: bold;">ــ Analyzed By : </span>
                    <span class="document_analyzedBy" style="font-weight: bold; font-style: italic;"></span>
                </div>
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 53px;font-weight: bold;">ــ From : </span>
                    <span class="document_from" style="font-weight: bold; font-style: italic;"></span>
                </div>
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 38px;font-weight: bold;">ــ To : </span>
                    <span class="document_to" style="font-weight: bold; font-style: italic;"></span>
                </div>
                <div class="ml-2" style="display: flex;">
                    <span style="display: inline-block; min-width: 45px;font-weight: bold;">ــ Rev : </span>
                    <span class="document_rev" style="font-weight: bold; font-style: italic;"></span>
                </div>
                
                
            </div>

        </div>

        <input hidden name="doc[{{ $id }}][doc_id]" class="doc_id_value" value="{{ $id }}">
        <input hidden name="doc[{{ $id }}][reference]" class="reference_value" value="">
        <input hidden name="doc[{{ $id }}][date]" class="date_value" value="">
        <input hidden name="doc[{{ $id }}][subject]" class="subject_value" value="">
        <input hidden name="doc[{{ $id }}][type]" class="type_value" value="">
        <input hidden name="doc[{{ $id }}][assign_to_file_id]" class="assign_to_file_id_value"
            value="">
        <input hidden name="doc[{{ $id }}][analyzed_by]" class="analyzed_by_value" value="">
        <input hidden name="doc[{{ $id }}][from]" class="from_value" value="">
        <input hidden name="doc[{{ $id }}][to]" class="to_value" value="">
        <input hidden name="doc[{{ $id }}][revision]" class="revision_value" value="">
        <input hidden name="doc[{{ $id }}][notes]" class="notes_value" value="">
    </div>
@endforeach
