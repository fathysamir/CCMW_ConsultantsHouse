@extends('project_dashboard.layout.app')
@section('title', 'Project Home - File Attachment')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .custom-fieldset {
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 8px;

            width: 100%;
            background-color: #fefefe;
            position: relative;
        }

        .custom-legend {
            font-weight: bold;
            font-size: 1.2rem;
            padding: 0 10px;
            color: #333;
            width: auto;
            max-width: 100%;
        }
        #btn-outline-primary {
            color: blue;
        }

        body {
            height: 100vh;
            /* تحديد ارتفاع الصفحة بنسبة لحجم الشاشة */
            overflow: hidden;
            /* منع التمرير */
        }

        #btn-outline-primary:hover {
            color: white;
            /* Change text color to white on hover */
        }

        .date {
            background-color: #fff !important;
        }

        .table-container {
            position: relative;
            max-height: 750px;
            /* Adjust this value based on your needs */
            overflow: hidden;
        }

        .table-container table {
            width: 100%;
            margin: 0;
        }

        .table-container thead th {
            padding-right: 0.75rem !important;
        }

        .table-container thead {
            position: sticky;
            top: 0;
            z-index: 1;
            /* Match your background color */
        }

        .table-container tbody {
            overflow-y: auto;
            display: block;
            height: calc(450px - 40px);
            /* Adjust based on your header height */
        }

        .table-container thead,
        .table-container tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        /* Ensure consistent column widths */
        .table-container th:nth-child(1),
        .table-container td:nth-child(1) {
            width: 1% !important;
        }



        .table-container th:nth-child(2),
        .table-container td:nth-child(2) {
            width: 6% !important;
        }

        .table-container th:nth-child(3),
        .table-container td:nth-child(3) {
            width: 7% !important;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 83% !important;
        }


        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 3% !important;
        }

        /* Maintain styles from your original table */
        .table-container tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }
   
        .table-container tbody::-webkit-scrollbar {
            width: 6px;
        }

        .table-container tbody::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-container tbody::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .table-container tbody::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        #dataTable-1_filter label {
            width: 100%;
            width-space: none;
        }

        #dataTable-1_filter label input {
            width: 92%;

        }
                                                                                                                                                                                                                                                                                                                                        } */
    </style>
    <div id="hintBox"
        style="
        display:none;
        position: fixed;
        top: 65px;
        right: 42%;
        background-color: #d4edda;
        color: #155724;
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        z-index: 9999;
        font-size: 0.9rem;
        ">
    </div>
    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">{{ $file->name }} - {{ $Type_Name[$type] }}</h2>
        </div>
        <div class="col-auto">
            <a type="button" href="{{ route('project.file-attachments.create_attachment',['type'=>$type,'file_id'=>$file->slug]) }}"
            class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create Attachment</a>
            <button type="button" class="btn mb-2 dropdown-toggle btn-success"data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">File
                Action</button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="javascript:void(0);" data-file-id="{{ $file->slug }}" id="export-allDoc">
                    Export
                </a>

            </div>
        </div>
    </div>
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
    <div class="row my-4">
        <!-- Small table -->
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <!-- Table container with fixed height -->
                    <div class="table-container">

                        <!-- Table -->
                        <table class="table datatables" id="dataTable-1" style="font-size: 0.7rem;">

                            <thead>
                                <tr>
                                    <th id="check"class="">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="select-all">
                                            <label class="custom-control-label" for="select-all"></label>
                                        </div>
                                    </th>

                                    <th>
                                        <label id="all_for_claim"
                                            style=" background-color: rgb(169, 169, 169); width:15px;height:15px;border-radius: 50%;text-align:center;cursor: pointer;"><span>C</span></label>

                                        <label id="all_blue_flag" style="margin-right:0.02rem;cursor: pointer;"
                                            title="Blue Flags">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
                                        <label id="all_red_flag" style="cursor: pointer;" title="Blue Flags">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
                                    </th>
                                    <th><b>Order</b></th>
                                    <th><b>Narrative</b></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($attachments as $attachment)
                                    <tr id="dddd_{{ $attachment->id }}"
                                        @if ($specific_file_attach == $attachment->id) style="background-color: #AFEEEE" class="specific_file_attach" @endif>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input row-checkbox"
                                                    data-file-id="{{ $attachment->id }}" id="checkbox-{{ $attachment->id }}"
                                                    value="{{ $attachment->id }}">
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $attachment->id }}"></label>
                                            </div>
                                        </td>
                                        <td>

                                            <span
                                                class="fe fe-22 @if ($attachment->narrative != null) fe-file-text doc_narrative @else fe-file @endif"
                                                @if ($attachment->narrative != null) style="cursor:pointer;" @endif
                                                data-attachment-id="{{ $attachment->id }}"></span>
                                            <label
                                                class="for_claim for-claim-btn222 @if ($attachment->forClaim == '1') active @endif"
                                                style="@if ($attachment->forClaim == '1') background-color: rgb(45, 209, 45); @else background-color: rgb(169, 169, 169); @endif width:15px;height:15px;border-radius: 50%;text-align:center;cursor: pointer;"
                                                data-attachment-id="{{ $attachment->id }}"
                                                data-action-type="forClaim"><span>C</span></label>

                                            <label
                                                class="blue_flag change-flag @if (in_array($attachment->id, $array_blue_flags ?? [])) active @endif"
                                                style="margin-right:0.02rem;cursor: pointer;"data-attachment-id="{{ $attachment->id }}"
                                                data-flag="blue" title="Blue Flag">
                                                @if (in_array($attachment->id, $array_blue_flags ?? []))
                                                    <i class="fa-solid fa-flag" style="color: #0000ff;"></i>
                                                @else
                                                    <i class="fa-regular fa-flag"></i>
                                                @endif

                                            </label>
                                            <label
                                                class="red_flag change-flag @if (in_array($attachment->id, $array_red_flags ?? [])) active @endif"
                                                data-attachment-id="{{ $attachment->id }}" data-flag="red"
                                                style="cursor: pointer;" title="Red Flag">
                                                @if (in_array($attachment->id, $array_red_flags ?? []))
                                                    <i class="fa-solid fa-flag"style="color: #ff0000;"></i>
                                                @else
                                                    <i class="fa-regular fa-flag"></i>
                                                @endif
                                            </label>
                                        </td>
                                        <td>{{ $attachment->order }}</td>
                                        <td>{!! $attachment->narrative !!}</td>
                                        <td>
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item"
                                                    href="{{ route('project.file-attachments.attachment' , $attachment->id) }}">
                                                    Edit
                                                </a>
                                                <a class="dropdown-item copy-to-file-btn" href="javascript:void(0);"
                                                    data-attachment-id="{{ $attachment->id }}" data-action-type="Copy">Copy
                                                    To another File</a>
                                                <a class="dropdown-item copy-to-file-btn" href="javascript:void(0);"
                                                    data-attachment-id="{{ $attachment->id }}" data-action-type="Move">Move
                                                    To another File</a>
                                                <a class="dropdown-item Delete-btn" href="javascript:void(0);"
                                                    data-attachment-id="{{ $attachment->id }}">Delete</a>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="copyToForAllModal" tabindex="-1" role="dialog"
        aria-labelledby="copyToForAllModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="copyToForAllModalLabel">

                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="copyToForAllForm">
                        @csrf
                        <input type="hidden" id="attachmentIds" name="attachmentIds">
                        <input type="hidden" id="action_type2" name="action_type2">
                        <div class="form-group">
                            <label for="folder_id2">Select Folder</label>
                            <select class="form-control" id="folder_id2" required>
                                <option value="" disabled selected>Select Folder</option>
                                @foreach ($folders as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none">
                            <label for="newFile2">Select File</label>
                            <select class="form-control" id="newFile2" name="file_id2">
                                <option value="" disabled selected>Select File</option>

                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCopyDocs">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Settings To Export Attachments
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportForm">
                        @csrf
                        <input type="hidden" id="file_id111" name="file_id111">
                        <input type="hidden" id="attach_type" name="attach_type" value="{{ $type }}">
                        <div class="form-group">
                            <label for="newDocTypeForAll">Heading 1 Number</label>
                            <input type="Number" required name="Chapter" class="form-control" placeholder="Heading 1"
                                id="Chapter" value="1" min="1"
                                oninput="this.value = Math.max(1, this.value)">
                        </div>
                        <div class="form-group">
                            <label for="newDocTypeForAll">Heading 2 Number</label>
                            <input type="Number" required name="Section" class="form-control" placeholder="Heading 2"
                                id="Section" value="0" min="0"
                                oninput="this.value = Math.max(0, this.value)">
                        </div>
                        <div class="form-group">
                            <label for="subtitle">Section</label>
                            <input type="text" required name="subtitle" class="form-control" placeholder="Subtitle"
                                id="subtitle" value="{{ $Type_Name[$type] }}">
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"checked id="forclaimdocs"
                                name="forclaimdocs">
                            <label class="custom-control-label" for="forclaimdocs">For Claim Attachments</label>
                        </div>
                        <hr>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="export">Export</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="narrativeModal" tabindex="-1" role="dialog" aria-labelledby="narrativeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 800px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="narrativeModalLabel">Narrative
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="narrative_container">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        window.addEventListener('DOMContentLoaded', function() {
            const targetRow = document.querySelector('.specific_file_attach');
            const container = document.querySelector('.table-container tbody');
            console.log(targetRow.offsetTop);
            if (targetRow && container) {
                const headerHeight = 0; // في حالتك الهيدر sticky فوق الجدول مش جواه، فمش لازم نطرح ارتفاعه
                const offsetTop = targetRow.offsetTop - headerHeight;
                container.scrollTop = offsetTop - 58;
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            // When "Download All" button is clicked
            $('#export-allDoc').on('click', function() {
                const fileId = $(this).data('file-id');
                $('#file_id111').val(fileId);
                $('#exportModal').modal('show');
            });

            // When user clicks "Save" (download)
            $('#export').on('click', function() {
                const form = $('#exportForm');

                // Optional client-side check before AJAX send
                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }

                const formData = form.serialize();

                $.ajax({
                    url: '/export-word-claim-attachments', // Replace with real route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // showHint(response.message || 'Download started!');
                        if (response.download_url) {
                            window.location.href = response.download_url; // يبدأ التحميل فعليًا
                        }
                        $('#exportModal').modal('hide');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Failed to process. Please try again.');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {

            $('.doc_narrative').on('click', function() {
                const DOCid = $(this).data('attachment-id');

                $.ajax({
                    url: '/get-attachment-narrative',
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_id: DOCid, // Pass the array here
                    },

                    success: function(response) {
                        // showHint(response.message || 'Download started!');
                        $('#narrative_container').html(response.html);
                        $('#narrativeModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Failed to process. Please try again.');
                    }
                });

            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $('.dropdown-toggle').dropdown();


            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
            $("#check").removeClass("sorting_asc");



            $('.Delete-btn').on('click', function() {
                var documentIds = [];
                documentIds.push($(this).data('attachment-id'));
                if (confirm(
                        'Are you sure you want to delete this attachment from file? This action cannot be undone.'
                    )) {
                    $.ajax({
                        url: '/project/delete-attach-from-file', // Adjust the route to your API endpoint
                        type: 'POST',
                        data: {
                            _token: $('input[name="_token"]').val(), // CSRF token
                            document_ids: documentIds,
                        },
                        success: function(response) {
                            documentIds.forEach(function(id) {
                                document.getElementById('dddd_' + id)?.remove();
                            });
                            showHint(response.message); // Show success message
                        },
                        error: function() {
                            alert('Failed to assign attachment. Please try again.');
                        }
                    });
                }
            })


            ////////////////////////////////////////////////
            $('.for-claim-btn').on('click', function() {
                var documentIds = [];
                var type = $(this).data('action-type')
                documentIds.push($(this).data('document-id'));

                $.ajax({
                    url: '/project/attachment/make-for-claim', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_ids: documentIds,
                        action_type: type,
                        val: '1'
                    },
                    success: function(response) {
                        documentIds.forEach(function(id) {
                            let tr = document.getElementById('dddd_' + id);
                            if (tr) {
                                if (type == 'forClaim') {
                                    const forClaimLabel = tr.querySelector(
                                        'label.for_claim');
                                    if (forClaimLabel && !forClaimLabel.classList
                                        .contains('active')) {
                                        forClaimLabel.classList.add('active');
                                        forClaimLabel.style.backgroundColor =
                                            'rgb(45, 209, 45)';
                                    }
                                } else if (type == 'forLetter') {
                                    const forNoticeLabel = tr.querySelector(
                                        'label.for_notice');
                                    if (forNoticeLabel && !forNoticeLabel.classList
                                        .contains('active')) {
                                        forNoticeLabel.classList.add('active');
                                        forNoticeLabel.style.backgroundColor =
                                            'rgb(45, 209, 45)';
                                    }
                                } else if (type == 'forChart') {
                                    const forChartLabel = tr.querySelector(
                                        'label.for_timeline');
                                    if (forChartLabel && !forChartLabel.classList
                                        .contains('active')) {
                                        forChartLabel.classList.add('active');
                                        forChartLabel.style.backgroundColor =
                                            'rgb(45, 209, 45)';
                                    }
                                }

                            }
                        });
                    },
                    error: function() {
                        alert('Failed to assign document. Please try again.');
                    }
                });

            })
            $('.change-flag').on('click', function() {
                var type = $(this).data('flag')
                var docId = $(this).data('attachment-id');
                var $button = $(this);

                $.ajax({
                    url: '/project/change-flag/attachment', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        docId: docId,
                        type: type
                    },
                    success: function(response) {
                        if (response.success == false) {
                            $button.removeClass('active');
                            $button.html(
                                `<i class="fa-regular fa-flag"></i>`
                            );
                        } else {
                            $button.addClass('active');
                            if (type == 'red') {
                                $button.html(
                                    `<i class="fa-solid fa-flag" style="color: #ff0000;"></i>`
                                );
                            } else {
                                $button.html(
                                    `<i class="fa-solid fa-flag" style="color: #0000ff;"></i>`
                                );
                            }
                        }
                    },
                    error: function() {
                        alert('Failed to assign attachment. Please try again.');
                    }
                });
            });
            $('.for-claim-btn222').on('click', function() {
                var documentIds = [];
                var type = $(this).data('action-type')
                documentIds.push($(this).data('attachment-id'));

                let value = ''
                if (this.classList.contains('active')) {
                    value = '0';
                } else {
                    value = '1';
                }

                $.ajax({
                    url: '/project/attachment/make-for-claim', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_ids: documentIds,
                        action_type: type,
                        val: value
                    },
                    success: function(response) {
                        documentIds.forEach(function(id) {
                            let tr = document.getElementById('dddd_' + id);
                            if (tr) {

                                if (type == 'forClaim') {
                                    const forClaimLabel = tr.querySelector(
                                        'label.for_claim');
                                    if (forClaimLabel && !forClaimLabel.classList
                                        .contains('active')) {
                                        forClaimLabel.classList.add('active');
                                        forClaimLabel.style.backgroundColor =
                                            'rgb(45, 209, 45)';
                                    } else {
                                        forClaimLabel.classList.remove('active');
                                        forClaimLabel.style.backgroundColor =
                                            'rgb(169, 169, 169)';
                                    }
                                }

                            }
                        });
                    },
                    error: function() {
                        alert('Failed to assign attachment. Please try again.');
                    }
                });

            })
            ///////////////////////////////////////////////

            function showHint(message, bgColor = '#d4edda', textColor = '#155724') {
                const hintBox = document.getElementById("hintBox");
                hintBox.innerText = message;
                hintBox.style.backgroundColor = bgColor;
                hintBox.style.color = textColor;
                hintBox.style.display = "block";

                setTimeout(() => {
                    hintBox.style.display = "none";
                }, 3000); // Hide after 3 seconds
            }

            $('.copy-to-file-btn').on('click', function() {
                var documentId_ = [];
                documentId_.push($(this).data('attachment-id'));
                //var documentId_ = $(this).data('attachment-id');
                var action_type = $(this).data('action-type');


                $('#attachmentIds').val(documentId_.join(','));
                $('#action_type2').val(action_type);
                $('#folder_id2').val('');
                let fileDropdown = $('#newFile2');
                fileDropdown.closest('.form-group').addClass(
                    'd-none');
                if (documentId_.length > 1) {
                    document.getElementById('copyToForAllModalLabel').innerHTML =
                        `<spam id="type2">${action_type}</spam> Selected Attachments To another File`;
                } else {
                    document.getElementById('copyToForAllModalLabel').innerHTML =
                        `<spam id="type2">${action_type}</spam> Attachment To another File`;
                }

                $('#copyToForAllModal').modal('show'); // Show the modal
            });



            $('#folder_id2').change(function() {
                let folderId = $(this).val();

                if (!folderId) return; // Stop if no folder is selected

                $.ajax({
                    url: '/project/folder/get-files/' +
                        folderId, // Adjust the route to your API endpoint
                    type: 'GET',
                    success: function(response) {
                        let fileDropdown = $('#newFile2');
                        fileDropdown.empty().append(
                            '<option value="" disabled selected>Select File</option>');

                        if (response.files.length > 0) {
                            $.each(response.files, function(index, file) {
                                fileDropdown.append(
                                    `<option value="${file.id}">${file.name}</option>`
                                );
                            });

                            fileDropdown.closest('.form-group').removeClass(
                                'd-none'); // Show file dropdown
                        } else {
                            fileDropdown.closest('.form-group').addClass(
                                'd-none'); // Hide if no files
                        }
                    },
                    error: function() {
                        alert('Failed to fetch files. Please try again.');
                    }
                });
            });

            $('#saveCopyDocs').click(function() {
                let ids = $('#attachmentIds').val();

                // Convert to array (assuming comma-separated string)
                let attachmentIdsArray = ids ? ids.split(',') : [];

                //let documentId = $('#documentId_').val();
                let fileId = $('#newFile2').val();
                let actionType = $('#action_type2').val();

                if (!fileId) {
                    alert('Please select a file.');
                    return;
                }

                $.ajax({
                    url: '/project/copy_move_attachment_to_another_file', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_ids: attachmentIdsArray,
                        file_id: fileId,
                        actionType: actionType
                    },
                    success: function(response) {
                        if (response.status == 'error') {
                            alert("⚠️ " + response.message);
                        } else {
                            if (actionType == 'move') {
                                attachmentIdsArray.forEach(function(id) {
                                    document.getElementById('dddd_' + id)?.remove();
                                });


                            }
                            showHint(response.message); // Show success message

                        }
                        $('#copyToForAllModal').modal('hide');
                    },
                    error: function() {
                        alert('Failed to assign attachment. Please try again.');
                    }
                });
            });

            const parentDiv = document.getElementById('dataTable-1_wrapper');

            if (parentDiv) {
                const rowDiv = parentDiv.querySelector('.row');

                if (rowDiv) {
                    const colDivs = rowDiv.querySelectorAll('.col-md-6');

                    if (colDivs.length > 0) {
                        colDivs[0].classList.remove('col-md-6');
                        colDivs[0].classList.add('col-md-2');
                    }

                    // Create a new dropdown element
                    let new_down_list = document.createElement('div');
                    new_down_list.className = "col-sm-12 col-md-4";
                    new_down_list.innerHTML = `
                                <div class="dropdown" id="Action-DIV" style="text-align:center">
                                    <button class="btn btn-sm dropdown-toggle  btn-secondary" type="button"
                                        id="actionButton" aria-haspopup="true" aria-expanded="false">
                                        Open Actions
                                    </button>
                                    <div class="dropdown-menu " id="actionList" style="position: absolute;right:10px; ">
                                         <a class="dropdown-item copyForAllBtn" id="copyForAllBtn" href="javascript:void(0);"data-action-type2="Copy">Copy All Documents To Another File</a>
                                         <a class="dropdown-item copyForAllBtn" id="moveForAllBtn" href="javascript:void(0);"data-action-type2="Move">Move All Documents To Another File</a>
                                         <a class="dropdown-item" id="removeForAllBtn" href="javascript:void(0);">Delete</a>
                                         <a class="dropdown-item for-claim-btn-for-all" data-action-type="forClaim" id="forClaimForAllBtn" href="javascript:void(0);">For Claim</a>


                                    </div>
                                </div>
                            `;

                    // Append the new dropdown to the row
                    rowDiv.appendChild(new_down_list);

                    // Get the button and dropdown menu
                    const actionButton = new_down_list.querySelector('#actionButton');
                    const actionList = new_down_list.querySelector('#actionList');

                    // Toggle dropdown on button click
                    actionButton.addEventListener('click', function(event) {
                        event.stopPropagation(); // Prevent the click from bubbling up
                        if (actionList.style.display === 'block') {
                            actionList.style.display = 'none';
                        } else {
                            actionList.style.display = 'block';
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!event.target.closest('.dropdown')) {
                            actionList.style.display = 'none';
                        }
                        
                    });
                }
            }

            // Select all the checkboxes with the class "row-checkbox"
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const actionDiv = document.getElementById('Action-DIV');

            // Initially hide the Action-DIV
            if (actionDiv) {
                actionDiv.style.display = 'none';
            }

            // Add an event listener to each checkbox
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    // Get the number of checkboxes that are checked
                    const checkedCheckboxes = document.querySelectorAll(
                        'tbody tr:not([style*="display: none"]) .row-checkbox:checked');

                    // If more than one checkbox is checked, display the Action-DIV, else hide it
                    if (checkedCheckboxes.length > 1) {
                        actionDiv.style.display = 'block';
                    } else {
                        actionDiv.style.display = 'none';
                    }
                });
            });
            document.getElementById('select-all').addEventListener('change', function() {
                const visibleCheckboxes = document.querySelectorAll(
                    'tbody tr:not([style*="display: none"]) .row-checkbox');

                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });

                const checkedCheckboxes = document.querySelectorAll(
                    'tbody tr:not([style*="display: none"]) .row-checkbox:checked');
                // If more than one checkbox is checked, display the Action-DIV, else hide it
                if (checkedCheckboxes.length > 1) {
                    actionDiv.style.display = 'block';
                } else {
                    actionDiv.style.display = 'none';
                }
            });




        });
    </script>
    <script>
        $(document).ready(function() {
            function showHint(message, bgColor = '#d4edda', textColor = '#155724') {
                const hintBox = document.getElementById("hintBox");
                hintBox.innerText = message;
                hintBox.style.backgroundColor = bgColor;
                hintBox.style.color = textColor;
                hintBox.style.display = "block";

                setTimeout(() => {
                    hintBox.style.display = "none";
                }, 3000); // Hide after 3 seconds
            }

           
            $('.copyForAllBtn').on('click', function() {
                // Get all checked checkboxes

                const checkedCheckboxes = document.querySelectorAll(
                    'tbody tr:not([style*="display: none"]) .row-checkbox:checked');

                if (checkedCheckboxes.length === 0) {
                    alert('Please select at least one Attachment.');
                    return;
                }

                // Collect the IDs of all checked checkboxes
                let documentIds = [];
                checkedCheckboxes.forEach(function(checkbox) {
                    documentIds.push(checkbox.value);
                });

                // Set the document IDs in a hidden input (optional)
                $('#attachmentIds').val(documentIds.join(','));
                var action_type2 = $(this).data('action-type2');
                $('#action_type2').val(action_type2);
                // Open the modal
                $('#folder_id2').val('');
                let fileDropdown = $('#newFile2');
                fileDropdown.closest('.form-group').addClass(
                    'd-none');
                    document.getElementById('copyToForAllModalLabel').innerHTML =
                    `<spam id="type2">${action_type2}</spam> Selected Attachments To another File`;
                $('#copyToForAllModal').modal('show');
            });

            $('#saveCopyDocs').click(function() {
                let documentIds = $('#attachmentIds').val().split(',');

                //let documentId = $('#documentId_').val();
                let fileId = $('#newFile2').val();
                let actionType = $('#action_type2').val();

                if (!fileId) {
                    alert('Please select a file.');
                    return;
                }

                $.ajax({
                    url: '/project/copy_move_attachment_to_another_file', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_ids: documentIds,
                        file_id: fileId,
                        actionType: actionType
                    },
                    success: function(response) {
                        if (response.status == 'error') {
                            alert("⚠️ " + response.message);
                        } else {
                            if (actionType == 'move') {
                                documentIds.forEach(function(id) {
                                    document.getElementById('dddd_' + id)?.remove();
                                });


                            }
                            showHint(response.message); // Show success message

                        }
                        $('#copyToForAllModal').modal('hide');
                    },
                    error: function() {
                        alert('Failed to assign attachment. Please try again.');
                    }
                });
            });
            $('#removeForAllBtn').on('click', function() {
                const checkedCheckboxes = document.querySelectorAll(
                    'tbody tr:not([style*="display: none"]) .row-checkbox:checked');

                if (checkedCheckboxes.length === 0) {
                    alert('Please select at least one document.');
                    return;
                }

                // Collect the IDs of all checked checkboxes
                let documentIds = [];
                checkedCheckboxes.forEach(function(checkbox) {
                    documentIds.push(checkbox.value);
                });

                if (confirm(
                        'Are you sure you want to delete these attachments from file? This action cannot be undone.'
                    )) {
                    $.ajax({
                        url: '/project/delete-attach-from-file', // Adjust the route to your API endpoint
                        type: 'POST',
                        data: {
                            _token: $('input[name="_token"]').val(), // CSRF token
                            document_ids: documentIds,
                        },
                        success: function(response) {
                            documentIds.forEach(function(id) {
                                document.getElementById('dddd_' + id)?.remove();
                            });
                            showHint(response.message); // Show success message
                        },
                        error: function() {
                            alert('Failed to assign attachment. Please try again.');
                        }
                    });
                }
            });
            $('.for-claim-btn-for-all').on('click', function() {
                const checkedCheckboxes = document.querySelectorAll(
                    'tbody tr:not([style*="display: none"]) .row-checkbox:checked');

                if (checkedCheckboxes.length === 0) {
                    alert('Please select at least one document.');
                    return;
                }

                // Collect the IDs of all checked checkboxes
                let documentIds = [];
                checkedCheckboxes.forEach(function(checkbox) {
                    documentIds.push(checkbox.value);
                });
                var type = $(this).data('action-type')


                $.ajax({
                    url: '/project/attachment/make-for-claim', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_ids: documentIds,
                        action_type: type,
                        val: '1'
                    },
                    success: function(response) {
                        documentIds.forEach(function(id) {
                            let tr = document.getElementById('dddd_' + id);
                            if (tr) {
                                if (type == 'forClaim') {
                                    const forClaimLabel = tr.querySelector(
                                        'label.for_claim');
                                    if (response.value == '1' && forClaimLabel && !
                                        forClaimLabel.classList
                                        .contains('active')) {
                                        forClaimLabel.classList.add('active');
                                        forClaimLabel.style.backgroundColor =
                                            'rgb(45, 209, 45)';
                                    } else if (response.value == '0' && forClaimLabel &&
                                        forClaimLabel.classList
                                        .contains('active')) {

                                        forClaimLabel.classList.remove('active');
                                        forClaimLabel.style.backgroundColor =
                                            'rgb(169, 169, 169)';
                                    }
                                } 

                            }
                        });
                    },
                    error: function() {
                        alert('Failed to assign attachment. Please try again.');
                    }
                });

            })

        })
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const filters = {
               
                all_for_claim: false,
              
                all_blue_flag: false,
                all_red_flag: false
               
            };

            function applyFilters() {
                document.querySelectorAll("tbody tr").forEach((row) => {
                    let show = true;

                    if (filters.all_for_claim) show = show && row.querySelector(".for_claim.active");
                    if (filters.all_red_flag) show = show && row.querySelector(".red_flag.active");
                    if (filters.all_blue_flag) show = show && row.querySelector(".blue_flag.active");

                    row.style.display = show ? "" : "none";
                });
            }

            function toggleFilter(id) {
                filters[id] = !filters[id];
                const label = document.getElementById(id);

               if (id === 'all_red_flag') {
                    label.innerHTML = filters[id] ? `<i class="fa-solid fa-flag"style="color: #ff0000;"></i>` :
                        `<i class="fa-regular fa-flag"></i>`;
                } else if (id === 'all_blue_flag') {
                    label.innerHTML = filters[id] ? `<i class="fa-solid fa-flag"style="color: #0000ff;"></i>` :
                        `<i class="fa-regular fa-flag"></i>`;
                } else {
                    label.style.backgroundColor = filters[id] ? "rgb(45, 209, 45)" : "rgb(169, 169, 169)";
                }


                applyFilters();
            }

            document.getElementById("all_for_claim").addEventListener("click", () => toggleFilter("all_for_claim"));
            
            document.getElementById("all_red_flag").addEventListener("click", () => toggleFilter(
                "all_red_flag"));
            document.getElementById("all_blue_flag").addEventListener("click", () => toggleFilter(
                "all_blue_flag"));

           
        });
    </script>
    <script src="{{ asset('dashboard/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $('#dataTable-1').DataTable({
            autoWidth: true,
            responsive: true,
            "lengthMenu": [
                [-1, 16, 32, 64],
                ["All", 16, 32, 64]
            ],
            "columnDefs": [{
                "targets": 0, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 1, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 4, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }]
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
