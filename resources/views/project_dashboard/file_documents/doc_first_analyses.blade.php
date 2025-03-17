@extends('project_dashboard.layout.app')
@section('title', 'Project Home - First Analyses')
@section('content')
    <h2 id="toggleTitle" style="cursor:pointer;" class="page-title"><span id="chevronIcon" class="fe fe-24 fe-chevrons-right"></span>Details of
        "{{ $doc->document->subject }}"</h2>
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
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="Type">Type</label>
                                <input type="text" id="Type" class="form-control"
                                    placeholder="Type" disabled value="{{ $doc->document->docType->name }}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group mb-3">
                                <label for="Subject">Subject</label>
                                <input type="text"id="Subject" class="form-control"
                                    placeholder="Subject" disabled value="{{ $doc->document->subject }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="Reference">Reference</label>
                                <input type="text"id="Reference" class="form-control"
                                    placeholder="Reference" disabled value="{{ $doc->document->reference }}">
                            </div>
                        </div>
                    
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label for="Date">Date</label>
                                <input type="text" id="Date" class="form-control"
                                    placeholder="Date" disabled value="{{$doc->document->start_date? date('d-M-Y', strtotime( $doc->document->start_date)):'' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label for="Return_Date">Return Date</label>
                                <input type="text"id="Return_Date" class="form-control"
                                    placeholder="Return Date" disabled value="{{ $doc->document->end_date? date('d-M-Y', strtotime( $doc->document->end_date)):'' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label for="Status">Status</label>
                                <input type="text"id="Status" class="form-control"
                                    placeholder="Status" disabled value="{{ $doc->document->status }}">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mb-3">
                                <label for="Revision">Revision</label>
                                <input type="text"id="Revision" class="form-control"
                                    placeholder="Revision" disabled value="{{ $doc->document->revision }}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group mb-3">
                                <label for="Thread">Thread</label>
                                <select class="form-control select2-multi" disabled  id="multi-select2" multiple style="height: calc(1.5em + 0.75rem + 2px) !important;">
            
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
                                <input type="text" id="Note" class="form-control"
                                    placeholder="Note" disabled value="{{$doc->document->notes}}">
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
            </div>
        </div>
    </div>
    <div class="card shadow mb-4" >
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form id="formNarrative" method="post"
                        action="{{ route('project.file-document-first-analyses.store', $doc->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="owner">Narrative</label>
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
                                        placeholder="Enter Serial Number" required value="{{ old('sn', $doc->sn) }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="Revision">Tags</label>
                                    <select class="form-control select2-multi" id="multi-select2" name="tags[]"multiple>
                                        @foreach ($tags as $tag)
                                            <option
                                                value="{{ $tag->id }}"{{ count($doc->tags) != 0 && in_array($tag->id, $doc->tags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $tag->name }} ({{ $tag->sub_clause }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row"style="margin-left: 50px;"">
                                               
                                    <div class="custom-control custom-checkbox" style="margin-right: 20px;margin-top: 6.5%;">
                                                   
                                                    <input type="checkbox" class="custom-control-input" name="forClaim"id="
                                        forClaim"@if ($doc->forClaim == '1') checked @endif>
                                        <label class="custom-control-label" for="forClaim">For Claim (c)</label>
                                    </div>
                                    <div class="custom-control custom-checkbox"style="margin-right: 20px;margin-top: 6.5%;">

                                        <input type="checkbox" class="custom-control-input" name="forLetter"id="forLetter"
                                            @if ($doc->forLetter == '1') checked @endif>
                                        <label class="custom-control-label" for="forLetter">For Notice (N)</label>
                                    </div>
                                    <div class="custom-control custom-checkbox"style="margin-right: 20px;margin-top: 6.5%;">

                                        <input type="checkbox" class="custom-control-input" name="forChart"id="forChart"
                                            @if ($doc->forChart == '1') checked @endif>
                                        <label class="custom-control-label" for="forChart">For Timeline (T)</label>
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
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("toggleTitle").addEventListener("click", function() {
            document.getElementById("detailsCard").classList.toggle("d-none");
            let icon = document.getElementById("chevronIcon");
            icon.classList.toggle("fe-chevrons-right");
            icon.classList.toggle("fe-chevrons-down");
        });
    </script>
    <script>
        $(document).ready(function() {

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
            });

        });
    </script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
@endpush
