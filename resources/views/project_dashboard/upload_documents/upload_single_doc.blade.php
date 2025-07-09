@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Upload Single Document')
@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .date {
            background-color: #fff !important;
        }
    </style>
    <h2 class="page-title">Upload a New Document</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('project.upload_single_doc.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="doc_id" name="doc_id" required value="">
                        <div class="custom-control custom-checkbox" style="margin-bottom: 5px;">
                            <input type="checkbox" class="custom-control-input"id="use_ai" checked>
                            <label class="custom-control-label" for="use_ai" style="padding-top: 3px;">Use AI</label>
                        </div>
                        <div class="form-group" style="display: flex; align-items: center;">
                            <hr style="flex: 1; margin: 0;">
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <label for="file">Document <span style="color: red">*</span></label>
                                        {{-- <span class="fe fe-24 fe-eye d-none" id="viewPdf" title="View PDF"
                                            style="cursor: pointer;"></span> --}}
                                        <div style="display: flex;cursor: pointer;">

                                            <img class="d-none" id="ocr_image"
                                                src="{{ asset('/dashboard/assets/selected_images/ocr.png') }}"
                                                width="30"height="30" title="OCR" style="margin-bottom: -20px;">

                                            <img class="d-none"
                                                src="{{ asset('/dashboard/assets/selected_images/eye3.png') }}"
                                                width="50"
                                                style="margin-bottom: -20px;position: relative; top: -10px;"id="viewPdf"
                                                title="PDF">
                                        </div>

                                    </div>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="customFile">
                                        <label class="custom-file-label" for="customFile">Choose File</label>
                                    </div>
                                    <div class="mt-2">
                                        <div class="progress d-none">
                                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Name Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="type">Type <span style="color: red">*</span></label>
                                    <select class="form-control" id="type" required name="doc_type">
                                        <option value="" selected disabled>please select</option>
                                        @foreach ($documents_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="owner">Analyzed By <span style="color: red">*</span></label>
                                    <select class="form-control" id="owner" required name="user_id">
                                        <option value="" selected disabled>please select</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="form-group mb-3">
                            <label for="Subject">Subject <span style="color: red">*</span></label>
                            <input type="text" name="subject"required id="Subject" class="form-control"
                                placeholder="Subject"value="{{ old('subject') }}">
                        </div>
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date">srart Date <span style="color: red">*</span></label>
                                    <input required type="date"style="background-color:#fff;" name="start_date"
                                        id="start_date" class="form-control date"
                                        placeholder="Start Date"value="{{ old('start_date') }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-6" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="end_date">End Date</label>
                                    <input type="date"style="background-color:#fff;" name="end_date" id="end_date"
                                        class="form-control date" placeholder="End Date"value="{{ old('end_date') }}">
                                </div>
                            </div>


                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"id="With-Return">
                            <label class="custom-control-label" for="With-Return">This Document With Return</label>
                        </div>
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="from_id">From</label>
                                    <select class="form-control" id="from_id" name="from_id">
                                        <option selected disabled>please select</option>
                                        @foreach ($stake_holders as $stake_holder)
                                            <option value="{{ $stake_holder->id }}">{{ $stake_holder->narrative }} -
                                                {{ $stake_holder->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="to_id">To</label>
                                    <select class="form-control" id="to_id" name="to_id">
                                        <option selected disabled>please select</option>
                                        @foreach ($stake_holders as $stake_holder)
                                            <option value="{{ $stake_holder->id }}">{{ $stake_holder->narrative }} -
                                                {{ $stake_holder->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                        </div>
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="reference">Reference <span style="color: red">*</span></label>
                                    <input type="text" name="reference" id="reference" class="form-control"
                                        placeholder="Reference" required value="{{ old('reference') }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="Revision">Revision</label>
                                    <input type="text" name="revision" id="Revision" class="form-control"
                                        placeholder="Revision"value="{{ old('revision') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="status">Status</label>

                                    <input type="text" name="status" id="status" class="form-control"
                                        placeholder="Status"value="{{ old('status') }}">
                                </div>
                            </div>
                        </div>
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
                            <textarea name="notes" rows="5" id="Note" class="form-control" placeholder="Note">{{ old('notes') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="multi-select2">Thread</label>
                            <select class="form-control select2-multi" id="multi-select2" name="threads[]"multiple>
                                @foreach ($threads as $thread)
                                    <option value="{{ $thread }}">{{ $thread }}</option>
                                @endforeach
                            </select>
                        </div> <!-- form-group -->
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"id="analyzed" name="analyzed">
                            <label class="custom-control-label" for="analyzed">Notify for Analysis</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"id="analysis_complete"
                                name="analysis_complete">
                            <label class="custom-control-label" for="analysis_complete">Analysis Complete</label>
                        </div>
                        <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary"
                            style="margin-top: 10px;">Create</button>
                    </form>
                </div> <!-- /.col -->

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->

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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#customFile').on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                let use_ai = $("#use_ai").prop("checked") ? 1 : 0
                const formData = new FormData();
                formData.append('file', file);
                formData.append('use_ai', use_ai);

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
                                $viewPdf.off('click').on('click', function() {
                                    window.open('/' + file.path, '_blank');
                                });

                                const $ocr_image = $('#ocr_image');
                                $ocr_image.removeClass('d-none');
                                $ocr_image.off('click').on('click', function() {
                                    window.open('/project/ocr_layer', '_blank');
                                });
                            }
                            console.log(response);
                            if (response.type_id) $('#type').val(response.type_id).trigger(
                                'change');
                            if (response.sender_id) $('#from_id').val(response.sender_id)
                                .trigger('change');
                            if (response.receiver_id) $('#to_id').val(response.receiver_id)
                                .trigger('change');
                            if (response.start_date) {
                                // Get the flatpickr instance and set the date properly
                                let fp = document.querySelector('#start_date')._flatpickr;
                                if (fp) {
                                    fp.setDate(response.start_date,
                                        true); // true = trigger change events
                                }
                            }
                            if (response.reference) $('#reference').val(response.reference);
                            if (response.subject) $('#Subject').val(response.subject);

                            if (response.threads && response.threads.length > 0) {
                                // Ensure options are available before setting the value
                                response.threads.forEach(function(thread) {
                                    if ($('#multi-select2 option[value="' + thread +
                                            '"]').length === 0) {
                                        $('#multi-select2').append(new Option(thread,
                                            thread));
                                    }
                                });

                                $('#multi-select2').val(response.threads).trigger('change');
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

            //const typeSelect = document.getElementById("type");
            //const ownerSelect = document.getElementById("owner");
            //const fileInput = document.getElementById("customFile");

            //function checkInputs() {
            //    if (typeSelect.value !== "" && ownerSelect.value !== "") {
            //        fileInput.removeAttribute("disabled");
            //    } else {
            //        fileInput.setAttribute("disabled", true);
            //    }
            //}

            // typeSelect.addEventListener("change", checkInputs);
            // ownerSelect.addEventListener("change", checkInputs);






        });
    </script>
@endpush
