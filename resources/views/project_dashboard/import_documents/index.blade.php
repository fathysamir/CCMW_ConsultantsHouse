@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Upload Single Document')
@section('content')
<style>
    .date{
        background-color:#fff !important;
    }
</style>
    <style>
        .uppy-Dashboard-inner {
            width: 100%;
            height: 350px;
        }

        body {
            /* تحديد ارتفاع الصفحة بنسبة لحجم الشاشة
                                                                                                overflow: hidden;
                                                                                                /* منع التمرير */
        }

        .uppy-StatusBar-actions {
            justify-content: center;
        }
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h2 mb-0 page-title">Import Documents from Excel</h2>
        </div>
        <div class="col-auto">
            <a type="button" href="#" class="btn mb-2 btn-outline-primary" id="btn-outline-primary"
                onclick="location.reload(); return false;">Reset</a>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12 " id="step1">
                    <div class="form-group mb-3">
                        <label for="file">Upload Excel File <span style="color: red">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="excelFile"
                                accept=".xlsx, .xls, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            <label class="custom-file-label" for="excelFile">Choose File</label>
                        </div>
                        <div class="mt-2">
                            <div class="progress d-none">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="excel_file_id" name="excel_file_id" value="">
                    <div class="form-group mb-3">
                        <label for="file">Upload PDF Files <span style="color: red">*</span></label>
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <strong>Upload PDF Files</strong>
                            </div>
                            <div class="card-body">
                                <div id="drag-drop-area"></div>
                            </div> <!-- .card-body -->
                        </div> <!-- .card -->
                    </div>
                </div> <!-- /.col -->

                <div class="col-md-12 d-none"id="step2">
                    <div class="form-group mb-3">
                        <label for="file">Sheet <span style="color: red">*</span></label>
                        <div class="custom-file">
                            <select class="form-control" id="sheets">
                                <option value="" selected disabled>Please Select Sheet</option>
                            </select>
                        </div>

                    </div>
                    <div class="form-group" style="display: flex; align-items: center;margin-top: 100px;">
                        <h4 style="margin-right: 10px;">Assign Documents To File</h4>
                        <hr style="flex: 1; margin: 0;">
                    </div>
                    <div class="form-group mb-3">
                        <label for="folder_id">Folder</label>
                        <select class="form-control" id="folder_id">
                            <option value="" disabled selected>Select Folder</option>
                            @foreach ($folders as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3 d-none files_" style="margin-top: 40px;">
                        <label for="newFile">File</label>
                        <select class="form-control" id="newFile">
                            <option value="" disabled selected>Select File</option>
                        </select>
                    </div>
                    <div class="text-right" style="margin-top: 10px;">
                        <button type="button"
                            class="btn mb-2 btn-outline-secondary"onclick="window.location.href='/project'">Cancel</button>
                        <button type="button" id="get_headers" class="btn mb-2 btn-outline-success" disabled>Get
                            Headers</button>
                    </div>
                </div>

                <div class="col-md-12 d-none"id="step3">
                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Document File Name <span style="color: red">*</span></label>
                            </div>
                            <div class="col-md-10">

                                <select class="form-control select_header" id="doc_file_name">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>

                            </div>
                        </div>

                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Subject <span style="color: red">*</span></label>
                            </div>
                            <div class="col-md-10">

                                <select class="form-control select_header" id="subject">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>

                            </div>
                        </div>

                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Type <span style="color: red">*</span></label>
                            </div>

                            <div class="col-md-5">
                                <select class="form-control select_header" id="type">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select class="form-control" id="typeForAll">
                                    <option value="" disabled selected>Select Document Type</option>
                                    @foreach ($documents_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Reference <span style="color: red">*</span></label>
                            </div>
                            <div class="col-md-10">

                                <select class="form-control select_header" id="reference">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>

                            </div>
                        </div>


                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Revision</label>
                            </div>
                            <div class="col-md-10">

                                <select class="form-control select_header" id="revision">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>

                            </div>
                        </div>


                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>From</label>
                            </div>

                            <div class="col-md-5">
                                <select class="form-control select_header" id="from">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select class="form-control" id="fromForAll">
                                    <option value="" disabled selected>Select Stakeholder</option>
                                    @foreach ($stake_holders as $stake_holder)
                                        <option value="{{ $stake_holder->id }}">
                                            {{ $stake_holder->role }} - {{ $stake_holder->narrative }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>To</label>
                            </div>

                            <div class="col-md-5">
                                <select class="form-control select_header" id="to">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <select class="form-control" id="toForAll">
                                    <option value="" disabled selected>Select Stakeholder</option>
                                    @foreach ($stake_holders as $stake_holder)
                                        <option value="{{ $stake_holder->id }}">
                                            {{ $stake_holder->role }} - {{ $stake_holder->narrative }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Date <span style="color: red">*</span></label>
                            </div>
                            <div class="col-md-5">

                                <select class="form-control select_header" id="start_date">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>

                            </div>
                            <div class="col-md-5">
                                <input type="date" id="start_dateForAll" class="form-control date">
                            </div>
                        </div>


                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Return Date</label>
                            </div>
                            <div class="col-md-10">

                                <select class="form-control select_header" id="end_date">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>

                            </div>
                        </div>


                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Status</label>
                            </div>

                            <div class="col-md-5">
                                <select class="form-control select_header" id="status">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" id="statusForAll" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Thread</label>
                            </div>

                            <div class="col-md-5">
                                <select class="form-control select_header" id="thread">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" id="threadForAll" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Note</label>
                            </div>

                            <div class="col-md-5">
                                <select class="form-control select_header" id="note">
                                    <option value="" selected disabled>Select Column Name</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" id="noteForAll" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="form-group mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Analyzed By <span style="color: red">*</span></label>
                            </div>
                            <div class="col-md-10">

                                <select class="form-control" id="analyzed_By">
                                    <option value="" selected disabled>Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>


                    </div>
                    <div class="form-group" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>

                    <div class="custom-control custom-checkbox" style="text-align: center;">
                        <input type="checkbox" class="custom-control-input"id="analysis_complete">
                        <label class="custom-control-label" for="analysis_complete">Analysis Complete</label>
                    </div>

                    <div class="text-right" style="margin-top: 10px;">
                        <button type="button"
                            class="btn mb-2 btn-outline-secondary"onclick="window.location.href='/project'">Cancel</button>
                        <button type="button" id="start_import" class="btn mb-2 btn-outline-success" disabled>Start
                            Import</button>
                    </div>
                </div>

                <div class="col-md-12 d-none"id="step4">   
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let excelUploaded = false;
            let pdfUploaded = false;

            function checkUploadStatus() {
                if (excelUploaded && pdfUploaded) {
                    setTimeout(function() {
                        $('#step1').addClass('d-none');
                        $('#step2').removeClass('d-none');
                    }, 700);

                }
            }

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#excelFile').on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                // Show progress bar
                const $progress = $('.progress');
                const $progressBar = $progress.find('.progress-bar');
                $progress.removeClass('d-none');

                $.ajax({
                    url: '/upload-import-excel-file',
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
                            $('#excel_file_id').val(file.id);
                            let sheetsDropdown = $('#sheets');
                            sheetsDropdown.empty().append(
                                '<option value="" selected disabled>Please Select Sheet</option>'
                            );

                            if (response.sheets.length > 0) {

                                $.each(response.sheets, function(index, sheet) {
                                    sheetsDropdown.append(
                                        `<option value="${sheet}">${sheet}</option>`
                                    );
                                });
                                excelUploaded = true;
                                checkUploadStatus();

                            }
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

            var uptarg = document.getElementById('drag-drop-area');
            if (uptarg) {
                var uppy = Uppy.Core({
                    restrictions: {
                        allowedFileTypes: ['application/pdf'],
                        maxNumberOfFiles: 100, // Optional: limit number of files
                        // maxFileSize: 50 * 1024 * 1024 // Optional: 10MB limit
                    }
                }).use(Uppy.Dashboard, {
                    inline: true,
                    target: uptarg,
                    proudlyDisplayPoweredByUppy: false,
                    theme: 'dark',
                    width: 770,
                    height: 210,
                    note: 'PDF files only (max 10MB each)',
                    restrictions: {
                        allowedFileTypes: ['application/pdf']
                    },
                    plugins: ['Webcam']
                }).use(Uppy.Tus, {
                    endpoint: 'https://master.tus.io/files/'
                });
                uppy.on('complete', (result) => {
                    console.log('Upload complete! We’ve uploaded these files:', result.successful)

                    const formData = new FormData();

                    result.successful.forEach((file, index) => {
                        formData.append(`files[]`, file.data); // Append each file separately
                    });

                    // Send the file data to another Laravel route (e.g., for database storage)
                    $.ajax({
                        url: '/upload-multi-files',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,

                        success: function(response) {
                            if (response.success) {

                                pdfUploaded = true;
                                checkUploadStatus();

                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Upload error:', error);
                            alert('Failed to upload file');
                        },
                        complete: function() {
                            // Hide progress bar

                        }
                    });
                });
            }
            $('#sheets').change(function() {
                $("#get_headers").prop("disabled", false);
            })

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
            $("#get_headers").click(function() {
                let selectedSheet = $("#sheets").val();
                let selectedFile = $("#newFile").val();

                if (!selectedSheet) {
                    alert("Please select Sheet before proceeding.");
                    return;
                }

                $.ajax({
                    url: "/get-headers",
                    type: "POST",
                    data: {
                        sheet: selectedSheet,
                        file_id: selectedFile,
                        _token: "{{ csrf_token() }}" // If using Laravel CSRF protection
                    },
                    success: function(response) {
                        // console.log("Headers received:", response.headers)
                        if (response.headers && response.headers.length > 0) {
                            $(".select_header").each(function() {
                                let select = $(this);
                                select.empty(); // Clear existing options
                                select.append(
                                    '<option value="" selected disabled>Select Header</option>'
                                ); // Default option

                                // Append each header as an option
                                response.headers.forEach(header => {
                                    select.append(
                                        `<option value="${header}">${header}</option>`
                                    );
                                });
                            });
                        } else {
                            alert("No headers found in the selected sheet.");
                        }
                        $('#step2').addClass('d-none');
                        $('#step3').removeClass('d-none');
                        // Handle response (e.g., display headers)
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        alert("Failed to retrieve headers.");
                    }
                });
            });

            $(".select_header, #analyzed_By, #typeForAll,#fromForAll,#toForAll,#statusForAll,#noteForAll, #threadForAll")
                .on("change input", function() {
                    checkValues();
                });

            function checkValues() {
                let isValid = true;
                if ($("#sheets").val() == '' || !$("#sheets").val()) {
                    isValid = false;
                }
                if ($("#doc_file_name").val() == '' || !$("#doc_file_name").val()) {
                    isValid = false;
                }
                if ($("#subject").val() == '' || !$("#subject").val()) {
                    isValid = false;
                }
                if (($("#type").val() == '' || !$("#type").val()) && ($("#typeForAll").val() == '' || !$(
                        "#typeForAll").val())) {
                    isValid = false;
                }
                if ($("#reference").val() == '' || !$("#reference").val()) {
                    isValid = false;
                }
               
                if (($("#start_date").val() == '' || !$("#start_date").val()) && ($("#start_dateForAll").val() == '' || !$(
                    "#start_dateForAll").val())) {
                    isValid = false;
                }
               
                
                if ($("#analyzed_By").val() == '' || !$("#analyzed_By").val()) {
                    isValid = false;
                }
                $("#start_import").prop("disabled", !isValid);

            }
            $("#start_import").click(function() {
                let data = {
                    sheet: $("#sheets").val(),

                    doc_file_name: $("#doc_file_name").val(),
                    subject: $("#subject").val(),
                    type: $("#type").val(),
                    typeForAll: $("#typeForAll").val(),
                    reference: $("#reference").val(),
                    revision: $("#revision").val(),
                    from: $("#from").val(),
                    fromForAll: $("#fromForAll").val(),
                    to: $("#to").val(),
                    toForAll: $("#toForAll").val(),
                    start_date: $("#start_date").val(),
                    start_dateForAll: $("#start_dateForAll").val(),
                    end_date: $("#end_date").val(),
                    status: $("#status").val(),
                    statusForAll: $("#statusForAll").val(),
                    thread: $("#thread").val(),
                    threadForAll: $("#threadForAll").val(),
                    note: $("#note").val(),
                    noteForAll: $("#noteForAll").val(),
                    analyzed_By: $("#analyzed_By").val(),
                    analysis_complete: $("#analysis_complete").prop("checked") ? 1 :
                    0, // Convert checkbox to boolean
                    _token: "{{ csrf_token() }}" // Laravel CSRF token
                };



                $.ajax({
                    url: "/start-import", // Change this to your Laravel route
                    type: "POST",
                    data: data,
                    success: function(response) {
                        console.log("Import Success:", response);
                        $('#step4').html(response.html);
                        $('#step3').addClass('d-none');
                        $('#step4').removeClass('d-none');
                    },
                    error: function(xhr, status, error) {
                        console.error("Import Error:", error);
                        alert("Failed to start import.");
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

        });
    </script>
@endpush
