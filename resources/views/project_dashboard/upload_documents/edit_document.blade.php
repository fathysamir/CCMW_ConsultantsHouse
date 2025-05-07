@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Edit Document')
@section('content')
<style>
    .date{
        background-color:#fff !important;
    }
</style>
    <h2 class="page-title">Edit Document</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <form method="post" action="{{ route('project.update-document', $document->slug) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" id="doc_id" name="doc_id" required value="{{ $document->storage_file_id }}">
                        <div class="row">
                            <!-- Type Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="type">Type <span style="color: red">*</span></label>
                                    <select class="form-control" id="type" required name="doc_type">
                                        <option value="" disabled>please select</option>
                                        @foreach ($documents_types as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $document->doc_type_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Analyzed By Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="owner">Analyzed By <span style="color: red">*</span></label>
                                    <select class="form-control" id="owner" required name="user_id">
                                        <option value="" disabled>please select</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ $document->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Document Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <label for="file">Document <span style="color: red">*</span></label>
                                        <!-- Conditionally show the "View PDF" icon if the old document exists -->
                                        @if ($document->storage_file_id)
                                            <span class="fe fe-24 fe-eye" id="viewPdf" title="View PDF"
                                                style="cursor: pointer;"
                                                data-file-path="{{ $document->storageFile->path }}"></span>
                                        @else
                                            <span class="fe fe-24 fe-eye d-none" id="viewPdf" title="View PDF"
                                                style="cursor: pointer;"></span>
                                        @endif
                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="customFile">
                                        <label class="custom-file-label"
                                            for="customFile">{{ $document->storageFile->file_name ?? 'Choose File' }}</label>
                                    </div>
                                    <div class="mt-2">
                                        <div class="progress d-none">
                                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="Subject">Subject <span style="color: red">*</span></label>
                            <input type="text" name="subject" required id="Subject" class="form-control"
                                placeholder="Subject" value="{{ $document->subject }}">
                        </div>
                        <div class="row">
                            <!-- Start Date Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date">Start Date <span style="color: red">*</span></label>
                                    <input required type="date" style="background-color:#fff;" name="start_date"
                                        id="start_date" class="form-control date" placeholder="Start Date"
                                        value="{{ $document->start_date }}">
                                </div>
                            </div>

                            <!-- End Date Input -->
                            <div class="col-md-6" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="end_date">End Date</label>
                                    <input type="date" style="background-color:#fff;" name="end_date" id="end_date"
                                        class="form-control date" placeholder="End Date" value="{{ $document->end_date }}">
                                </div>
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="With-Return"
                                {{ $document->end_date ? 'checked' : '' }}>
                            <label class="custom-control-label" for="With-Return">This Document With Return</label>
                        </div>
                        <div class="row">
                            <!-- From Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="from_id">From</label>
                                    <select class="form-control" id="from_id" name="from_id">
                                        <option disabled>please select</option>
                                        @foreach ($stake_holders as $stake_holder)
                                            <option value="{{ $stake_holder->id }}"
                                                {{ $document->from_id == $stake_holder->id ? 'selected' : '' }}>
                                                {{ $stake_holder->narrative }} - {{ $stake_holder->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- To Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="to_id">To</label>
                                    <select class="form-control" id="to_id" name="to_id">
                                        <option disabled>please select</option>
                                        @foreach ($stake_holders as $stake_holder)
                                            <option value="{{ $stake_holder->id }}"
                                                {{ $document->to_id == $stake_holder->id ? 'selected' : '' }}>
                                                {{ $stake_holder->narrative }} - {{ $stake_holder->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Reference Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="reference">Reference <span style="color: red">*</span></label>
                                    <input type="text" name="reference" id="reference" class="form-control"
                                        placeholder="Reference" required value="{{ $document->reference }}">
                                </div>
                            </div>

                            <!-- Revision Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="Revision">Revision</label>
                                    <input type="text" name="revision" id="Revision" class="form-control"
                                        placeholder="Revision" value="{{ $document->revision }}">
                                </div>
                            </div>

                            <!-- Status Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="status">Status</label>
                                    <input type="text" name="status" id="status" class="form-control"
                                        placeholder="Status" value="{{ $document->status }}">
                                </div>
                            </div>
                        </div>

                        <!-- Start Date Input -->

                        <div class="form-group mb-3">
                            <label>Assign Document To File</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-control" id="folder_id">
                                        <option value="" disabled selected>Select Folder</option>
                                        @foreach ($folders as $key => $name)
                                            <option value="{{ $key }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 d-none files_">
                                    <select class="form-control" id="newFile" name="file_id">
                                        <option value="" disabled selected>Select File</option>
        
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group mb-3">
                            <label for="Note">Note</label>
                            <textarea name="notes" rows="5" id="Note" class="form-control" placeholder="Note">{{ $document->notes }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="multi-select2">Thread</label>
                            <select class="form-control select2-multi" id="multi-select2" name="threads[]" multiple>
                                @foreach ($threads as $thread)
                                    <option
                                        value="{{ $thread }}"{{ $document->threads && in_array($thread, json_decode($document->threads, true)) ? 'selected' : '' }}>
                                        {{ $thread }}</option>
                                @endforeach
                                @if ($document->threads)
                                    @foreach (json_decode($document->threads, true) as $thread2)
                                        @if (!in_array($thread2, $threads->toArray()))
                                            <option value="{{ $thread2 }}"selected>
                                                {{ $thread2 }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="analyzed" name="analyzed"
                                {{ $document->analyzed ? 'checked' : '' }}>
                            <label class="custom-control-label" for="analyzed">Notify for Analysis</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"id="analysis_complete"
                                name="analysis_complete"{{ $document->analysis_complete ? 'checked' : '' }}>
                            <label class="custom-control-label" for="analysis_complete">Analysis Complete</label>
                        </div>
                        <input type="hidden" name="action" id="formAction" value="save">
                        <div class="text-right" style="margin-top: 10px;">
                            <button type="submit"
                                class="btn mb-2 btn-outline-success"onclick="document.getElementById('formAction').value='save'">Save</button>
                            <button type="submit" class="btn mb-2 btn-outline-primary"
                                onclick="document.getElementById('formAction').value='update'">Update</button>
                            <button type="button"
                                class="btn mb-2 btn-outline-secondary"@if (session()->has('current_view') && session('current_view') == 'file_doc') onclick="window.location.href='/project/file-document-first-analyses/<?php echo session('current_file_doc'); ?>'" @elseif(session()->has('current_view') && session('current_view') == 'file') onclick="window.location.href='/project/file/<?php echo session('current_file2'); ?>/documents'" @else 
                                onclick="window.location.href='/project/all-documents'" @endif>Back</button>
                        </div>
                    </form>
                </div> <!-- /.col -->
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#folder_id').change(function() {
                let folderId = $(this).val();

                if (!folderId) return; // Stop if no folder is selected

                $.ajax({
                    url: '/project/folder/get-files/' +
                    folderId, // Adjust the route to your API endpoint
                    type: 'GET',
                    success: function(response) {
                        let fileDropdown = $('#newFile');
                        fileDropdown.empty().append(
                            '<option value="" disabled selected>Select File</option>');

                        if (response.files.length > 0) {
                            $.each(response.files, function(index, file) {
                                fileDropdown.append(
                                    `<option value="${file.id}">${file.name}</option>`
                                );
                            });

                            fileDropdown.closest('.files_').removeClass(
                                'd-none'); // Show file dropdown
                        } else {
                            fileDropdown.closest('.files_').addClass(
                                'd-none'); // Hide if no files
                        }
                    },
                    error: function() {
                        alert('Failed to fetch files. Please try again.');
                    }
                });
            });
            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
            const viewPdfIcon = document.getElementById('viewPdf');

            if (viewPdfIcon) {
                viewPdfIcon.addEventListener('click', function() {
                    const filePath = this.getAttribute('data-file-path');
                    if (filePath) {
                        window.open('/' + filePath, '_blank'); // Open the file in a new tab
                    }
                });
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#customFile').on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                // Show progress bar
                const $progress = $('.progress');
                const $progressBar = $progress.find('.progress-bar');
                $progress.removeClass('d-none');

                $.ajax({
                    url: '/upload-single-file',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percentComplete = Math.round((e.loaded / e
                                    .total) * 100);
                                $progressBar.css('width', percentComplete + '%');
                                $progressBar.text(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        if (response.success) {
                            const file = response.file;

                            // Update file label
                            $('.custom-file-label').text(file.file_name);
                            $('#doc_id').val(file.id);
                            // Show PDF viewer icon if it's a PDF
                            if (file.file_type === 'application/pdf') {
                                const $viewPdf = $('#viewPdf');
                                $viewPdf.removeClass('d-none');
                                $viewPdf.attr('data-file-path', file
                                        .path) // Set or add the attribute
                                    .off('click')
                                    .on('click', function() {
                                        window.open('/' + $(this).attr('data-file-path'),
                                            '_blank');
                                    });
                            }

                            // Show success message
                            //alert('File uploaded successfully!');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', error);
                        alert('Failed to upload file');
                    },
                    complete: function() {
                        // Hide progress bar
                        setTimeout(function() {
                            $progress.addClass('d-none');
                            $progressBar.css('width', '0%');
                        }, 1000);
                    }
                });
            });
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

            const withReturnCheckbox = document.getElementById("With-Return");
            const endDateDiv = document.getElementById("end_date").closest(".col-md-6");

            // Function to toggle visibility
            function toggleEndDate() {
                if (withReturnCheckbox.checked) {
                    endDateDiv.style.display = "block";
                } else {
                    endDateDiv.style.display = "none";
                }
            }

            // Add event listener to the checkbox
            withReturnCheckbox.addEventListener("change", toggleEndDate);

            // Initialize on page load (in case of form repopulation)
            toggleEndDate();

            const typeSelect = document.getElementById("type");
            const ownerSelect = document.getElementById("owner");
            const fileInput = document.getElementById("customFile");

            function checkInputs() {
                if (typeSelect.value !== "" && ownerSelect.value !== "") {
                    fileInput.removeAttribute("disabled");
                } else {
                    fileInput.setAttribute("disabled", true);
                }
            }

            typeSelect.addEventListener("change", checkInputs);
            ownerSelect.addEventListener("change", checkInputs);
        });
    </script>
@endpush
