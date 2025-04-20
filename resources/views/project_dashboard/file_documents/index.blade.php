@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Files')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">

    <style>
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
    </style>
    <style>
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
            width: 1%;
        }



        .table-container th:nth-child(2),
        .table-container td:nth-child(2) {
            width: 9%;
        }

        .table-container th:nth-child(3),
        .table-container td:nth-child(3) {
            width: 19%;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 6.5%;
        }

        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 7.7%;
        }

        .table-container th:nth-child(6),
        .table-container td:nth-child(6) {
            width: 10.6%;
        }

        .table-container th:nth-child(7),
        .table-container td:nth-child(7) {
            width: 4%;
        }

        .table-container th:nth-child(8),
        .table-container td:nth-child(8) {
            width: 8.8%;
        }

        .table-container th:nth-child(9),
        .table-container td:nth-child(9) {
            width: 8.8%;
        }

        .table-container th:nth-child(10),
        .table-container td:nth-child(10) {
            width: 3%;
        }

        .table-container th:nth-child(11),
        .table-container td:nth-child(11) {
            width: 6.8%;
        }

        .table-container th:nth-child(12),
        .table-container td:nth-child(12) {
            width: 13.8%;
        }

        .table-container th:nth-child(13),
        .table-container td:nth-child(13) {
            width: 3%;
        }

        /* Maintain styles from your original table */
        .table-container tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }
    </style>
    <style>
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

        /* #dataTable-1_wrapper {
                                                                                                                                        max-height:650px;
                                                                                                                                    } */
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">{{ $file->name }}</h2>
        </div>
        <div class="col-auto">
            <button type="button"
                class="btn mb-2 btn-success"onclick="window.location.href='/export-word-claim-docs/<?php echo $file->slug; ?>'">Export</button>
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

                                    <th><label id="all_with_tags"><span class="fe fe-23 fe-volume-2"
                                                style="color: rgb(169, 169, 169);"></span></label>
                                        <label id="all_for_claim"
                                            style=" background-color: rgb(169, 169, 169); width:15px;height:15px;border-radius: 50%;text-align:center;"><span>C</span></label>
                                        <label id="all_for_notice"
                                            style=" background-color: rgb(169, 169, 169); width:15px;height:15px;border-radius: 50%;text-align:center;"><span>N</span></label>
                                        <label id="all_for_timeline"
                                            style=" background-color: rgb(169, 169, 169); width:15px;height:15px;border-radius: 50%;text-align:center;"><span>T</span></label>
                                    </th>
                                    <th><b>Subject </b>
                                        <span id="subjectFilterIcon" style="color:rgb(35, 197, 226); cursor: pointer;"
                                            class="fe fe-23 fe-filter"></span>

                                        <!-- Hidden Filter Div -->
                                        <div class="form-group" id="subjectFilterDiv"
                                            style="width:150px;display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
                                            <select class="form-control"id="subjectFilterType" style="margin-bottom: 5px;">
                                                <option value="contains">Contains</option>
                                                <option value="equals">Equal</option>
                                                <option value="not-equals">Not Equal</option>
                                                <option value="starts-with">Starts With</option>
                                                <option value="ends-with">Ends With</option>
                                            </select>
                                            <input class="form-control"type="text" id="subjectFilterInput"
                                                placeholder="Enter text"style="margin-bottom: 5px;">
                                            <button
                                                id="applySubjectFilter"class="btn mr-1 mb-2 btn-outline-primary">Apply</button>
                                            <button
                                                id="clearSubjectFilter"class="btn mb-2 btn-outline-warning">Clear</button>
                                        </div>
                                    </th>
                                    <th><b>Date </b> <span id="dateFilterIcon"
                                            style="color:rgb(35, 197, 226); cursor: pointer;"
                                            class="fe fe-23 fe-filter"></span>

                                        <!-- Hidden Filter Div -->
                                        <div class="form-group" id="dateFilterDiv"
                                            style="width:150px;display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
                                            <select class="form-control"id="dateFilterType" style="margin-bottom: 5px;">
                                                <option value="equals">Equals</option>
                                                <option value="not-equals">Not Equals</option>
                                                <option value="start-from">Start From</option>
                                                <option value="end-to">End To</option>

                                            </select>
                                            <input class="form-control date"type="date" id="dateFilterInput"
                                                placeholder="Enter date"style="margin-bottom: 5px;">
                                            <button
                                                id="applyDateFilter"class="btn mr-1 mb-2 btn-outline-primary">Apply</button>
                                            <button id="clearDateFilter"class="btn mb-2 btn-outline-warning">Clear</button>
                                        </div>
                                    </th>
                                    <th><b>Return Date </b>
                                        <span id="returnFilterIcon" style="color:rgb(35, 197, 226); cursor: pointer;"
                                            class="fe fe-23 fe-filter"></span>

                                        <!-- Hidden Filter Div -->
                                        <div class="form-group"id="returnFilterDiv"
                                            style="width:150px;display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
                                            <select class="form-control"id="returnFilterType" style="margin-bottom: 5px;">
                                                <option value="equals">Equals</option>
                                                <option value="not-equals">Not Equals</option>
                                                <option value="start-from">Start From</option>
                                                <option value="end-to">End To</option>

                                            </select>
                                            <input class="form-control date"type="date" id="returnFilterInput"
                                                placeholder="Enter date"style="margin-bottom: 5px;">
                                            <button
                                                id="applyReturnFilter"class="btn mr-1 mb-2 btn-outline-primary">Apply</button>
                                            <button
                                                id="clearReturnFilter"class="btn mb-2 btn-outline-warning">Clear</button>
                                        </div>
                                    </th>
                                    <th><b>Reference </b> <span id="referenceFilterIcon"
                                            style="color:rgb(35, 197, 226); cursor: pointer;"
                                            class="fe fe-23 fe-filter"></span>

                                        <!-- Hidden Filter Div -->
                                        <div class="form-group" id="referenceFilterDiv"
                                            style="width:150px;display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
                                            <select id="referenceFilterType"class="form-control"
                                                style="margin-bottom: 5px;">
                                                <option value="contains">Contains</option>
                                                <option value="equals">Equal</option>
                                                <option value="not-equals">Not Equal</option>
                                                <option value="starts-with">Starts With</option>
                                                <option value="ends-with">Ends With</option>
                                            </select>
                                            <input class="form-control"type="text" id="referenceFilterInput"
                                                placeholder="Enter text"style="margin-bottom: 5px;">
                                            <button
                                                id="applyReferenceFilter"class="btn mr-1 mb-2 btn-outline-primary">Apply</button>
                                            <button
                                                id="clearReferenceFilter"class="btn mb-2 btn-outline-warning">Clear</button>
                                        </div>
                                    </th>
                                    <th><b>Rev.</b></th>
                                    <th><b>From </b> <span id="fromFilterIcon"
                                            style="color:rgb(35, 197, 226); cursor: pointer;"
                                            class="fe fe-23 fe-filter"></span>

                                        <!-- Hidden Filter Div -->
                                        <div class="form-group" id="fromFilterDiv"
                                            style="width:150px;display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
                                            <select id="fromFilterType"class="form-control" style="margin-bottom: 5px;">
                                                <option value="contains">Contains</option>
                                                <option value="equals">Equal</option>
                                                <option value="not-equals">Not Equal</option>
                                                <option value="starts-with">Starts With</option>
                                                <option value="ends-with">Ends With</option>
                                            </select>
                                            <input class="form-control"type="text" id="fromFilterInput"
                                                placeholder="Enter text"style="margin-bottom: 5px;">
                                            <button
                                                id="applyFromFilter"class="btn mr-1 mb-2 btn-outline-primary">Apply</button>
                                            <button id="clearFromFilter"class="btn mb-2 btn-outline-warning">Clear</button>
                                        </div>
                                    </th>
                                    <th><b>To </b> <span id="toFilterIcon"
                                            style="color:rgb(35, 197, 226); cursor: pointer;"
                                            class="fe fe-23 fe-filter"></span>

                                        <!-- Hidden Filter Div -->
                                        <div class="form-group" id="toFilterDiv"
                                            style="width:150px;display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
                                            <select id="toFilterType"class="form-control" style="margin-bottom: 5px;">
                                                <option value="contains">Contains</option>
                                                <option value="equals">Equal</option>
                                                <option value="not-equals">Not Equal</option>
                                                <option value="starts-with">Starts With</option>
                                                <option value="ends-with">Ends With</option>
                                            </select>
                                            <input class="form-control"type="text" id="toFilterInput"
                                                placeholder="Enter text"style="margin-bottom: 5px;">
                                            <button
                                                id="applyToFilter"class="btn mr-1 mb-2 btn-outline-primary">Apply</button>
                                            <button id="clearToFilter"class="btn mb-2 btn-outline-warning">Clear</button>
                                        </div>
                                    </th>
                                    <th><b>SN</b></th>
                                    <th><b>Status </b> <span id="statusFilterIcon"
                                            style="color:rgb(35, 197, 226); cursor: pointer;"
                                            class="fe fe-23 fe-filter"></span>

                                        <!-- Hidden Filter Div -->
                                        <div class="form-group" id="statusFilterDiv"
                                            style="width:150px;display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);">
                                            <select id="statusFilterType"class="form-control" style="margin-bottom: 5px;">
                                                <option value="contains">Contains</option>
                                                <option value="equals">Equal</option>
                                                <option value="not-equals">Not Equal</option>
                                                <option value="starts-with">Starts With</option>
                                                <option value="ends-with">Ends With</option>
                                            </select>
                                            <input class="form-control"type="text" id="statusFilterInput"
                                                placeholder="Enter text"style="margin-bottom: 5px;">
                                            <button
                                                id="applyStatusFilter"class="btn mr-1 mb-2 btn-outline-primary">Apply</button>
                                            <button
                                                id="clearStatusFilter"class="btn mb-2 btn-outline-warning">Clear</button>
                                        </div>
                                    </th>
                                    <th><b>Note </b> <span id="noteFilterIcon"
                                            style="color:rgb(35, 197, 226); cursor: pointer;"
                                            class="fe fe-23 fe-filter"></span>

                                        <!-- Hidden Filter Div -->
                                        <div class="form-group" id="noteFilterDiv"
                                            style="display: none; position: absolute; background: white; border: 1px solid #ccc; padding: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);width:150px;">
                                            <select id="noteFilterType" class="form-control" style="margin-bottom: 5px;">
                                                <option value="contains">Contains</option>
                                                <option value="equals">Equal</option>
                                                <option value="not-equals">Not Equal</option>
                                                <option value="starts-with">Starts With</option>
                                                <option value="ends-with">Ends With</option>
                                            </select>
                                            <input class="form-control" type="text" id="noteFilterInput"
                                                placeholder="Enter text"style="margin-bottom: 5px;">
                                            <button id="applyNoteFilter"
                                                class="btn mr-1 mb-2 btn-outline-primary">Apply</button>
                                            <button id="clearNoteFilter"
                                                class="btn mb-2 btn-outline-warning">Clear</button>
                                        </div>
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($documents as $document)
                                    <tr @if ($specific_file_doc == $document->id) style="background-color: #AFEEEE" @endif>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input row-checkbox"data-file-id="{{ $document->id }}"
                                                    id="checkbox-{{ $document->id }}" value="{{ $document->id }}">
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $document->id }}"></label>
                                            </div>
                                        </td>
                                        <td>

                                            <label class="with_tag @if (count($document->tags) != 0) active @endif"><span
                                                    class="fe fe-23 fe-volume-2"
                                                    style="@if (count($document->tags) != 0) color: rgb(45, 209, 45); @else color: rgb(169, 169, 169); @endif"></span></label>
                                            <label class="for_claim @if ($document->forClaim == '1') active @endif"
                                                style="@if ($document->forClaim == '1') background-color: rgb(45, 209, 45); @else background-color: rgb(169, 169, 169); @endif width:15px;height:15px;border-radius: 50%;text-align:center;"><span>C</span></label>
                                            <label class="for_notice @if ($document->forLetter == '1') active @endif"
                                                style="@if ($document->forLetter == '1') background-color: rgb(45, 209, 45); @else background-color: rgb(169, 169, 169); @endif width:15px;height:15px;border-radius: 50%;text-align:center;"><span>N</span></label>
                                            <label class="for_timeline @if ($document->forChart == '1') active @endif"
                                                style="@if ($document->forChart == '1') background-color: rgb(45, 209, 45); @else background-color: rgb(169, 169, 169); @endif width:15px;height:15px;border-radius: 50%;text-align:center;"><span>T</span></label>
                                            <br>
                                            <span
                                                class="fe fe-22 @if ($document->narrative != null) fe-file-text @else fe-file @endif"></span>
                                            <label>{{ $document->document->docType->name }}</label>
                                        </td>

                                        <td><a class="l-link"style="color:rgb(80, 78, 78);" style="color:"
                                                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('analysis', $Project_Permissions ?? [])) href="{{ route('project.file-document-first-analyses', $document->id) }}" @endif>
                                                {{ $document->document->subject }}</a>
                                        </td>

                                        <td>{{ $document->document->start_date ? date('d.M.y', strtotime($document->document->start_date)) : '_' }}
                                        </td>
                                        <td>{{ $document->document->end_date ? date('d.M.y', strtotime($document->document->end_date)) : '_' }}
                                        </td>
                                        <td ondblclick="openDocumentPdf('{{ asset($document->document->storageFile->path) }}')"
                                            style="cursor: pointer;">{{ $document->document->reference }}</td>
                                        <td>{{ $document->document->revision }}</td>
                                        <td>{{ $document->document->fromStakeHolder ? $document->document->fromStakeHolder->narrative : '_' }}
                                        </td>
                                        <td>{{ $document->document->toStakeHolder ? $document->document->toStakeHolder->narrative : '_' }}
                                        </td>
                                        <td>{{ $document->sn }}</td>
                                        <td>{{ $document->document->status }}</td>
                                        <td>{{ $document->notes1 }}
                                        </td>
                                        <td>
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item copy-to-file-btn" href="javascript:void(0);"
                                                data-document-id="{{ $document->id }}">Copy To Another File</a>
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

