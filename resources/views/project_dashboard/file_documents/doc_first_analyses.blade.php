@extends('project_dashboard.layout.app')
@section('title', 'Project Home - First Analyses')
@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .date {
            background-color: #fff !important;
        }
    </style>
    <h4 style="cursor:pointer;" class="page-title">
        <a href="{{ route('switch.folder', $doc->file->folder->id) }}">{{ $doc->file->folder->name }}</a>
        <span style="position: relative; top: 3px;" class="fe fe-23 fe-chevrons-right"></span>
        <a href="{{ route('project.file-documents.index', $doc->file->slug) }}">{{ $doc->file->name }}</a>
        <label id="chevronIcon" style="cursor: pointer;" ><span id="chevronIcon2" style="position: relative; top: 3px;" class="fe fe-23 fe-chevrons-right"></span>
        Details of "{{ $doc->document ? $doc->document->subject : $doc->note->subject }}"</label>
        
    </h4>
    @if (session('error'))
        <div id="errorAlert" class="alert alert-danger"
            style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:brown;border-radius: 20px; color:beige;">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div id="successAlert"
            class="alert alert-success"style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:green;border-radius: 20px; color:beige;">
            {{ session('success') }}
        </div>
    @endif
    <div class="card shadow mb-4 d-none"id="detailsCard">
        <div class="card-body">
            <div class="row">
                @if ($doc->document)
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="Type">Type</label>
                                    <input type="text" id="Type" class="form-control" placeholder="Type" disabled
                                        value="{{ $doc->document->docType->name }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-3">
                                    <label for="Subject">Subject</label>
                                    <input type="text"id="Subject" class="form-control" placeholder="Subject" disabled
                                        value="{{ $doc->document->subject }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="Reference">Reference</label>
                                    <input type="text"id="Reference" class="form-control" placeholder="Reference"
                                        disabled value="{{ $doc->document->reference }}">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="Date">Date</label>
                                    <input type="text" id="Date" class="form-control" placeholder="Date" disabled
                                        value="{{ $doc->document->start_date ? date('d-M-Y', strtotime($doc->document->start_date)) : '' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="Return_Date">Return Date</label>
                                    <input type="text"id="Return_Date" class="form-control" placeholder="Return Date"
                                        disabled
                                        value="{{ $doc->document->end_date ? date('d-M-Y', strtotime($doc->document->end_date)) : '' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="Status">Status</label>
                                    <input type="text"id="Status" class="form-control" placeholder="Status" disabled
                                        value="{{ $doc->document->status }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-3">
                                    <label for="Revision">Revision</label>
                                    <input type="text"id="Revision" class="form-control" placeholder="Revision" disabled
                                        value="{{ $doc->document->revision }}">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-3">
                                    <label for="Thread">Thread</label>
                                    <select class="form-control select2-multi" disabled id="multi-select2" multiple
                                        style="height: calc(1.5em + 0.75rem + 2px) !important;">

                                        @if ($doc->document->threads)
                                            @foreach (json_decode($doc->document->threads, true) as $thread2)
                                                <option value="{{ $thread2 }}"selected>
                                                    {{ $thread2 }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group mb-3">
                                    <label for="Note">Note</label>
                                    <input type="text" id="Note" class="form-control" placeholder="Note" disabled
                                        value="{{ $doc->document->notes }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-3">
                                    <button type="button" class="btn mt-4 btn-success"
                                        onclick="window.location.href='/project/file-docs/<?php echo $doc->id; ?>/doc/<?php echo $doc->document->slug; ?>/edit'">Edit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="Type">Type</label>
                                    <input type="text" id="Type" class="form-control" placeholder="Type" disabled
                                        value="Note/Activity">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-3">
                                    <label for="Subject">Subject</label>
                                    <input type="text"id="Subject" class="form-control" placeholder="Subject" disabled
                                        value="{{ $doc->note->subject }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="Date">Date</label>
                                    <input type="text" id="Date" class="form-control" placeholder="Date"
                                        disabled
                                        value="{{ $doc->note->start_date ? date('d-M-Y', strtotime($doc->note->start_date)) : '' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="Return_Date">Return Date</label>
                                    <input type="text"id="Return_Date" class="form-control" placeholder="Return Date"
                                        disabled
                                        value="{{ $doc->note->end_date ? date('d-M-Y', strtotime($doc->note->end_date)) : '' }}">
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-11">
                                <div class="form-group mb-3">
                                    <label for="Note">Note</label>
                                    <input type="text" id="Note" class="form-control" placeholder="Note"
                                        disabled value="{{ $doc->note->notes }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-3">
                                    <button type="button" class="btn mt-4 btn-success"
                                        onclick="window.location.href='/project/file-docs/<?php echo $doc->id; ?>/doc/<?php echo $doc->note->slug; ?>/edit'">Edit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form id="formNarrative" method="post"
                        action="{{ route('project.file-document-first-analyses.store', $doc->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <label for="owner" style="margin-bottom: 0.1rem !important">Narrative</label>
                                @if ($doc->document)
                                    <div style="display: flex;cursor: pointer;">
                                        <img id="ai_image" src="{{ asset('/dashboard/assets/selected_images/ai.png') }}"
                                            width="25"height="25" title="AI"
                                            style="position: relative; top: 9px; margin-right:5px;">
                                        <a href="{{ route('project.file-documents.ocr_layer_with_path') }}"
                                            target="_blank"><img
                                                src="{{ asset('/dashboard/assets/selected_images/ocr.png') }}"
                                                width="30" title="OCR" style="margin-bottom: -25px;"></a>
                                        <a href="{{ asset($doc->document->storageFile->path) }}" target="blank_"> <img
                                                src="{{ asset('/dashboard/assets/selected_images/eye3.png') }}"
                                                width="50" title="PDF" style="margin-bottom: -10px;"></a>
                                    </div>
                                @endif

                            </div>

                            <div id="editor" style="min-height:250px;">
                                {!! $doc->narrative !!}
                            </div>
                            <input type="hidden" name="narrative" id="narrative">
                        </div>
                        <div class="form-group mb-3">
                            <label for="Note1">Note 1</label>
                            <textarea name="notes1" rows="5" id="Note1" class="form-control" placeholder="First Note">{{ old('notes1', $doc->notes1) }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="Note2">Note 2</label>
                            <textarea name="notes2" rows="5" id="Note2" class="form-control" placeholder="Second Note">{{ old('notes2', $doc->notes2) }}</textarea>
                        </div>
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-1">
                                <div class="form-group mb-3">
                                    <label for="sn">SN.</label>
                                    <input type="number" name="sn" id="sn" class="form-control"
                                        placeholder="Enter Serial Number" value="{{ old('sn', $doc->sn) }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="Revision">Tags</label>
                                    <select class="form-control select2-multi" id="multi-select2" name="tags[]"multiple
                                        @if ($doc->document_id == null) disabled @endif>
                                        @foreach ($tags as $tag)
                                            <option
                                                value="{{ $tag->id }}"{{ count($doc->tags) != 0 && in_array($tag->id, $doc->tags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $tag->name }} ({{ $tag->sub_clause }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row"style="margin-left: 50px;">

                                    <div class="custom-control custom-checkbox"
                                        style="margin-right: 20px;margin-top: 6.5%;">

                                        <input type="checkbox" class="custom-control-input" name="forClaim"
                                            id="forClaim" @if ($doc->forClaim == '1') checked @endif>
                                        <label class="custom-control-label" for="forClaim">For Claim (c)</label>
                                    </div>
                                    <div
                                        class="custom-control custom-checkbox"style="margin-right: 20px;margin-top: 6.5%;">

                                        <input type="checkbox" class="custom-control-input"
                                            name="forLetter"id="forLetter" @if ($doc->forLetter == '1') checked @endif
                                            @if ($doc->document_id == null) disabled @endif>
                                        <label class="custom-control-label" for="forLetter">For Notice (N)</label>
                                    </div>
                                    <div
                                        class="custom-control custom-checkbox"style="margin-right: 20px;margin-top: 6.5%;">

                                        <input type="checkbox" class="custom-control-input" name="forChart"id="forChart"
                                            @if ($doc->forChart == '1') checked @endif>
                                        <label class="custom-control-label" for="forChart">For Gantt Chart (G)</label>
                                    </div>

                                </div>
                            </div>


                        </div>
                        <input type="hidden" name="action" id="formAction" value="save">
                        <div class="text-right" style="margin-top: 10px;">
                            <button type="submit"
                                class="btn mb-2 btn-outline-success"onclick="document.getElementById('formAction').value='save'">Save</button>
                            <button type="submit" class="btn mb-2 btn-outline-primary"
                                onclick="document.getElementById('formAction').value='update'">Update</button>
                            <button type="button" class="btn mb-2 btn-outline-secondary"
                                onclick="window.location.href='/project/file/<?php echo $doc->file->slug; ?>/documents'">Back</button>
                        </div>
                    </form>
                </div> <!-- /.col -->

            </div>
        </div>
    </div>
    <div class="modal fade" id="insertImageModal" tabindex="-1" role="dialog" aria-labelledby="insertImageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertImageModalLabel">Insert Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">




                    <div class="form-group mb-3">
                        <label for="customFile">Upload Image:</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input"accept="image/*" id="uploadImageInput">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Image URL:</label>
                        <input type="text" id="imageUrlInput" placeholder="Enter image URL"class="form-control">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Description:</label>
                        <input type="text" id="imageAltInput" placeholder="Describe the image"class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="insertImageBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="pagesFromToModal" tabindex="-1" role="dialog" aria-labelledby="pagesFromToModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pagesFromToModalLabel">PDF Pages</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="from">From</label>
                        <input type="number" name="from" id="from" class="form-control" placeholder="From"
                            value="1" min="1" oninput="this.value = Math.max(1, this.value)">
                    </div>

                    <div class="form-group">
                        <label for="to">To</label>
                        <input type="number" name="to" id="to" class="form-control" placeholder="To"
                            value="1" min="1" oninput="this.value = Math.max(1, this.value)">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="pagesFromTo">Open</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("chevronIcon").addEventListener("click", function() {
            document.getElementById("detailsCard").classList.toggle("d-none");
            let icon = document.getElementById("chevronIcon2");
            icon.classList.toggle("fe-chevrons-right");
            icon.classList.toggle("fe-chevrons-down");
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#ai_image').on('click', function() {
                $.ajax({
                    url: '/project/checkDoc_aiLayerUsed', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        id: {{ $doc->id }}

                    },
                    success: function(response) {
                        if (response.result == '1') {
                            alert('Someone Use AI Layer for this document');
                        } else {
                            $('#pagesFromToModal').modal('show'); // Show the modal

                        }


                    },
                    error: function() {
                        alert('Failed to assign document. Please try again.');
                    }
                });
            });
            $('#pagesFromTo').click(function() {
                let from = $('#from').val();
                let to = $('#to').val();

                if (parseInt(to) < parseInt(from)) {
                    alert('"To" page number should be greater than or equal to "From" page number.');
                    return;
                }

                $.ajax({
                    url: '/project/create_ai_pdf', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        from: from,
                        to: to,

                    },
                    success: function(response) {

                        $('#pagesFromToModal').modal('hide');
                        window.open('/project/AI-layer/' + response.ai_zip_file, '_blank');
                    },
                    error: function() {
                        alert('Failed to assign document. Please try again.');
                    }
                });
            });
            $('.dropdown-toggle').dropdown();
            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
        });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            flatpickr(".date", {
                enableTime: false,
                dateFormat: "Y-m-d", // Format: YYYY-MM-DD
                altInput: true,
                altFormat: "d.M.Y",
            });

        });
    </script>
@endpush
