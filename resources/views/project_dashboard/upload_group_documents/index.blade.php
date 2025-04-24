@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Upload Group of Documents')
@section('content')

    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">
    <style>
        .date{
            background-color:#fff !important;
        }
    </style>
    <style>
        .uppy-Dashboard-inner {
            width: 100%;
            height: 360px;
        }


        body {
            height: 100vh;
            /* تحديد ارتفاع الصفحة بنسبة لحجم الشاشة */
            overflow: hidden;
            /* منع التمرير */
        }

        .options {
            overflow-y: auto;
            overflow-x: hidden;
            height: calc(500px - 40px);
        }

        #docs_list {
            overflow-y: auto;
            overflow-x: hidden;
            height: calc(420px - 40px);
        }

        .options::-webkit-scrollbar {
            width: 4px;
        }

        .options::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .options::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .options::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        #docs_list::-webkit-scrollbar {
            width: 4px;
        }

        #docs_list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #docs_list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        #docs_list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .uppy-StatusBar-actions {
            justify-content: center;
        }

        .rotate-90 {
            transform: rotate(90deg);

        }

        .icon {
            transition: transform 0.1s ease;
        }

        /* Optionally, you can add styling to smooth the rotation */
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
            height: calc(300px - 40px);
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
            width: 3% !important;
        }



        .table-container th:nth-child(2),
        .table-container td:nth-child(2) {
            width: 10% !important;
        }

        .table-container th:nth-child(3),
        .table-container td:nth-child(3) {
            width: 13% !important;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 33% !important;
        }

        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 11% !important;
        }

        .table-container th:nth-child(6),
        .table-container td:nth-child(6) {
            width: 15% !important;
        }

        .table-container th:nth-child(7),
        .table-container td:nth-child(7) {
            width: 15% !important;
        }

        .table-container th:nth-child(8),
        .table-container td:nth-child(8) {
            width: 10% !important;
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

        .not_confirmed {
            background-color: #ff9999 !important;
        }

        /* #dataTable-1_wrapper {
                                                                                                                                        max-height:650px;
                                                                                                                                    } */
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h2 mb-0 page-title">Upload Group Of Documents</h2>
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
                        <label for="file">Upload PDF Files <span style="color: red">*</span></label>
                        <div class="card shadow mb-4">
                            <div class="card-header">
                                <strong>Uppy</strong>
                            </div>
                            <div class="card-body">
                                <div id="drag-drop-area"></div>
                            </div> <!-- .card-body -->
                        </div> <!-- .card -->
                    </div>
                    <p>Maximum Upload 200MB</p>
                </div> <!-- /.col -->
                <div class="col-md-12 d-none"id="step2">
                    <h4 class="h4 mb-0 page-title">Get the information</h4>
                    <div class="form-group mb-3 mt-3">
                        <div class="row" style="padding-right: 15px;padding-left: 15px;">

                            <div class="col-md-3" style="padding-left: 0px;">
                                <div class="options"
                                    style="border: 1px solid rgba(0, 0, 0, .125);border-radius: .25rem;    box-shadow: 0 5px 15px 0 rgba(0, 0, 0, .15);">
                                    <a href="#reference_section" data-toggle="collapse" aria-expanded="false"
                                        class="nav-link">
                                        <div class="row link_section" style="padding-right: 15px;padding-left: 15px;">
                                            <i class="fe fe-play fe-16 icon" id="reference-icon"></i>
                                            <div>
                                                <span class="ml-1 item-text">Reference</span>
                                            </div>
                                        </div>


                                    </a>
                                    <div class="collapse pl-4" id="reference_section"
                                        style="margin-left: 0.75rem !important;width:90%;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Start</label>
                                                <input type="number" id="reference_start_from" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label>End</label>
                                                <input type="number" id="reference_end_to" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="Subject">Replace Separator</label>
                                            <input type="text" id="reference_separator" class="form-control">
                                        </div>
                                        <div class="row"
                                            style="margin-left: 0.1rem !important;margin-right: 0.1rem !important;">
                                            <button type="button" class="btn  mr-1 btn-outline-primary"id="apply_reference"
                                                style="width: 49%">Apply</button>
                                            <button type="button" id="clear_reference"class="btn btn-outline-warning"
                                                style="width: 49%">Clear</button>

                                        </div>
                                        <div class="form-group mt-2"
                                            style="display: flex; align-items: center;margin-left: -1.5rem;">
                                            <hr style="flex: 1; margin: 0;">
                                        </div>
                                    </div>
                                    <a href="#date_section" data-toggle="collapse" aria-expanded="false" class="nav-link">
                                        <div class="row link_section" style="padding-right: 15px;padding-left: 15px;">
                                            <i class="fe fe-play fe-16 icon"></i>
                                            <div>
                                                <span class="ml-1 item-text">Date</span>
                                            </div>
                                        </div>


                                    </a>
                                    <div class="collapse pl-4" id="date_section"
                                        style="margin-left: 0.75rem !important;width:90%;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Start</label>
                                                <input type="number" id="date_start_from" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label>End</label>
                                                <input type="number" id="date_end_to" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label>Format</label>

                                            <select class="form-control" id="date_formate">
                                                <option value="d.m.y">DD.MM.YY</option>
                                                <option value="d.M.y">DD.MMM.YY </option>
                                                <option value="d.F.y">DD.MMMM.YY</option>
                                                <option value="y.m.d">YY.MM.DD</option>
                                                <option value="y.M.d">YY.MMM.DD</option>
                                                <option value="y.F.d">YY.MMMM.DD</option>
                                                <option value="d.m.Y">DD.MM.YYYY</option>
                                                <option value="d.M.Y">DD.MMM.YYYY</option>
                                                <option value="d.F.Y">DD.MMMM.YYYY</option>
                                                <option value="Y.m.d">YYYY.MM.DD</option>
                                                <option value="Y.M.d">YYYY.MMM.DD</option>
                                                <option value="Y.F.d">YYYY.MMMM.DD</option>
                                            </select>
                                        </div>
                                        <div class="row"
                                            style="margin-left: 0.1rem !important;margin-right: 0.1rem !important;">
                                            <button type="button" class="btn  mr-1 btn-outline-primary"id="apply_date"
                                                style="width: 49%">Apply</button>
                                            <button type="button" id="clear_date"class="btn btn-outline-warning"
                                                style="width: 49%">Clear</button>

                                        </div>
                                        <div class="form-group mt-2"
                                            style="display: flex; align-items: center;margin-left: -1.5rem;">
                                            <hr style="flex: 1; margin: 0;">
                                        </div>
                                    </div>
                                    <a href="#subject_section" data-toggle="collapse" aria-expanded="false"
                                        class="nav-link">
                                        <div class="row link_section" style="padding-right: 15px;padding-left: 15px;">
                                            <i class="fe fe-play fe-16 icon"></i>
                                            <div>
                                                <span class="ml-1 item-text">Subject</span>
                                            </div>
                                        </div>


                                    </a>
                                    <div class="collapse pl-4" id="subject_section"
                                        style="margin-left: 0.75rem !important;width:90%;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Start</label>
                                                <input type="number" id="subject_start_from" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label>End</label>
                                                <input type="number" id="subject_end_to" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row  mt-3"
                                            style="margin-left: 0.1rem !important;margin-right: 0.1rem !important;">
                                            <button type="button" class="btn  mr-1 btn-outline-primary"id="apply_subject"
                                                style="width: 49%">Apply</button>
                                            <button type="button" id="clear_subject"class="btn btn-outline-warning"
                                                style="width: 49%">Clear</button>

                                        </div>
                                        <div class="form-group mt-2"
                                            style="display: flex; align-items: center;margin-left: -1.5rem;">
                                            <hr style="flex: 1; margin: 0;">
                                        </div>
                                    </div>
                                    <a href="#type_section" data-toggle="collapse" aria-expanded="false"
                                        class="nav-link">
                                        <div class="row link_section" style="padding-right: 15px;padding-left: 15px;">
                                            <i class="fe fe-play fe-16 icon"></i>
                                            <div>
                                                <span class="ml-1 item-text">Type</span>
                                            </div>
                                        </div>


                                    </a>
                                    <div class="collapse pl-4" id="type_section"
                                        style="margin-left: 0.75rem !important;width:90%;">
                                        <div class="form-group">
                                            <label>Documents Type</label>

                                            <select class="form-control" id="docType">
                                                <option value="" disabled selected>Select Type</option>
                                                @foreach ($documents_types as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row  mt-3"
                                            style="margin-left: 0.1rem !important;margin-right: 0.1rem !important;">
                                            <button type="button" class="btn  mr-1 btn-outline-primary"id="apply_type"
                                                style="width: 49%">Apply</button>
                                            <button type="button" id="clear_type"class="btn btn-outline-warning"
                                                style="width: 49%">Clear</button>

                                        </div>
                                        <div class="form-group mt-2"
                                            style="display: flex; align-items: center;margin-left: -1.5rem;">
                                            <hr style="flex: 1; margin: 0;">
                                        </div>
                                    </div>
                                    <a href="#assign_section" data-toggle="collapse" aria-expanded="false"
                                        class="nav-link">
                                        <div class="row link_section" style="padding-right: 15px;padding-left: 15px;">
                                            <i class="fe fe-play fe-16 icon"></i>
                                            <div>
                                                <span class="ml-1 item-text">Assign Document to File</span>
                                            </div>
                                        </div>


                                    </a>
                                    <div class="collapse pl-4" id="assign_section"
                                        style="margin-left: 0.75rem !important;width:90%;">
                                        <div class="form-group">
                                            <label>Folder</label>

                                            <select class="form-control" id="folder_id">
                                                <option value="" disabled selected>Select Folder</option>
                                                @foreach ($folders as $key => $name)
                                                    <option value="{{ $key }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group d-none files_">
                                            <label>File</label>

                                            <select class="form-control" id="docNewFile">
                                                <option value="" disabled selected>Select file</option>

                                            </select>
                                        </div>
                                        <div class="row  mt-3"
                                            style="margin-left: 0.1rem !important;margin-right: 0.1rem !important;">
                                            <button type="button"
                                                class="btn  mr-1 btn-outline-primary"id="apply_assign_to_file"
                                                style="width: 49%">Apply</button>
                                            <button type="button"
                                                id="clear_assign_to_file"class="btn btn-outline-warning"
                                                style="width: 49%">Clear</button>

                                        </div>
                                        <div class="form-group mt-2"
                                            style="display: flex; align-items: center;margin-left: -1.5rem;">
                                            <hr style="flex: 1; margin: 0;">
                                        </div>
                                    </div>
                                    <a href="#analyzedBy_section" data-toggle="collapse" aria-expanded="false"
                                        class="nav-link">
                                        <div class="row link_section" style="padding-right: 15px;padding-left: 15px;">
                                            <i class="fe fe-play fe-16 icon"></i>
                                            <div>
                                                <span class="ml-1 item-text">To be Analyzed By</span>
                                            </div>
                                        </div>


                                    </a>
                                    <div class="collapse pl-4" id="analyzedBy_section"
                                        style="margin-left: 0.75rem !important;width:90%;">
                                        <div class="form-group">
                                            <label>Users</label>

                                            <select class="form-control" id="doc_analyzedBy">
                                                <option value="" disabled selected>Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row  mt-3"
                                            style="margin-left: 0.1rem !important;margin-right: 0.1rem !important;">
                                            <button type="button"
                                                class="btn  mr-1 btn-outline-primary"id="apply_analyzedBy"
                                                style="width: 49%">Apply</button>
                                            <button type="button" id="clear_analyzedBy"class="btn btn-outline-warning"
                                                style="width: 49%">Clear</button>

                                        </div>
                                        <div class="form-group mt-2"
                                            style="display: flex; align-items: center;margin-left: -1.5rem;">
                                            <hr style="flex: 1; margin: 0;">
                                        </div>
                                    </div>
                                    <a href="#Correspondence_section" data-toggle="collapse" aria-expanded="false"
                                        class="nav-link">
                                        <div class="row link_section" style="padding-right: 15px;padding-left: 15px;">
                                            <i class="fe fe-play fe-16 icon"></i>
                                            <div>
                                                <span class="ml-1 item-text">Correspondence</span>
                                            </div>
                                        </div>


                                    </a>
                                    <div class="collapse pl-4" id="Correspondence_section"
                                        style="margin-left: 0.75rem !important;width:90%;">
                                        <div class="form-group">
                                            <label>From</label>

                                            <select class="form-control" id="doc_from">
                                                <option value="" disabled selected>Select Stakeholder</option>
                                                @foreach ($stake_holders as $stake_holder)
                                                    <option value="{{ $stake_holder->id }}">{{ $stake_holder->narrative }}
                                                        -
                                                        {{ $stake_holder->role }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>To</label>

                                            <select class="form-control" id="doc_to">
                                                <option value="" disabled selected>Select Stakeholder</option>
                                                @foreach ($stake_holders as $stake_holder)
                                                    <option value="{{ $stake_holder->id }}">
                                                        {{ $stake_holder->narrative }}
                                                        -
                                                        {{ $stake_holder->role }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row  mt-3"
                                            style="margin-left: 0.1rem !important;margin-right: 0.1rem !important;">
                                            <button type="button"
                                                class="btn  mr-1 btn-outline-primary"id="apply_Correspondence"
                                                style="width: 49%">Apply</button>
                                            <button type="button"
                                                id="clear_Correspondence"class="btn btn-outline-warning"
                                                style="width: 49%">Clear</button>

                                        </div>
                                        <div class="form-group mt-2"
                                            style="display: flex; align-items: center;margin-left: -1.5rem;">
                                            <hr style="flex: 1; margin: 0;">
                                        </div>
                                    </div>
                                    <a href="#Revision_section" data-toggle="collapse" aria-expanded="false"
                                        class="nav-link">
                                        <div class="row link_section" style="padding-right: 15px;padding-left: 15px;">
                                            <i class="fe fe-play fe-16 icon"></i>
                                            <div>
                                                <span class="ml-1 item-text">Revision</span>
                                            </div>
                                        </div>


                                    </a>
                                    <div class="collapse pl-4" id="Revision_section"
                                        style="margin-left: 0.75rem !important;width:90%;">
                                        <div class="form-group">

                                            <input type="text" id="doc_revision" class="form-control"
                                                placeholder="Rev">
                                        </div>
                                        <div class="row  mt-3"
                                            style="margin-left: 0.1rem !important;margin-right: 0.1rem !important;">
                                            <button type="button"
                                                class="btn  mr-1 btn-outline-primary"id="apply_revision"
                                                style="width: 49%">Apply</button>
                                            <button type="button" id="clear_revision"class="btn btn-outline-warning"
                                                style="width: 49%">Clear</button>

                                        </div>
                                        <div class="form-group mt-2"
                                            style="display: flex; align-items: center;margin-left: -1.5rem;">
                                            <hr style="flex: 1; margin: 0;">
                                        </div>
                                    </div>
                                    <a href="#Note_section" data-toggle="collapse" aria-expanded="false"
                                        class="nav-link">
                                        <div class="row link_section" style="padding-right: 15px;padding-left: 15px;">
                                            <i class="fe fe-play fe-16 icon"></i>
                                            <div>
                                                <span class="ml-1 item-text">Note</span>
                                            </div>
                                        </div>


                                    </a>
                                    <div class="collapse pl-4" id="Note_section"
                                        style="margin-left: 0.75rem !important;width:90%;">
                                        <div class="form-group">

                                            <input type="text" id="doc_note" class="form-control"
                                                placeholder="Note">
                                        </div>
                                        <div class="row  mt-3"
                                            style="margin-left: 0.1rem !important;margin-right: 0.1rem !important;">
                                            <button type="button" class="btn  mr-1 btn-outline-primary"id="apply_note"
                                                style="width: 49%">Apply</button>
                                            <button type="button" id="clear_note"class="btn btn-outline-warning"
                                                style="width: 49%">Clear</button>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div
                                class="col-md-9"style="border: 1px solid rgba(0, 0, 0, .125);border-radius: .25rem;    box-shadow: 0 5px 15px 0 rgba(0, 0, 0, .15);">
                                <div class="row align-items-center"
                                    style="margin-top: 0px !important; justify-content: center;">
                                    <div class="col">
                                        <label class="mt-2"><B><i class="fe fe-file fe-16"
                                                    style="margin-right: 5px;"></i>List
                                                of
                                                Documents</B></label>
                                    </div>
                                    <div class="col-auto">
                                        <div class="custom-control custom-checkbox mt-1 ml-1 mb-1"
                                            style="display: flex;align-items: center;">
                                            <input type="checkbox" class="custom-control-input"id="select-all">
                                            <label class="custom-control-label doc_name" for="select-all">Select
                                                All</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" style="display: flex; align-items: center;">
                                    <hr style="flex: 1; margin: 0;">
                                </div>
                                <div id="docs_list" class="mb-2">

                                </div>



                            </div>
                        </div>
                    </div>

                    <div class="text-right" style="margin-top: 10px;">
                        <button type="button"
                            class="btn btn-outline-secondary"onclick="window.location.href='/project'">Cancel</button>
                        <button type="button" id="GetData" class="btn  btn-outline-success">Get
                            Date</button>

                    </div>
                </div>
                <div class="col-md-12 d-none" id="step3">
                    <h4 class="h4 mb-0 page-title">Preview and Editing</h4>
                    <div id="table__">

                    </div>
                    <!-- Table container with fixed height -->


                    <div class="text-right" style="margin-top: 10px;">
                        <button type="button"
                            class="btn btn-outline-secondary"onclick="window.location.href='/project'">Cancel</button>
                        <button type="button" id="startImport" class="btn  btn-outline-success" disabled>Start
                            Import</button>

                    </div>
                </div>
                <div class="col-md-12 d-none" id="step4">
                    <h4 class="h4 mb-0 page-title">Import Report</h4>
                    <div class="form-group mt-2" style="display: flex; align-items: center;">
                        <hr style="flex: 1; margin: 0;">
                    </div>
                    <div id="report_content">

                    </div>
                    
                    <div class="text-right" style="margin-top: 10px;">
                        <button type="button"
                            class="btn btn-outline-secondary"onclick="window.location.href='/project'">Cancel</button>
                        <button type="button" class="btn  btn-outline-success" onclick="window.location.href='/project/all-documents'">Close</button>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script> --}}
    <script>
        $(document).ready(function() {

            $('#startImport').on('click', function() {
                $.ajax({
                    url: '{{ route('group-documents.import_group_documents') }}', // Replace with your route
                    type: 'GET',
                    data: {
                        _token: '{{ csrf_token() }}',

                    },
                    success: function(response) {
                        $('#report_content').append(response.html)
                        $('#step3').addClass('d-none');
                        $('#step4').removeClass('d-none');

                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error('Error saving data:',
                            error);
                        alert(
                            'Error saving data. Please try again.'
                        );
                    }
                });
            });

            document.getElementById('select-all').addEventListener('change', function() {
                const visibleCheckboxes = document.querySelectorAll(
                    '.docName_checkBox');

                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });

            });

            document.querySelectorAll('.link_section').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const icon = toggle.querySelector('.icon');
                    icon.classList.toggle('rotate-90');
                });
            });
            let pdfUploaded = false;

            function checkUploadStatus() {
                if (pdfUploaded) {
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

            $('#GetData').click(function() {
                // Collect all document data
                var documents = [];

                $('.document-container').each(function() {
                    var docId = $(this).data('id');
                    var docData = {
                        doc_id: $(this).find('.doc_id_value').val(),
                        reference: $(this).find('.reference_value').val(),
                        date: $(this).find('.date_value').val(),
                        subject: $(this).find('.subject_value').val(),
                        type: $(this).find('.type_value').val(),
                        assign_to_file_id: $(this).find('.assign_to_file_id_value').val(),
                        analyzed_by: $(this).find('.analyzed_by_value').val(),
                        from: $(this).find('.from_value').val(),
                        to: $(this).find('.to_value').val(),
                        revision: $(this).find('.revision_value').val(),
                        notes: $(this).find('.notes_value').val()
                    };

                    documents.push(docData);
                });

                // Send data via AJAX
                $.ajax({
                    url: '{{ route('group-documents.saveDocuments') }}', // Replace with your route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        documents: documents
                    },
                    success: function(response) {
                        // Handle success response
                        $('#table__').append(response.html);
                        $('#step2').addClass('d-none');
                        $('#step3').removeClass('d-none');
                        const checkDocumentsInterval = setInterval(function() {
                            $.ajax({
                                url: '{{ route('group-documents.check_test_documents') }}', // Replace with your route
                                type: 'GET',
                                data: {
                                    _token: '{{ csrf_token() }}',

                                },
                                success: function(response) {
                                    const rows = document.querySelectorAll(
                                        'tr[data-id]');
                                    const validIds = new Set(response
                                        .IDs); // assume numbers

                                    rows.forEach(row => {
                                        const rowId = Number(row
                                            .dataset.id
                                        ); // convert to number


                                        if (validIds.has(rowId)) {
                                            row.style
                                                .backgroundColor =
                                                ''; // keep this row
                                        }
                                    });
                                    const startImportBtn = document
                                        .getElementById('startImport');
                                    if (startImportBtn) {
                                        startImportBtn.disabled = rows
                                            .length !== response.IDs.length;
                                    }
                                    if (rows.length === response.IDs
                                        .length) {
                                        clearInterval(
                                            checkDocumentsInterval);
                                    }

                                    // console.log('Data saved successfully', response);
                                    // alert('Data saved successfully!');
                                },
                                error: function(xhr, status, error) {
                                    // Handle error
                                    console.error('Error saving data:',
                                        error);
                                    alert(
                                        'Error saving data. Please try again.'
                                    );
                                }
                            });

                        }, 2500);
                        // console.log('Data saved successfully', response);
                        // alert('Data saved successfully!');
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error('Error saving data:', error);
                        alert('Error saving data. Please try again.');
                    }
                });
            });


            var uptarg = document.getElementById('drag-drop-area');
            if (uptarg) {
                var uppy = Uppy.Core({
                    restrictions: {
                        allowedFileTypes: ['application/pdf'],
                        maxNumberOfFiles: 100, // Optional: limit number of files
                        // maxFileSize: 10 * 1024 * 1024 // Optional: 10MB limit
                    }
                }).use(Uppy.Dashboard, {
                    inline: true,
                    target: uptarg,
                    proudlyDisplayPoweredByUppy: false,
                    theme: 'dark',
                    width: 770,
                    height: 210,
                    note: 'PDF files only (max 50MB each)',
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
                        url: '/group-documents/upload-multi-files',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,

                        success: function(response) {
                            if (response.success) {

                                pdfUploaded = true;
                                $('#docs_list').append(response.html);
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

            $('#apply_note').on('click', function() {
                let doc_note = $('#doc_note').val();

                if (doc_note == '') {
                    alert("Please Enter Note.");
                    return;
                }

                $('.docName_checkBox:checked').each(function() {
                    let parentDiv = $(this).closest('.mb-2');

                    parentDiv.find('.notes_value').val(doc_note);
                    parentDiv.find('.document_note').text(doc_note);
                });
            });
            $('#clear_note').on('click', function() {
                $('#doc_note').val('');

            });

            $('#apply_revision').on('click', function() {
                let doc_revision = $('#doc_revision').val();

                if (doc_revision == '') {
                    alert("Please Enter Revision.");
                    return;
                }

                $('.docName_checkBox:checked').each(function() {
                    let parentDiv = $(this).closest('.mb-2');

                    parentDiv.find('.revision_value').val(doc_revision);
                    parentDiv.find('.document_rev').text(doc_revision);
                });
            });
            $('#clear_revision').on('click', function() {
                $('#doc_revision').val('');

            });

            $('#apply_Correspondence').on('click', function() {
                let doc_from = parseInt($('#doc_from').val());
                let doc_to = parseInt($('#doc_to').val());

                if (isNaN(doc_from) && isNaN(doc_to)) {
                    alert("Please select Stakeholders.");
                    return;
                }

                $('.docName_checkBox:checked').each(function() {
                    let parentDiv = $(this).closest('.mb-2');

                    parentDiv.find('.from_value').val(doc_from);
                    parentDiv.find('.to_value').val(doc_to);
                    parentDiv.find('.document_from').text($('#doc_from option:selected').text());
                    parentDiv.find('.document_to').text($('#doc_to option:selected').text());
                });
            });
            $('#clear_Correspondence').on('click', function() {
                $('#doc_from').val('');
                $('#doc_to').val('');

            });

            $('#apply_analyzedBy').on('click', function() {
                let doc_analyzedBy = parseInt($('#doc_analyzedBy').val());

                if (isNaN(doc_analyzedBy)) {
                    alert("Please select User.");
                    return;
                }

                $('.docName_checkBox:checked').each(function() {
                    let parentDiv = $(this).closest('.mb-2');

                    parentDiv.find('.analyzed_by_value').val(doc_analyzedBy);
                    parentDiv.find('.document_analyzedBy').text($('#doc_analyzedBy option:selected')
                        .text());
                });
            });
            $('#clear_analyzedBy').on('click', function() {
                $('#doc_analyzedBy').val('');

            });

            $('#folder_id').change(function() {
                let folderId = $(this).val();

                if (!folderId) return; // Stop if no folder is selected

                $.ajax({
                    url: '/project/folder/get-files/' +
                        folderId, // Adjust the route to your API endpoint
                    type: 'GET',
                    success: function(response) {
                        let fileDropdown = $('#docNewFile');
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
            $('#apply_assign_to_file').on('click', function() {
                let docNewFile = parseInt($('#docNewFile').val());

                if (isNaN(docNewFile)) {
                    alert("Please select file.");
                    return;
                }

                $('.docName_checkBox:checked').each(function() {
                    let parentDiv = $(this).closest('.mb-2');

                    parentDiv.find('.assign_to_file_id_value').val(docNewFile);
                    parentDiv.find('.doc_assign_file').text($('#folder_id option:selected').text() +
                        ' --> ' + $('#docNewFile option:selected').text());
                });
            });
            $('#clear_assign_to_file').on('click', function() {
                $('#docNewFile').val('');
                $('#folder_id').val('');
                let fileDropdown = $('#docNewFile');
                fileDropdown.closest('.files_').addClass(
                    'd-none');
            });


            $('#apply_reference').on('click', function() {
                let start = parseInt($('#reference_start_from').val()) - 1;
                let end = parseInt($('#reference_end_to').val());
                let separator = $('#reference_separator').val();

                if (isNaN(start) || isNaN(end)) {
                    alert("Please enter valid start and end positions.");
                    return;
                }

                $('.docName_checkBox:checked').each(function() {
                    let parentDiv = $(this).closest('.mb-2');
                    let fullText = parentDiv.find('.doc_name')
                        .text(); // e.g., "25.04.25_148_lll_2025test2"

                    // Get substring based on start and end
                    let extracted = fullText.substring(start, end);

                    if (separator !== '') {
                        // Replace any non-alphanumeric character with the new separator
                        extracted = extracted.replace(/[^a-zA-Z0-9\']/g, separator);
                    }

                    // Set the result in the .doc_ref label
                    parentDiv.find('.doc_ref').text(extracted);
                    parentDiv.find('.reference_value').val(extracted);
                });
            });
            $('#clear_reference').on('click', function() {
                $('#reference_start_from').val('');
                $('#reference_end_to').val('');
                $('#reference_separator').val('');
            });

            $('#apply_date').on('click', function() {
                let startdate = parseInt($('#date_start_from').val()) - 1;
                let enddate = parseInt($('#date_end_to').val());
                let formate = $('#date_formate').val();

                if (isNaN(startdate) || isNaN(enddate)) {
                    alert("Please enter valid start and end positions.");
                    return;
                }
                $('.docName_checkBox:checked').each(function() {
                    let parentDiv = $(this).closest('.mb-2');
                    let fullText = parentDiv.find('.doc_name')
                        .text(); // e.g., "25.04.25_148_lll_2025test2"

                    // Get substring based on start and end
                    let extracted = fullText.substring(startdate, enddate);

                    if (formate !== '') {
                        $.ajax({
                            url: "/formate_date",
                            type: "GET",
                            data: {
                                date: extracted,
                                formate: formate,
                                _token: "{{ csrf_token() }}" // If using Laravel CSRF protection
                            },
                            success: function(response) {
                                // console.log("Headers received:", response.headers)
                                if (response.success) {
                                    parentDiv.find('.doc_date').text(response
                                        .parsedDate);
                                    parentDiv.find('.date_value').val(response
                                        .formattedDate);
                                } else {
                                    alert(response.message +
                                        " Please enter valid start and end positions of document '" +
                                        fullText + "'.");
                                }
                                // Handle response (e.g., display headers)
                            },
                            error: function(xhr, status, error) {
                                console.error("Error:", error);
                                alert("Failed to retrieve headers.");
                            }
                        });
                    }

                    // Set the result in the .doc_ref label

                });
            });
            $('#clear_date').on('click', function() {
                $('#date_start_from').val('');
                $('#date_end_to').val('');

            });


            $('#apply_subject').on('click', function() {
                let startSubject = parseInt($('#subject_start_from').val()) - 1;
                let endSubject = parseInt($('#subject_end_to').val());


                if (isNaN(startSubject) || isNaN(endSubject)) {
                    alert("Please enter valid start and end positions.");
                    return;
                }

                $('.docName_checkBox:checked').each(function() {
                    let parentDiv = $(this).closest('.mb-2');
                    let fullText = parentDiv.find('.doc_name')
                        .text(); // e.g., "25.04.25_148_lll_2025test2"

                    // Get substring based on start and end
                    let extracted = fullText.substring(startSubject, endSubject);



                    // Set the result in the .doc_ref label
                    parentDiv.find('.doc_subject').text(extracted);
                    parentDiv.find('.subject_value').val(extracted);
                });
            });
            $('#clear_subject').on('click', function() {
                $('#subject_start_from').val('');
                $('#subject_end_to').val('');
            });
            $('#apply_type').on('click', function() {
                let docType = parseInt($('#docType').val());

                if (isNaN(docType)) {
                    alert("Please select document type.");
                    return;
                }

                $('.docName_checkBox:checked').each(function() {
                    let parentDiv = $(this).closest('.mb-2');

                    parentDiv.find('.type_value').val(docType);
                    parentDiv.find('.doc_type').text($('#docType option:selected').text());
                });
            });

            $('#clear_type').on('click', function() {
                $('#docType').val('');
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