@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openDocumentPdf(url) {
            window.open(url, '_blank');
        }
    </script>
    <script>
        $(document).ready(function() {
            $('.dropdown-toggle').dropdown();


            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
            $("#check").removeClass("sorting_asc");
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
                                <div class="dropdown" id="Action-DIV">
                                    <button class="btn btn-sm dropdown-toggle  btn-secondary" type="button"
                                        id="actionButton" aria-haspopup="true" aria-expanded="false">
                                        Open Actions
                                    </button>
                                    <div class="dropdown-menu " id="actionList" style="position: absolute;left:-50px; ">
                                        
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
                    const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

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
        document.addEventListener("DOMContentLoaded", function() {
            const filters = {
                all_with_tags: false,
                all_for_claim: false,
                all_for_notice: false,
                all_for_timeline: false,
                subject: {
                    type: "",
                    value: ""
                },
                reference: {
                    type: "",
                    value: ""
                },
                from: {
                    type: "",
                    value: ""
                },
                to: {
                    type: "",
                    value: ""
                },
                status: {
                    type: "",
                    value: ""
                },
                note: {
                    type: "",
                    value: ""
                },
                date: {
                    type: "",
                    value: ""
                },
                return: {
                    type: "",
                    value: ""
                },
            };

            function applyFilters() {
                document.querySelectorAll("tbody tr").forEach((row) => {
                    let show = true;

                    // 🔹 Tag Filtering
                    if (filters.all_with_tags) show = row.querySelector(".with_tag.active");
                    if (filters.all_for_claim) show = show && row.querySelector(".for_claim.active");
                    if (filters.all_for_notice) show = show && row.querySelector(".for_notice.active");
                    if (filters.all_for_timeline) show = show && row.querySelector(".for_timeline.active");

                    // 🔹 General Column Filtering (Subject, Reference, From, To, Status, Note)
                    show = show && applyTextFilter(row.cells[2], filters.subject); // Subject
                    show = show && applyTextFilter(row.cells[5], filters.reference); // Reference
                    show = show && applyTextFilter(row.cells[7], filters.from); // From
                    show = show && applyTextFilter(row.cells[8], filters.to); // To
                    show = show && applyTextFilter(row.cells[10], filters.status); // Status
                    show = show && applyTextFilter(row.cells[11], filters.note); // Note

                    // 🔹 Date Filtering (Start Date, Return Date)
                    show = show && applyDateFilter(row.cells[3], filters.date); // Start Date
                    show = show && applyDateFilter(row.cells[4], filters.return); // Return Date

                    row.style.display = show ? "" : "none";
                });
            }

            function applyTextFilter(cell, filter) {
                if (!cell || !filter.value) return true; // No filter applied
                let text = cell.textContent.trim().toLowerCase();
                let filterValue = filter.value.toLowerCase();

                switch (filter.type) {
                    case "contains":
                        return text.includes(filterValue);
                    case "equals":
                        return text === filterValue;
                    case "not-equals":
                        return text !== filterValue;
                    case "starts-with":
                        return text.startsWith(filterValue);
                    case "ends-with":
                        return text.endsWith(filterValue);
                    default:
                        return true;
                }
            }
            const monthMap = {
                "Jan": "01",
                "Feb": "02",
                "Mar": "03",
                "Apr": "04",
                "May": "05",
                "Jun": "06",
                "Jul": "07",
                "Aug": "08",
                "Sep": "09",
                "Oct": "10",
                "Nov": "11",
                "Dec": "12"
            };

            function formatTableDate(dateStr) {
                let parts = dateStr.split(".");
                if (parts.length === 3) {
                    let day = parts[0].padStart(2, "0"); // Ensure two digits
                    let month = monthMap[parts[1]];
                    let year = "20" + parts[2]; // Assuming years are in 2000s
                    return `${year}-${month}-${day}`;
                }
                return null;
            }

            function applyDateFilter(cell, filter) {

                if (!cell || !filter.value) return true;
                let cellDate = formatTableDate(cell.textContent.trim());
                let filterDate = filter.value;

                if (!(cellDate) || !(filterDate)) return false; // Invalid date
                switch (filter.type) {
                    case "equals":
                        console.log(cellDate === filterDate);
                        return cellDate === filterDate;
                    case "not-equals":
                        return cellDate !== filterDate;
                    case "start-from":
                        return cellDate >= filterDate;
                    case "end-to":
                        return cellDate <= filterDate;
                    default:
                        return true;
                }
            }


            function toggleFilter(id) {
                filters[id] = !filters[id];
                const label = document.getElementById(id);

                if (id === "all_with_tags") {
                    label.querySelector("span").style.color = filters[id] ? "rgb(45, 209, 45)" :
                        "rgb(169, 169, 169)";
                } else {
                    label.style.backgroundColor = filters[id] ? "rgb(45, 209, 45)" : "rgb(169, 169, 169)";
                }

                applyFilters();
            }

            document.getElementById("all_with_tags").addEventListener("click", () => toggleFilter("all_with_tags"));
            document.getElementById("all_for_claim").addEventListener("click", () => toggleFilter("all_for_claim"));
            document.getElementById("all_for_notice").addEventListener("click", () => toggleFilter(
                "all_for_notice"));
            document.getElementById("all_for_timeline").addEventListener("click", () => toggleFilter(
                "all_for_timeline"));

            function setupFilterUI(field) {

                let filterIcon = document.getElementById(`${field}FilterIcon`);
                let filterDiv = document.getElementById(`${field}FilterDiv`);
                let filterType = document.getElementById(`${field}FilterType`);
                let filterInput = document.getElementById(`${field}FilterInput`);
                let applyFilterBtn = document.getElementById(
                    `apply${field.charAt(0).toUpperCase() + field.slice(1)}Filter`);
                let clearFilterBtn = document.getElementById(
                    `clear${field.charAt(0).toUpperCase() + field.slice(1)}Filter`);

                filterIcon.addEventListener("click", function(event) {
                    event.stopPropagation();
                    filterDiv.style.display = filterDiv.style.display === "none" ? "block" : "none";
                });

                document.addEventListener("click", function(event) {
                    if (!filterDiv.contains(event.target) && event.target !== filterIcon) {
                        filterDiv.style.display = "none";
                    }
                });

                applyFilterBtn.addEventListener("click", function() {
                    filters[field].type = filterType.value;
                    filters[field].value = filterInput.value;
                    applyFilters();
                    filterDiv.style.display = "none";
                });

                clearFilterBtn.addEventListener("click", function() {
                    filters[field].type = "";
                    filters[field].value = "";
                    applyFilters();
                    filterDiv.style.display = "none";
                });
            }

            // Setup filters for all fields
            ["subject", "reference", "from", "to", "status", "note", "date", "return"].forEach(setupFilterUI);
        });
    </script>
    <script src="{{ asset('dashboard/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $('#dataTable-1').DataTable({
            autoWidth: true,
            responsive: true,
            "lengthMenu": [
                [16, 32, 64, -1],
                [16, 32, 64, "All"]
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
            }, {
                "targets": 6, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 9, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 11, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 12, // Target the first column (index 0)
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
            });

        });
    </script>
@endpush
