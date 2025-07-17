@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Attachment')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    .date{
        background-color:#fff !important;
    }
</style>
@php
    $arr=[
        '1'=>'Synopsis',
        '2'=>'Contractual Position',
        '3'=>'Cause-and-Effect Analysis'
    ]
@endphp
    <h4 id="toggleTitle" style="cursor:pointer;" class="page-title"><a href="{{ route('switch.folder', $file_attachment->file->folder->id) }}">{{ $file_attachment->file->folder->name }}</a><span
            class="fe fe-24 fe-chevrons-right"></span><a href="{{ route('project.file-attachments.index', ['id' => $file_attachment->file->slug, 'type' => $file_attachment->section ]) }}">{{ $file_attachment->file->name }} - {{ $arr[$file_attachment->section] }}</a><span class="fe fe-24 fe-chevrons-right"></span>Details of Attachment
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
    <div class="card shadow mb-4" >
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form id="formNarrative" method="post"
                        action="{{ route('project.update_attachment.update', $file_attachment->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <div>
                                <label for="owner">Narrative</label>
                            </div>
                            
                            <div id="editor" style="min-height:250px;">
                                {!! $file_attachment->narrative !!}
                            </div>
                            <input type="hidden" name="narrative" id="narrative">
                        </div>
                        
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label for="sn">Order.</label>
                                    <input type="number" name="order" id="order" class="form-control"
                                        placeholder="Enter Serial Number" value="{{ old('order', $file_attachment->order) }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                           
                            <div class="col-md-5">
                                <div class="row"style="margin-left: 50px;">
                                               
                                    <div class="custom-control custom-checkbox" style="margin-right: 20px;margin-top: 6.5%;">
                                                   
                                                    <input type="checkbox" class="custom-control-input" name="forClaim" id="forClaim" @if ($file_attachment->forClaim == '1') checked @endif>
                                        <label class="custom-control-label" for="forClaim">For Claim (c)</label>
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
                                onclick="window.location.href='/project/file/<?php echo $file_attachment->file->slug; ?>/attachments/<?php echo $file_attachment->section; ?>'">Back</button>
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
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
   
    <script>
        $(document).ready(function() {
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
