@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Files')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
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



        .custom-context-menu {
            display: none;
            position: absolute;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 8px 0;
            width: 180px;
            list-style: none;
            z-index: 1000;
        }


        .custom-context-menu li {
            padding: 10px 15px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s ease-in-out;
        }


        .custom-context-menu li:hover {
            background: #f5f5f5;
        }


        .custom-context-menu li i {
            font-size: 16px;
            color: #007bff;
            margin-bottom: 5px;
            margin-right: 5px;
        }


        .custom-context-menu li a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            width: 100%;

        }

        .custom-context-menu li a:hover {
            text-decoration: none;
        }
    </style>
    <style>
        .dot {
            height: 25px;
            width: 25px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
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
    </style>
    <style>
        .date {
            background-color: #fff !important;
        }

        .datennn {
            background-color: #fff !important;
        }

        .fghd {
            margin-right: 2%;
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
            width: 1% !important;
        }



        .table-container th:nth-child(2),
        .table-container td:nth-child(2) {
            width: 12% !important;
        }

        .table-container th:nth-child(3),
        .table-container td:nth-child(3) {
            width: 16% !important;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 6.5% !important;
        }

        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 7.7% !important;
        }

        .table-container th:nth-child(6),
        .table-container td:nth-child(6) {
            width: 10.6% !important;
        }

        .table-container th:nth-child(7),
        .table-container td:nth-child(7) {
            width: 4% !important;
        }

        .table-container th:nth-child(8),
        .table-container td:nth-child(8) {
            width: 8.8% !important;
        }

        .table-container th:nth-child(9),
        .table-container td:nth-child(9) {
            width: 8.8% !important;
        }

        .table-container th:nth-child(10),
        .table-container td:nth-child(10) {
            width: 3% !important;
        }

        .table-container th:nth-child(11),
        .table-container td:nth-child(11) {
            width: 6.8% !important;
        }

        .table-container th:nth-child(12),
        .table-container td:nth-child(12) {
            width: 13.8% !important;
        }

        .table-container th:nth-child(13),
        .table-container td:nth-child(13) {
            width: 3% !important;
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
    <style>
        .assignment-list1 {
            max-width: 600px;
            margin: 0px auto;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            gap: 24px;
            /* مسافة بين المجموعات */
        }

        .assignment-group1 {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .ref-code1 {
            font-weight: bold;
            font-size: 16px;
            color: #16a34a;

            display: block;
        }

        .ref-code2 {
            font-weight: bold;
            font-size: 16px;
            color: #a31616;

            display: block;
        }

        .assignment-item1 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .ref-link1 {
            font-size: 15px;
            font-weight: 600;
            color: #16a34a;
            margin-bottom: 0px;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .ref-link1:hover {
            color: #0f9d58;
            text-decoration: underline;
        }

        .assign-btn1,
        .replace-btn1 {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 110px;
        }

        .assign-btn1 {
            background: #0b74ff;
            color: #fff;
            border-color: #0b74ff;
        }

        .replace-btn1 {
            background: #f59e0b;
            color: #fff;
            border-color: #f59e0b;
        }

        .assign-btn1:hover {
            background: #0856c0;
        }

        .replace-btn1:hover {
            background: #d97706;
        }
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
            <h3 class="h3 mb-0 page-title"><a
                    href="{{ route('switch.folder', $file->folder->id) }}">{{ $file->folder->name }}</a><span
                    id="chevronIcon"
                    class="fe fe-24 fe-chevrons-right"style="position: relative; top: 2px;"></span>{{ $file->name }}</h3>
        </div>
        <div class="col-auto">
            <button type="button" class="btn mb-2 dropdown-toggle btn-success"data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">File
                Action</button>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="javascript:void(0);" data-file-id="{{ $file->slug }}" id="export-allDoc">
                    Export
                </a>
                <a class="dropdown-item" href="javascript:void(0);" data-file-id="{{ $file->slug }}"
                    id="download-allDoc">Download Documents</a>
                <a class="dropdown-item" href="javascript:void(0);" id="addNote">
                    Add Note/Activity
                </a>
                <a class="dropdown-item" href="javascript:void(0);" id="exportGanttChart">
                    Export Gantt Chart
                </a>
                <a class="dropdown-item" href="javascript:void(0);" id="checkThreads">
                    Check Threads
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

                                    <th><label id="all_with_tags"><span class="fe fe-23 fe-volume-2"
                                                style="color: rgb(169, 169, 169);cursor: pointer;"></span></label>
                                        <label id="all_for_claim"
                                            style=" background-color: rgb(169, 169, 169); width:15px;height:15px;border-radius: 50%;text-align:center;cursor: pointer;"><span>C</span></label>
                                        <label id="all_for_notice"
                                            style=" background-color: rgb(169, 169, 169); width:15px;height:15px;border-radius: 50%;text-align:center;cursor: pointer;"><span>N</span></label>
                                        <label id="all_for_timeline"
                                            style=" background-color: rgb(169, 169, 169); width:15px;height:15px;border-radius: 50%;text-align:center;cursor: pointer;"><span>G</span></label>
                                        <label id="all_blue_flag" style="margin-right:0.02rem;cursor: pointer;"
                                            title="Blue Flags">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
                                        <label id="all_red_flag" style="cursor: pointer;" title="Red Flags">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
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
                                    <tr id="dddd_{{ $document->id }}"
                                        @if ($specific_file_doc == $document->id) style="background-color: #AFEEEE" class="specific_file_doc" @endif>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input @if ($document->note_id == null) row-checkbox @endif"data-file-id="{{ $document->id }}"
                                                    id="checkbox-{{ $document->id }}" value="{{ $document->id }}"
                                                    @if ($document->document_id == null) disabled @endif>
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $document->id }}"></label>
                                            </div>
                                        </td>
                                        <td>

                                            <label class="with_tag @if ($document->note_id == null && count($document->tags) != 0) active @endif"><span
                                                    class="fe fe-23 fe-volume-2"
                                                    style="@if ($document->note_id == null && count($document->tags) != 0) @if ($document->tags->contains('is_notice', 1))
            color: rgb(255, 0, 0); /* red */
        @else
            color: rgb(45, 209, 45); /* green */ @endif
@else
color: rgb(169, 169, 169); /* gray */
    @endif"></span></label>
                                            <label
                                                class="for_claim for-claim-btn222 @if ($document->forClaim == '1') active @endif"
                                                style="@if ($document->forClaim == '1') background-color: rgb(45, 209, 45); @else background-color: rgb(169, 169, 169); @endif width:15px;height:15px;border-radius: 50%;text-align:center;cursor: pointer;"
                                                data-document-id="{{ $document->id }}"
                                                data-action-type="forClaim"><span>C</span></label>
                                            <label
                                                class="for_notice for-claim-btn222 @if ($document->forLetter == '1') active @endif"
                                                style="@if ($document->forLetter == '1') background-color: rgb(45, 209, 45); @else background-color: rgb(169, 169, 169); @endif width:15px;height:15px;border-radius: 50%;text-align:center;cursor: pointer;"
                                                data-document-id="{{ $document->id }}"
                                                data-action-type="forLetter"><span>N</span></label>
                                            <label
                                                class="for_timeline for-claim-btn222 @if ($document->forChart == '1') active @endif"
                                                style=" @if ($document->forChart == '1') background-color: rgb(45, 209, 45); @else background-color: rgb(169, 169, 169); @endif width:15px;height:15px;border-radius: 50%;text-align:center;cursor: pointer;"
                                                data-document-id="{{ $document->id }}"
                                                data-action-type="forChart"><span>G</span></label>
                                            <label
                                                class="blue_flag change-flag @if (in_array($document->id, $array_blue_flags ?? [])) active @endif"
                                                style="margin-right:0.02rem;cursor: pointer;"data-document-id="{{ $document->id }}"
                                                data-flag="blue" title="Blue Flag">
                                                @if (in_array($document->id, $array_blue_flags ?? []))
                                                    <i class="fa-solid fa-flag" style="color: #0000ff;"></i>
                                                @else
                                                    <i class="fa-regular fa-flag"></i>
                                                @endif

                                            </label>
                                            <label
                                                class="red_flag change-flag @if (in_array($document->id, $array_red_flags ?? [])) active @endif"
                                                data-document-id="{{ $document->id }}" data-flag="red"
                                                style="cursor: pointer;" title="Red Flag">
                                                @if (in_array($document->id, $array_red_flags ?? []))
                                                    <i class="fa-solid fa-flag"style="color: #ff0000;"></i>
                                                @else
                                                    <i class="fa-regular fa-flag"></i>
                                                @endif
                                            </label>
                                            <br>
                                            <span
                                                class="fe fe-22 @if ($document->narrative != null) fe-file-text doc_narrative @else fe-file @endif"
                                                @if ($document->narrative != null) style="cursor:pointer;" @endif
                                                data-doc-id="{{ $document->id }}"></span>
                                            <label>{{ $document->note_id == null ? $document->document->docType->name : 'Note/Activity' }}</label>


                                        </td>

                                        <td><a class="l-link"style="color:rgb(80, 78, 78);" style="color:"
                                                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('analysis', $Project_Permissions ?? [])) href="{{ route('project.file-document-first-analyses', $document->id) }}" @endif>
                                                {{ $document->document ? $document->document->subject : $document->note->subject }}</a>
                                        </td>

                                        <td>
                                            @if ($document->document)
                                                {{ $document->document->start_date ? date('d.M.y', strtotime($document->document->start_date)) : '_' }}
                                            @else
                                                {{ $document->note->start_date ? date('d.M.y', strtotime($document->note->start_date)) : '_' }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->document)
                                                {{ $document->document->end_date ? date('d.M.y', strtotime($document->document->end_date)) : '_' }}
                                            @else
                                                {{ $document->note->end_date ? date('d.M.y', strtotime($document->note->end_date)) : '_' }}
                                            @endif
                                        </td>
                                        <td @if ($document->document) ondblclick="openDocumentPdf('{{ asset($document->document->storageFile->path) }}')" @endif
                                            style="cursor: pointer;">
                                            {{ $document->document ? $document->document->reference : '_' }}</td>
                                        <td>{{ $document->document ? $document->document->revision : '_' }}</td>
                                        <td>
                                            @if ($document->document)
                                                {{ $document->document->fromStakeHolder ? $document->document->fromStakeHolder->narrative : '_' }}
                                            @else
                                                _
                                            @endif
                                        </td>
                                        <td>
                                            @if ($document->document)
                                                {{ $document->document->toStakeHolder ? $document->document->toStakeHolder->narrative : '_' }}
                                            @else
                                                _
                                            @endif
                                        </td>
                                        <td>{{ $document->sn }}</td>
                                        <td>{{ $document->document ? $document->document->status : '_' }}</td>
                                        <td>{{ $document->notes1 }}
                                        </td>
                                        <td>
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if ($document->document)
                                                    <a class="dropdown-item"
                                                        href="{{ url('/project/files_file/' . $document->file->slug . '/doc/' . $document->id . '/edit/' . $document->document->slug) }}">
                                                        Edit Document
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('project.documents.document_analysis', $document->document->slug) }}"
                                                        target="_blink">Document Analysis</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('project.file-document-first-analyses', $document->id) }}">Chronology</a>
                                                    <a class="dropdown-item copy-to-file-btn" href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}"
                                                        data-action-type="copy">Copy
                                                        To another File</a>
                                                    <a class="dropdown-item copy-to-file-btn" href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}"
                                                        data-action-type="move">Move
                                                        To another File</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('download.document', $document->id) }}">
                                                        Download Document
                                                    </a>
                                                    <a class="dropdown-item unassign-doc-btn" href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}">Unassign Document</a>
                                                    <a class="dropdown-item Check-other-assignments-btn"
                                                        href="javascript:void(0);"
                                                        data-document-id="{{ $document->document->slug }}"data-type="document">Check
                                                        other
                                                        assignments</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('project.file-documents.ocr_layer', $document->document->slug) }}"target="_blank">OCR</a>
                                                    @php
                                                        $threads = $document->document->threads
                                                            ? json_decode($document->document->threads, true)
                                                            : [];
                                                        $escapedThreads = array_map(function ($item) {
                                                            return str_replace("'", "\'", $item);
                                                        }, $threads);
                                                    @endphp
                                                    <a class="dropdown-item threadsBtn"
                                                        href="javascript:void(0);"data-document-ref="{{ $document->document->reference }}"
                                                        data-document-threads="{{ json_encode($escapedThreads) }}"
                                                        data-document-sub="{{ $document->document->subject }}">Thread</a>
                                                    <a class="dropdown-item Delete-from-CMW-btn"
                                                        href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}">Delete from CMW</a>
                                                    <a class="dropdown-item gantt-chart" href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}">Gantt Chart</a>
                                                @else
                                                    <a class="dropdown-item"
                                                        href="{{ url('/project/files_file/' . $document->file->slug . '/doc/' . $document->id . '/edit/' . $document->note->slug) }}">
                                                        Edit Document
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('project.file-document-first-analyses', $document->id) }}">Chronology</a>
                                                    <a class="dropdown-item copy-to-file-btn" href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}"
                                                        data-action-type="copy">Copy
                                                        To another File</a>
                                                    <a class="dropdown-item copy-to-file-btn" href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}"
                                                        data-action-type="move">Move
                                                        To another File</a>
                                                    <a class="dropdown-item unassign-doc-btn" href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}">Unassign Document</a>
                                                    <a class="dropdown-item Check-other-assignments-btn"
                                                        href="javascript:void(0);"
                                                        data-document-id="{{ $document->note->slug }}"data-type="note">Check
                                                        other
                                                        assignments</a>
                                                    <a class="dropdown-item Delete-from-CMW-btn"
                                                        href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}">Delete from CMW</a>
                                                    <a class="dropdown-item gantt-chart" href="javascript:void(0);"
                                                        data-document-id="{{ $document->id }}">Gantt Chart</a>
                                                @endif
                                                {{-- <a class="dropdown-item for-claim-btn" href="javascript:void(0);"
                                                    data-document-id="{{ $document->id }}"
                                                    data-action-type="forClaim">For Claim</a>
                                                <a class="dropdown-item for-claim-btn" href="javascript:void(0);"
                                                    data-document-id="{{ $document->id }}"
                                                    data-action-type="forLetter">For Notice</a>
                                                <a class="dropdown-item for-claim-btn" href="javascript:void(0);"
                                                    data-document-id="{{ $document->id }}"
                                                    data-action-type="forChart">For Gantt Chart</a> --}}
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
    <div class="modal fade" id="copyToModal" tabindex="-1" role="dialog" aria-labelledby="copyToModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="copyToModalLabel">
                        <spam id="type">Copy</spam> Document To another File
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="assigneToForm">
                        @csrf
                        <input type="hidden" id="documentId_" name="document_id">
                        <input type="hidden" id="action_type" name="action_type">
                        <div class="form-group">
                            <label for="folder_id">Select Folder</label>
                            <select class="form-control" id="folder_id" required>
                                <option value="" disabled selected>Select Folder</option>
                                @foreach ($folders as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none">
                            <label for="newFile">Select File</label>
                            <select class="form-control" id="newFile" name="file_id">
                                <option value="" disabled selected>Select File</option>

                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCopyDoc">Save</button>
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
                        <spam id="type2">Copy</spam> Selected Documents To another File
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="copyToForAllForm">
                        @csrf
                        <input type="hidden" id="documentIdss" name="document_idss">
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
    <div class="modal fade" id="CheckOtherAssignmentModal" tabindex="-1" role="dialog"
        aria-labelledby="CheckOtherAssignmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CheckOtherAssignmentModalLabel">Files to which the document is assigned
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="container">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="downloadAllDocsModal" tabindex="-1" role="dialog"
        aria-labelledby="downloadAllDocsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadAllDocsModalLabel">How do you want to name the documents
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="downloadAllDocsForm">
                        @csrf
                        <input type="hidden" id="file_id_" name="file_id_">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"checked id="forclaimdocs2"
                                name="forclaimdocs2">
                            <label class="custom-control-label" for="forclaimdocs2">For Claim Documents</label>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="folder_id">Select document naming format</label>
                            <div>

                                <div class="custom-control custom-radio">
                                    <input type="radio" id="reference_only" name="formate_type" value="reference"
                                        class="custom-control-input" required>
                                    <label class="custom-control-label" for="reference_only">Reference</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="dateAndReference" name="formate_type"
                                        class="custom-control-input" value="dateAndReference"required>
                                    <label class="custom-control-label" for="dateAndReference">YYMMDD – Reference</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="formate_type" value="formate" id="formate"
                                        class="custom-control-input"required>
                                    <label class="custom-control-label" for="formate"><span
                                            style="background-color: #4dff00"><b>Prefix SN</b></span> – [From]’s [Type]
                                        Ref-
                                        [Ref] - dated [Date]</label>
                                </div>
                            </div>

                        </div>
                        <div id="extraOptions" class="row d-none">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="row form-group mb-3">
                                    <label class="mt-1" for="Prefix">Prefix : </label>
                                    <input type="text" name="prefix" id="Prefix" class="form-control"
                                        placeholder="Perfix" value="" style="width: 85%;margin-left:2%;">
                                </div>
                                <div class="row form-group mb-3">
                                    <label class="mt-1" for="sn">Number Of Digits : </label>
                                    <input type="number" name="sn" id="sn" class="form-control"
                                        placeholder="SN" value="" style="width: 30%;margin-left:2%;">
                                </div>
                                <div class="row form-group mb-3">
                                    <label class="mt-1" for="Start">SN - Start : </label>
                                    <input type="number" name="Start" id="Start" class="form-control"
                                        placeholder="Start" value="1" style="width: 30%;margin-left:2%;"min="1"
                                        oninput="this.value = Math.max(1, this.value)">
                                </div>
                                <div class="custom-control custom-checkbox mb-3" style="padding-left: 0.5rem;">
                                    <input type="checkbox" class="custom-control-input" id="fromForL-E"
                                        name="fromForL_E"checked>
                                    <label class="custom-control-label" for="fromForL-E">"From" only for Litters &
                                        Emails</label>
                                </div>
                                <div class="row form-group mb-0">
                                    <label for="sn">In case of e-mails : </label>
                                    <div style="width: 70%;margin-left:2%;font-size: 0.8rem;">

                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="option1" name="ref_part" value="option1"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="option1">Omit Ref part</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="option2" name="ref_part"
                                                class="custom-control-input" value="option2">
                                            <label class="custom-control-label" for="option2">Keep Ref part, but replace
                                                word “Ref” with “from”</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="ref_part" value="option3" id="option3"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="option3">Keep as other types</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="downloadalldocs">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editDocInfoModal" tabindex="-1" role="dialog" aria-labelledby="editDocInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDocInfoModalLabel">Edit Basic Information of Selected Documents</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        @csrf
                        <input type="hidden" id="documentIdds" name="documentIdds">

                        <div class="form-group">
                            <label for="newDocTypeForAll">New Document Type</label>
                            <select class="form-control" id="newDocTypeForAll" name="new_doc_type_id">
                                <option value="" disabled selected>Select Type</option>
                                @foreach ($documents_types as $documents_type)
                                    <option value="{{ $documents_type->id }}">{{ $documents_type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="newFromStakeHolderForAll">From</label>
                            <select class="form-control" id="newFromStakeHolderForAll" name="new_from_id">
                                <option value="" disabled selected>Select Stake Holder</option>
                                @foreach ($stake_holders as $stake_holder)
                                    <option value="{{ $stake_holder->id }}">{{ $stake_holder->narrative }} -
                                        {{ $stake_holder->role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="newToStakeHolderForAll">To</label>
                            <select class="form-control" id="newToStakeHolderForAll" name="new_to_id">
                                <option value="" disabled selected>Select Stake Holder</option>
                                @foreach ($stake_holders as $stake_holder)
                                    <option value="{{ $stake_holder->id }}">{{ $stake_holder->narrative }} -
                                        {{ $stake_holder->role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="newOwnerForAll">New Owner</label>
                            <select class="form-control" id="newOwnerForAll" name="new_owner_id">
                                <option value="" disabled selected>Select Owner</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="editDocInfo">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Settings To Export Documents
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportForm">
                        @csrf
                        <input type="hidden" id="file_id111" name="file_id111">
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
                                id="subtitle" value="Chronology of Events">
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"checked id="forclaimdocs"
                                name="forclaimdocs">
                            <label class="custom-control-label" for="forclaimdocs">For Claim Documents</label>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="folder_id">Select Footnote format</label>
                            <div>

                                <div class="custom-control custom-radio">
                                    <input type="radio" id="reference_only2" name="formate_type2" value="reference"
                                        class="custom-control-input" required checked>
                                    <label class="custom-control-label" for="reference_only2">Reference</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="dateAndReference2" name="formate_type2"
                                        class="custom-control-input" value="dateAndReference"required>
                                    <label class="custom-control-label" for="dateAndReference2">YYMMDD – Reference</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="formate_type2" value="formate" id="formate2"
                                        class="custom-control-input"required>
                                    <label class="custom-control-label" for="formate2"><span
                                            style="background-color: #4dff00"><b>Prefix </b></span> <span
                                            style="background-color: #4dff00"><b>SN</b></span> – [From]’s [Type] Ref-
                                        [Ref] - dated [Date]</label>
                                </div>
                            </div>

                        </div>
                        <div id="extraOptions2" class="row d-none">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <div class="row form-group mb-3">
                                    <label class="mt-1" for="Prefix2">Prefix : </label>
                                    <input type="text" name="prefix2" id="Prefix2" class="form-control"
                                        placeholder="Perfix" value="Exhibit 1.1." style="width: 85%;margin-left:2%;">
                                </div>
                                <div class="row form-group mb-3">
                                    <label class="mt-1" for="sn2">SN - Number of digits : </label>
                                    <input type="number" name="sn2" id="sn2" class="form-control"
                                        placeholder="SN" value="2" style="width: 30%;margin-left:2%;">
                                </div>
                                <div class="row form-group mb-3">
                                    <label class="mt-1" for="Start">SN - Start : </label>
                                    <input type="number" name="Start" id="Start2" class="form-control"
                                        placeholder="Start" value="1" style="width: 30%;margin-left:2%;"min="1"
                                        oninput="this.value = Math.max(1, this.value)">
                                </div>
                                <div class="custom-control custom-checkbox mb-3" style="padding-left: 0.5rem;">
                                    <input type="checkbox" class="custom-control-input" id="fromForL-Ee"
                                        name="fromForL_E" checked>
                                    <label class="custom-control-label" for="fromForL-Ee">"From" only for Litters &
                                        Emails</label>
                                </div>
                                <div class="row form-group mb-0">
                                    <label for="sn2">In case of e-mails : </label>
                                    <div style="width: 70%;margin-left:2%;font-size: 0.8rem;">

                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="option12" name="ref_part2" value="option1"
                                                class="custom-control-input" checked>
                                            <label class="custom-control-label" for="option12">Omit Ref part</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="option22" name="ref_part2"
                                                class="custom-control-input" value="option2">
                                            <label class="custom-control-label" for="option22">Keep Ref part, but replace
                                                word “Ref” with “from”</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" name="ref_part2" value="option3" id="option32"
                                                class="custom-control-input">
                                            <label class="custom-control-label" for="option32">Keep as other types</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="export">Export</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ThreadsModal" tabindex="-1" role="dialog" aria-labelledby="ThreadsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 800px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ThreadsModalLabel">Thread</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="searchThreadsForm">
                        @csrf

                        <div class="form-group">
                            <label for="folder_id2">Reference</label>
                            <input type="text"class="form-control" disabled id="ref">
                        </div>
                        <div class="form-group">
                            <label for="folder_id2">Subject</label>
                            <input type="text"class="form-control" disabled id="sub">
                        </div>
                        <div class="form-group">
                            <label for="folder_id2">Threads</label>
                            <div id="threadsContainer"></div>
                        </div>
                        <div class="form-group d-none" id="docs">
                            <fieldset class="custom-fieldset">
                                <legend class="custom-legend"style="margin-bottom: 0px;">Documents</legend>
                                <div id="documentsResult">

                                </div>
                                <div class="d-none" id="docAssignments">

                                </div>
                                <div class="d-none" id="assigneToFile">
                                    <hr>
                                    <div style="text-align:center;">
                                        <p>Assign "<spam id="file_ref_"></spam>" To File</p>
                                    </div>
                                    <div class="modal-body">
                                        <form id="assigneToForm2">
                                            @csrf
                                            <input type="hidden" id="document_id_2" name="document_id_2">
                                            <div class="form-group">
                                                <label for="folder_id_2">Select Folder</label>
                                                <select class="form-control" id="folder_id_2" required>
                                                    <option value="" disabled selected>Select Folder</option>
                                                    @foreach ($folders as $key => $name)
                                                        <option value="{{ $key }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group d-none">
                                                <label for="newFile_2">Select File</label>
                                                <select class="form-control" id="newFile_2" name="file_id_2">
                                                    <option value="" disabled selected>Select File</option>

                                                </select>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="saveAssigne2">Save</button>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-labelledby="addNoteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNoteModalLabel">
                        Create New Note/Activity
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addNoteForm">
                        @csrf

                        <input type="hidden" name="file_slug" value={{ $file->slug }}>
                        <div class="form-group">
                            <label for="folder_id2">subject</label>
                            <input class="form-control" id="subject" type="text" name="subject" required>

                        </div>
                        <div class="form-group">
                            <label for="newFile2">Date</label>
                            <input required type="date"style="background-color:#fff;" name="start_date"
                                id="start_date" class="form-control date" placeholder="Start Date">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveNote">Save</button>
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
    <div class="modal fade" id="ganttChartModal" tabindex="-1" role="dialog" aria-labelledby="ganttChartModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="width:80%;max-width:80% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ganttChartModalLabel">Gantt Chart Data
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="ganttChart-content">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveganttChartData">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="checkThreadsModal" tabindex="-1" role="dialog"
        aria-labelledby="checkThreadsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkThreadsModalLabel">Check Documents Threads</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exportGanttChartModal" tabindex="-1" role="dialog"
        aria-labelledby="exportGanttChartModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportGanttChartModalLabel">
                        Expotr Gantt Chart
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportGanttChartForm">
                        @csrf

                        <input type="hidden" id="file__slug" name="file_slug" value={{ $file->slug }}>
                        <div class="form-group">
                            <label>Time Frame</label>
                            <div style="display: flex;    margin-top: -5px;">
                                <div class="custom-control custom-radio" style="width: 50%;">
                                    <input type="radio" id="auto" name="timeframe" value="auto"
                                        class="custom-control-input" checked>
                                    <label class="custom-control-label" for="auto">Auto</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 50%;">
                                    <input type="radio" id="fixed_dates" name="timeframe"
                                        class="custom-control-input" value="fixed_dates">
                                    <label class="custom-control-label" for="fixed_dates">Fixed Dates</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-none" id="datesGroup">
                            <div style="display: flex;">
                                <input type="date"style="background-color:#fff;width: 49%;" name="start_date"
                                    id="start_date_gantt" class="form-control datennn fghd" placeholder="Start Date">
                                <input type="date"style="background-color:#fff;width: 49%;" name="end_date"
                                    id="end_date_gantt" class="form-control datennn" placeholder="End Date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <div style="display: flex;    margin-top: -5px;">
                                <div class="custom-control custom-radio" style="width: 33.3%;">
                                    <input type="radio" id="title_status1" name="title_status" value="non"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="title_status1">Non</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="title_status2" name="title_status"
                                        class="custom-control-input" value="up" checked>
                                    <label class="custom-control-label" for="title_status2">Over Timescale</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="title_status3" name="title_status"
                                        class="custom-control-input" value="down">
                                    <label class="custom-control-label" for="title_status3">Under Timescale</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Title Font Size</label>
                            <div style="display: flex;    margin-top: -5px;">
                                <div class="custom-control custom-radio" style="width: 33.3%;">
                                    <input type="radio" id="T_font_size1" name="title_font_size" value="14"
                                        class="custom-control-input" checked>
                                    <label class="custom-control-label" for="T_font_size1">14</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="T_font_size2" name="title_font_size"
                                        class="custom-control-input" value="16">
                                    <label class="custom-control-label" for="T_font_size2">16</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="T_font_size3" name="title_font_size"
                                        class="custom-control-input" value="18">
                                    <label class="custom-control-label" for="T_font_size3">18</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Current Font Size</label>
                            <div style="display: flex;    margin-top: -5px;">
                                <div class="custom-control custom-radio" style="width: 33.3%;">
                                    <input type="radio" id="cur_font_size1" name="cur_font_size" value="10"
                                        class="custom-control-input" checked>
                                    <label class="custom-control-label" for="cur_font_size1">10</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="cur_font_size2" name="cur_font_size"
                                        class="custom-control-input" value="12">
                                    <label class="custom-control-label" for="cur_font_size2">12</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="cur_font_size3" name="cur_font_size"
                                        class="custom-control-input" value="14">
                                    <label class="custom-control-label" for="cur_font_size3">14</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Planned Font Size</label>
                            <div style="display: flex;    margin-top: -5px;">
                                <div class="custom-control custom-radio" style="width: 33.3%;">
                                    <input type="radio" id="pl_font_size1" name="pl_font_size" value="8"
                                        class="custom-control-input" checked>
                                    <label class="custom-control-label" for="pl_font_size1">8</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="pl_font_size2" name="pl_font_size"
                                        class="custom-control-input" value="10">
                                    <label class="custom-control-label" for="pl_font_size2">10</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="pl_font_size3" name="pl_font_size"
                                        class="custom-control-input" value="12">
                                    <label class="custom-control-label" for="pl_font_size3">12</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Row Height</label>
                            <div style="display: flex;    margin-top: -5px;">
                                <div class="custom-control custom-radio" style="width: 33.3%;">
                                    <input type="radio" id="d1" name="raw_height" value="1"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="d1">1</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="d2" name="raw_height"
                                        class="custom-control-input" value="1.5" checked>
                                    <label class="custom-control-label" for="d2">1.5</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="d3" name="raw_height"
                                        class="custom-control-input" value="2">
                                    <label class="custom-control-label" for="d3">2</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Font Name</label>
                            <select class="form-control" id="font_type" name="font_type">
                                <option value="Aptos">Aptos</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveExportGanttChart">draw</button>
                </div>
            </div>
        </div>
    </div>
    <ul id="contextMenu" class="custom-context-menu" style="position:absolute; z-index:9999;">
        <li><a href="#" id="Preview-Document"><i class="fe fe-arrow-right"></i> Preview Document</a></li>
        <li><a href="#" id="Jump"><i class="fe fe-arrow-right"></i> Jump</a></li>
        <li><a href="#" id="Assign-To-File"><i class="fe fe-arrow-right"></i> Assign To File</a></li>

    </ul>
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
        let clickTimer = null;

        function handleClick(editUrl, pdfUrl) {
            if (clickTimer !== null) {
                // يعني double click → افتح الـ PDF
                clearTimeout(clickTimer);
                clickTimer = null;
                window.open(pdfUrl, '_blank');
            } else {
                // يعني single click → نستنى شوية قبل الفتح
                clickTimer = setTimeout(() => {
                    window.open(editUrl, '_blank');
                    clickTimer = null;
                }, 250); // 250ms كافية تفرق single عن double
            }
        }
    </script>

    <script>
        function updatePrefix() {
            const h1 = document.getElementById('Chapter').value;
            const h2 = document.getElementById('Section').value;
            document.getElementById('Prefix2').value = `Exhibit ${h1}.${h2}.`;
        }

        // Listen to changes on both inputs
        document.getElementById('Chapter').addEventListener('input', updatePrefix);
        document.getElementById('Section').addEventListener('input', updatePrefix);

        // Initial run in case values are preset
        updatePrefix();
    </script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const targetRow = document.querySelector('.specific_file_doc');
            const container = document.querySelector('.table-container tbody');
            console.log(targetRow.offsetTop);
            if (targetRow && container) {
                const headerHeight = 0; // في حالتك الهيدر sticky فوق الجدول مش جواه، فمش لازم نطرح ارتفاعه
                const offsetTop = targetRow.offsetTop - headerHeight;
                container.scrollTop = offsetTop - 58;
            }
        });
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            $('input[name="timeframe"]').on('change', function() {
                if ($('#fixed_dates').is(':checked')) {
                    $('#datesGroup').removeClass('d-none');
                    $('#start_date_gantt, #end_date_gantt').attr('required', true);
                } else {
                    $('#datesGroup').addClass('d-none');
                    $('#start_date_gantt, #end_date_gantt').removeAttr('required');
                }
            });

            $('.gantt-chart').on('click', function() {
                let FD_ID = $(this).data('document-id');

                $('#ganttChart-content').empty();
                $.ajax({
                    url: '/project/file-document/gantt-chart/' + FD_ID, // Replace with real route
                    type: 'GET',
                    data: {
                        _token: $('input[name="_token"]').val() // CSRF token
                    },
                    success: function(response) {
                        // showHint(response.message || 'Download started!');

                        $('#ganttChart-content').html(response.html);
                        flatpickr(".date2", {
                            enableTime: false,
                            dateFormat: "Y-m-d", // Format: YYYY-MM-DD
                            altInput: true,
                            altFormat: "d.M.Y",
                        });

                        flatpickr(".flatpickr-container", {
                            enableTime: false,
                            dateFormat: "Y-m-d",
                            altInput: true,
                            altFormat: "d.M.Y",
                            allowInput: true,
                            wrap: true, // enables `data-input`, `data-clear`
                            clickOpens: true,

                        });
                        const plType = document.getElementById('pl_type');
                        const plSdGroup = document.querySelector('#pl_sd').closest(
                            '.form-group');
                        const plFdGroup = document.querySelector('#pl_fd').closest(
                            '.form-group');

                        plType.addEventListener('change', function() {
                            if (this.value === 'SB') {
                                plSdGroup.classList.remove('d-none');
                                plFdGroup.classList.remove('d-none');
                            } else if (this.value === 'M') {
                                plSdGroup.classList.remove('d-none');

                                const plFdPickerjjj = flatpickr("#fgd", {
                                    enableTime: false,
                                    dateFormat: "Y-m-d",
                                    altInput: true,
                                    altFormat: "d.M.Y",
                                    allowInput: true,
                                    wrap: true, // enables `data-input`, `data-clear`
                                    clickOpens: true
                                });
                                document.getElementById('pl_fd').value = '';
                                plFdPickerjjj.clear();
                                plFdGroup.classList.add('d-none');
                            } else {
                                const plFdPickerjjj = flatpickr("#fgd", {
                                    enableTime: false,
                                    dateFormat: "Y-m-d",
                                    altInput: true,
                                    altFormat: "d.M.Y",
                                    allowInput: true,
                                    wrap: true, // enables `data-input`, `data-clear`
                                    clickOpens: true
                                });
                                document.getElementById('pl_fd').value = '';
                                plFdPickerjjj.clear();
                                plFdGroup.classList.add('d-none');
                                const plFdPickerjjjj = flatpickr("#sgd", {
                                    enableTime: false,
                                    dateFormat: "Y-m-d",
                                    altInput: true,
                                    altFormat: "d.M.Y",
                                    allowInput: true,
                                    wrap: true, // enables `data-input`, `data-clear`
                                    clickOpens: true
                                });
                                document.getElementById('pl_sd').value = '';
                                plFdPickerjjjj.clear();
                                plSdGroup.classList.add('d-none');
                            }
                        });

                        const curType = document.getElementById('cur_type');
                        const curSdGroup = document.querySelector('#cur_sd').closest(
                            '.form-group');
                        const curFdGroup = document.querySelector('#cur_fd').closest(
                            '.form-group');
                        const curColorGroup = document.querySelector('#cu_color').closest(
                            '.form-group');

                        curType.addEventListener('change', function() {
                            const val = this.value;

                            // Reset all
                            curSdGroup.classList.add('d-none');
                            curFdGroup.classList.add('d-none');
                            curColorGroup.classList.add('d-none');
                            document.querySelector('#multi-sec').classList.add(
                                'd-none');

                            // Apply logic
                            if (val === 'SB' || val === 'DA') {
                                curSdGroup.classList.remove('d-none');
                                curFdGroup.classList.remove('d-none');
                                curColorGroup.classList.remove('d-none');
                            } else if (val === 'M' || val === 'S') {
                                curSdGroup.classList.remove('d-none');
                                curColorGroup.classList.remove('d-none');
                            } else if (val === 'MS') {
                                document.querySelector('#multi-sec').classList.remove(
                                    'd-none')
                            }
                        });


                        // Sync finish date to next section's start date

                        const startInput = document.getElementById('lp_sd');
                        const endInput = document.getElementById('lp_fd');

                        const existingStart = new Date(startInput.dataset.documentSd);
                        const existingEnd = new Date(endInput.dataset.documentFd);

                        flatpickr(startInput.closest(".flatpickr-container"), {
                            enableTime: false,
                            dateFormat: "Y-m-d",
                            altInput: true,
                            altFormat: "d.M.Y",
                            allowInput: true,
                            wrap: true,
                            clickOpens: true,
                            onChange: function(selectedDates, dateStr, instance) {
                                const selected = selectedDates[0];
                                if (selected < existingStart || selected >=
                                    existingEnd) {
                                    alert(
                                        "Start date must be ≥ document start date and < document finish date."
                                    );
                                    instance.setDate(
                                        existingStart); // Reset to original
                                }
                            }
                        });

                        // Flatpickr instance for End Date
                        flatpickr(endInput.closest(".flatpickr-container"), {
                            enableTime: false,
                            dateFormat: "Y-m-d",
                            altInput: true,
                            altFormat: "d.M.Y",
                            allowInput: true,
                            wrap: true,
                            clickOpens: true,
                            onChange: function(selectedDates, dateStr, instance) {
                                const selected = selectedDates[0];
                                if (selected <= existingStart || selected >
                                    existingEnd) {
                                    alert(
                                        "Finish date must be > document start date and ≤ document finish date."
                                    );
                                    instance.setDate(
                                        existingEnd); // Reset to original
                                }
                            }
                        });


                        //////////////////////////////////////////////////////
                        function initFlatpickr() {
                            flatpickr(".date2", {
                                enableTime: false,
                                dateFormat: "Y-m-d",
                                altInput: true,
                                altFormat: "d.M.Y",
                                onChange: function(selectedDates, dateStr, instance) {
                                    if (instance.input.placeholder === "F Date") {
                                        updateNextStartDate(instance.input);
                                    }
                                }
                            });
                        }

                        // Function to update next row's start date
                        function updateNextStartDate(finishInput) {
                            const finishDateStr = finishInput.value;
                            if (!finishDateStr) return;

                            const finishDate = new Date(finishDateStr);
                            const currentRow = $(finishInput).closest('.multi-section')[0];

                            const startInput = currentRow.querySelector(
                                '.date2[placeholder="S Date"]');
                            if (startInput && startInput.value) {
                                const startDate = new Date(startInput.value);
                                if (finishDate < startDate) {
                                    alert("Finish date cannot be earlier than start date.");
                                    // Revert the value
                                    const fp = finishInput._flatpickr;
                                    if (fp) {
                                        fp.setDate(startInput.value, true);
                                    } else {
                                        finishInput.value = startInput.value;
                                    }
                                    return;
                                }
                            }

                            const nextRow = currentRow.nextElementSibling;
                            if (nextRow) {
                                const nextFinishInput = nextRow.querySelector(
                                    '.date2[placeholder="F Date"]');
                                if (nextFinishInput && nextFinishInput.value) {
                                    const nextFinishDate = new Date(nextFinishInput.value);
                                    if (finishDate > nextFinishDate) {
                                        alert(
                                            "Finish date cannot be later than the next section's finish date."
                                        );
                                        // Revert the value
                                        const fp = finishInput._flatpickr;
                                        if (fp) {
                                            fp.setDate(startInput.value, true);
                                        } else {
                                            finishInput.value = startInput.value;
                                        }
                                        return;
                                    }
                                }
                            }

                            // If valid, set start date in next section
                            if (nextRow) {
                                const nextStartInput = nextRow.querySelector(
                                    '.date2[placeholder="S Date"]');
                                if (nextStartInput) {
                                    nextStartInput.value = finishDateStr;
                                    const fp = nextStartInput._flatpickr;
                                    if (fp) fp.setDate(finishDateStr, true);
                                }
                            }
                        }

                        // Initial initialization
                        initFlatpickr();

                        // Add button click handler
                        $(document).on('click', '.add-btnooo', function() {
                            const currentSection = $(this).closest('.multi-section');
                            const finishDate = currentSection.find(
                                'input.date2[placeholder="F Date"]').val();

                            if (!finishDate) {
                                alert('Please set the finish date first');
                                return;
                            }

                            // Create the new section HTML
                            const newSection = $(`
                                <div style="display: flex" class="multi-section mb-2">
                                    <div style="padding-left: 0px;padding-right: 3px;width: 35%;">
                                        <input disabled type="date" style="background-color:#fff;" name="sections[sd]"
                                            class="date form-control date2" placeholder="S Date" value="${finishDate}">
                                    </div>
                                    <div style="padding-left: 0px;padding-right: 3px;width: 35%;">
                                        <input required type="date" style="background-color:#fff;" name="sections[fd]"
                                            class="date form-control date2" placeholder="F Date" value="${finishDate}">
                                    </div>
                                    <div style="padding-left: 0px;padding-right: 3px;">
                                        <select class="form-control" name="sections[color]">
                                            <option value="808080" style="background-color: #808080; color: white;">■ Gray</option>
                                            <option selected value="00008B" style="background-color: #00008B; color: white;">■ Dark Blue</option>
                                            <option value="FF0000" style="background-color: #FF0000; color: white;">■ Red</option>
                                            <option value="008000" style="background-color: #008000; color: white;">■ Green</option>
                                            <option value="000000" style="background-color: #000000; color: white;">■ Black</option>
                                            <option value="89CFF0" style="background-color: #89CFF0;">■ Baby Blue</option>
                                            <option value="FFFF00" style="background-color: #FFFF00;">■ Yellow</option>
                                            <option value="8B4513" style="background-color: #8B4513; color: white;">■ Brown</option>
                                            <option value="FFFFFF" style="background-color: #FFFFFF;">■ White</option>
                                            <option value="FFFFFF00">■ Gap</option>
                                        </select>
                                    </div>
                                    <div style="padding-left: 0px;padding-right: 3px;">
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 h-100 add-btnooo"
                                            title="Add" style="border-color: rgb(200, 214, 238);font-size:17px;">
                                            +
                                        </button>
                                    </div>
                                    <div style="padding-left: 0px;padding-right: 0px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 h-100" title="Clear"
                                            style="border-color: rgb(200, 214, 238)">
                                            ✖
                                        </button>
                                    </div>
                                    
                                </div>
                            `);

                            // Insert the new section after the current one
                            newSection.insertAfter(currentSection);

                            // Remove the add button from the current section


                            // Initialize flatpickr on the new inputs
                            initFlatpickr();
                        });

                        // Clear button click handler
                        $(document).on('click', '.btn-outline-danger', function() {
                            const section = $(this).closest('.multi-section');
                            const startDate = section.find(
                                'input.date2[placeholder="S Date"]').val();
                            const sections = $('.multi-section');

                            if (sections.length <= 2) {
                                alert('You must keep at least two sections');
                                return;
                            }
                            const nextSection = section.next('.multi-section');
                            section.remove();
                            if (nextSection.length > 0) {
                                const nextStartDate = nextSection.find(
                                    'input.date2[placeholder="S Date"]');

                                // Flatpickr requires setting both input.value and flatpickr.setDate
                                if (nextStartDate.length > 0) {
                                    const flatpickrInstance = nextStartDate[0]
                                        ._flatpickr;
                                    if (flatpickrInstance) {
                                        flatpickrInstance.setDate(startDate,
                                            true); // second param = triggerChange
                                    } else {
                                        nextStartDate.val(startDate); // fallback
                                    }
                                }
                            }
                            // Ensure last section has add button


                        });

                        // Initialize date propagation for existing sections
                        $('.date2[placeholder="F Date"]').on('change', function() {

                            updateNextStartDate(this);
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Failed to process. Please try again.');
                    }
                });
                $('#ganttChartModal').modal('show');

            });

            // Add this code inside your AJAX success callback after all the flatpickr initialization
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
            // Form submission handler
            $('#saveganttChartData').on('click', function() {
                // Get the form element
                const form = document.getElementById('ganttChartForm');

                // Validate multi-sections if they exist
                if ($('#multi-sec').is(':visible')) {
                    let isValid = true;
                    let errorMessage = '';

                    $('.multi-section').each(function(index) {
                        const $section = $(this);
                        const startDate = $section.find('input[placeholder="S Date"]').val();
                        const finishDate = $section.find('input[placeholder="F Date"]').val();

                        // Check if dates are empty
                        if (!startDate || !finishDate) {
                            isValid = false;
                            errorMessage = 'All section dates are required';
                            return false; // break out of loop
                        }

                        // Check if finish date is after start date
                        if (new Date(finishDate) < new Date(startDate)) {
                            isValid = false;
                            errorMessage =
                                `Section ${index + 1}: Finish date must be after start date`;
                            return false;
                        }

                        // Check if this section's start date matches previous section's finish date
                        if (index > 0) {
                            const prevFinish = $('.multi-section').eq(index - 1).find(
                                'input[placeholder="F Date"]').val();
                            if (startDate !== prevFinish) {
                                isValid = false;
                                errorMessage =
                                    `Section ${index + 1}: Start date must match previous section's finish date`;
                                return false;
                            }
                        }
                    });

                    if (!isValid) {
                        alert(errorMessage);
                        return;
                    }
                }

                // Validate other required fields
                if ($('#cur_type').val() == '' || $('#cur_type').val() == null) {
                    alert('Current Bar Type is required');
                    return;
                }
                const plType = $('#pl_type').val();

                // Validate based on selected type
                if (plType === 'SB') {
                    // Single Bar - both dates required
                    if (!$('#pl_sd').val() || !$('#pl_fd').val()) {
                        alert('Both start and finish dates are required for Planned Bar');
                        return false;
                    }
                } else if (plType === 'M') {
                    // Milestone - only start date required
                    if (!$('#pl_sd').val()) {
                        alert('Start date is required for Planned Bar');
                        return false;
                    }
                }

                // Collect all section data
                const sections = [];
                $('.multi-section').each(function() {
                    sections.push({
                        sd: $(this).find('input[placeholder="S Date"]').val() || null,
                        fd: $(this).find('input[placeholder="F Date"]').val() || null,
                        color: $(this).find('select').val() || null
                    });
                });

                // Prepare form data - CORRECTED THIS LINE
                const formData = new FormData(form);

                // Add sections data
                if (sections.length > 0) {
                    formData.append('sections', JSON.stringify(sections));
                }

                // Add CSRF token for Laravel
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Submit via AJAX
                $.ajax({
                    url: '/project/file-document/save-gantt-chart',
                    type: 'POST',
                    data: formData,
                    processData: false, // Important for FormData
                    contentType: false, // Important for FormData
                    success: function(response) {
                        if (response.success) {
                            $('#ganttChartModal').modal('hide');
                            showHint('Data saved successfully');
                            // Optional: reload or update the page
                        } else {
                            alert('Error: ' + (response.message || 'Failed to save data'));
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error saving data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg += ': ' + xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMsg += ': ' + xhr.responseText;
                        }
                        alert(errorMsg);
                    }
                });
            });
            ////////////////////////////////////////////

            $('#checkThreads').on('click', function() {
                let file = "{{ $file->slug }}";
                $.ajax({
                    url: '/check-unassignment-docs-by-threads',
                    type: 'GET',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        file: file, // Pass the array here
                    },

                    success: function(response) {
                        // showHint(response.message || 'Download started!');
                        $('#modal-body').html(response.html);
                        $('#checkThreadsModal').modal('show');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Failed to process. Please try again.');
                    }
                });

            });


            ///////////////////////////////////////////////////////
            $('#exportGanttChart').on('click', function() {

                $('#exportGanttChartModal').modal('show');
            });

            $('#saveExportGanttChart').on('click', function() {
                let form = $('#exportGanttChartForm');

                // Optional: basic validation if fixed_dates is selected
                let isFixed = $('input[name="timeframe"]:checked').val() === 'fixed_dates';
                if (isFixed && (!$('#start_date_gantt').val() || !$('#end_date_gantt').val())) {
                    alert("Please select both start and end dates.");
                    return;
                }

                $.ajax({
                    url: "{{ route('project.file-documents.extractPowerPoint') }}", // ✅ Replace with your route
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.download_url) {
                            window.location.href = response.download_url; // يبدأ التحميل فعليًا
                        }
                        $('#exportGanttChartModal').modal('hide');
                        // You can also trigger file download or redirect here
                    },
                    error: function(xhr) {
                        alert("Export failed. Please try again.");
                        console.error(xhr.responseText);
                    }
                });
            });
            ///////////////////////////////////////////////////////
            $('#addNote').on('click', function() {

                $('#subject').val('');
                $('#start_date').val('');
                $('#addNoteModal').modal('show');
            });
            $('#saveNote').on('click', function() {
                const form2 = $('#addNoteForm');

                // Optional client-side check before AJAX send
                if (!form2[0].checkValidity()) {
                    form2[0].reportValidity();
                    return;
                }

                const formData2 = form2.serialize();

                $.ajax({
                    url: '/project/create-new-note', // Replace with real route
                    type: 'POST',
                    data: formData2,
                    success: function(response) {
                        // showHint(response.message || 'Download started!');

                        $('#addNoteModal').modal('hide');
                        window.location.reload();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Failed to process. Please try again.');
                    }
                });

            });

            // When "Download All" button is clicked
            $('#export-allDoc').on('click', function() {
                const fileId = $(this).data('file-id');
                $('#file_id111').val(fileId);
                $('#exportModal').modal('show');
            });

            // When radio changes for format selection
            $('input[name="formate_type2"]').on('change', function() {
                if ($('#formate2').is(':checked')) {
                    $('#extraOptions2').removeClass('d-none');
                    $('#Prefix2').attr('required', true);
                    $('#sn2').attr('required', true);
                    $('#Start2').attr('required', true);
                    $('input[name="ref_part2"]').attr('required', true);
                } else {
                    $('#extraOptions2').addClass('d-none');

                    // Clear all inputs inside extraOptions
                    $('#extraOptions2').find('input[type="text"], input[type="number"]').val('');
                    $('#extraOptions2').find('input[type="radio"]').prop('checked', false);

                    // Remove required attributes
                    $('#Prefix2').removeAttr('required');
                    $('#sn2').removeAttr('required');
                    $('#Start2').removeAttr('required');
                    $('input[name="ref_part2"]').removeAttr('required');
                }
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
                    url: '/export-word-claim-docs', // Replace with real route
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
        $(document).on("click", ".assign-btn1", function() {
            console.log('fff');
            let docSlug = $(this).data("doc-slug");
            let parentDocSlug = $(this).data("parent-doc-slug");
            let thread = $(this).data("thread");
            let action = $(this).data("action");
            let file = "{{ $file->slug }}"
            $.ajax({
                url: "{{ route('project.assign-thread') }}", // اعمل route في لارافيل
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    doc_slug: docSlug,
                    parent_doc_slug: parentDocSlug,
                    thread: thread,
                    action: action,
                    file: file
                },
                success: function(response) {
                    showHint(response.success);
                    $.ajax({
                        url: '/check-unassignment-docs-by-threads',
                        type: 'GET',
                        data: {
                            _token: $('input[name="_token"]').val(), // CSRF token
                            file: file, // Pass the array here
                        },

                        success: function(response) {
                            // showHint(response.message || 'Download started!');
                            $('#modal-body').html(response.html);

                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('Failed to process. Please try again.');
                        }
                    });
                },
                error: function(xhr) {
                    alert("Something went wrong ❌");
                    console.log(xhr.responseText);
                }
            });
        })
    </script>
    <script>
        $(document).ready(function() {

            $('.doc_narrative').on('click', function() {
                const DOCid = $(this).data('doc-id');

                $.ajax({
                    url: '/get-narrative',
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
            // When "Download All" button is clicked
            $('#download-allDoc').on('click', function() {
                const fileId = $(this).data('file-id');
                $('#file_id_').val(fileId);
                $('#downloadAllDocsModal').modal('show');
            });

            // When radio changes for format selection
            $('input[name="formate_type"]').on('change', function() {
                if ($('#formate').is(':checked')) {
                    $('#extraOptions').removeClass('d-none');
                    $('#Prefix').attr('required', true);
                    $('#sn').attr('required', true);
                    $('#Start').attr('required', true);
                    $('input[name="ref_part"]').attr('required', true);
                } else {
                    $('#extraOptions').addClass('d-none');

                    // Clear all inputs inside extraOptions
                    $('#extraOptions').find('input').val('');
                    $('#extraOptions').find('input[type="radio"]').prop('checked', false);

                    // Remove required attributes
                    $('#Prefix').removeAttr('required');
                    $('#sn').removeAttr('required');
                    $('#Start').removeAttr('required');
                    $('input[name="ref_part"]').removeAttr('required');
                }
            });

            // When user clicks "Save" (download)
            $('#downloadalldocs').on('click', function() {
                const form = $('#downloadAllDocsForm');

                // Optional client-side check before AJAX send
                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }

                const formData = form.serialize();

                $.ajax({
                    url: '/download-all-documents', // Replace with real route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // showHint(response.message || 'Download started!');
                        if (response.download_url) {
                            window.location.href = response.download_url; // يبدأ التحميل فعليًا
                        }
                        $('#downloadAllDocsModal').modal('hide');
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
            $('.Check-other-assignments-btn').on('click', function() {
                var documentId = $(this).data('document-id');
                var type = $(this).data('type');
                fetch("{{ route('set.session') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            key: 'file_doc_type',
                            value: type
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data); // should log { status: 'ok' }
                    });
                $.ajax({
                    url: '/document/get-files/' +
                        documentId, // Adjust the route to your API endpoint
                    type: 'GET',
                    success: function(response) {
                        let container = $('#container');
                        container.empty()
                        $.each(response.files, function(index, file) {
                            container.append(
                                `<p><span class="fa fa-star"></span> <span style="font-size:1.2rem;">${file.folder.name}</span>  <span style="font-family: Helvetica, Arial, Sans-Serif; font-size: 26px;">&#x2192;</span>  <span style="font-size:1.2rem;">${file.name}</span></p>`
                            );
                        });
                    },
                    error: function() {
                        alert('Failed to fetch files. Please try again.');
                    }
                });

                $('#CheckOtherAssignmentModal').modal('show'); // Show the modal
            });

            $('.unassign-doc-btn').on('click', function() {
                var documentIds = [];
                documentIds.push($(this).data('document-id'));


                if (confirm(

                        'Are you sure you want to unassign this document from this file? This action cannot be undone.'
                    )) {
                    $.ajax({
                        url: '/project/unassign-doc',
                        type: 'POST',
                        data: {
                            _token: $('input[name="_token"]').val(), // CSRF token
                            document_ids: documentIds, // Pass the array here
                        },
                        success: function(response) {
                            // Loop through IDs and remove each corresponding row
                            documentIds.forEach(function(id) {
                                document.getElementById('dddd_' + id)?.remove();
                            });

                            showHint(response.message); // Show success message
                        },
                        error: function() {
                            alert('Failed to unassign documents. Please try again.');
                        }
                    });
                }
            })
            $('.Delete-from-CMW-btn').on('click', function() {
                var documentIds = [];
                documentIds.push($(this).data('document-id'));
                if (confirm(
                        'Are you sure you want to delete this document from CMW entirely? This action cannot be undone.'
                    )) {
                    $.ajax({
                        url: '/project/delete-doc-from-cmw-entirely', // Adjust the route to your API endpoint
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
                            alert('Failed to assign document. Please try again.');
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
                    url: '/project/doc/make-for-claim', // Adjust the route to your API endpoint
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
                var docId = $(this).data('document-id');
                var $button = $(this);

                $.ajax({
                    url: '/project/change-flag', // Adjust the route to your API endpoint
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
                        alert('Failed to assign document. Please try again.');
                    }
                });
            });
            $('.for-claim-btn222').on('click', function() {
                var documentIds = [];
                var type = $(this).data('action-type')
                documentIds.push($(this).data('document-id'));

                let value = ''
                if (this.classList.contains('active')) {
                    value = '0';
                } else {
                    value = '1';
                }

                $.ajax({
                    url: '/project/doc/make-for-claim', // Adjust the route to your API endpoint
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
                                } else if (type == 'forLetter') {
                                    const forNoticeLabel = tr.querySelector(
                                        'label.for_notice');
                                    if (forNoticeLabel && !forNoticeLabel.classList
                                        .contains('active')) {
                                        forNoticeLabel.classList.add('active');
                                        forNoticeLabel.style.backgroundColor =
                                            'rgb(45, 209, 45)';
                                    } else {

                                        forNoticeLabel.classList.remove('active');
                                        forNoticeLabel.style.backgroundColor =
                                            'rgb(169, 169, 169)';
                                    }
                                } else if (type == 'forChart') {
                                    const forChartLabel = tr.querySelector(
                                        'label.for_timeline');
                                    if (forChartLabel && !forChartLabel.classList
                                        .contains('active')) {
                                        forChartLabel.classList.add('active');
                                        forChartLabel.style.backgroundColor =
                                            'rgb(45, 209, 45)';
                                    } else {
                                        forChartLabel.classList.remove('active');
                                        forChartLabel.style.backgroundColor =
                                            'rgb(169, 169, 169)';
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
                var documentId_ = $(this).data('document-id');
                var action_type = $(this).data('action-type');
                console.log(action_type)
                $('#documentId_').val(documentId_);
                $('#action_type').val(action_type);
                $('#folder_id').val('');
                let fileDropdown = $('#newFile');
                fileDropdown.closest('.form-group').addClass(
                    'd-none');
                document.getElementById('type').innerText = action_type.charAt(0).toUpperCase() +
                    action_type.slice(1).toLowerCase();

                $('#copyToModal').modal('show'); // Show the modal
            });



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

            $('#saveCopyDoc').click(function() {
                let documentIds = [];
                documentIds.push($('#documentId_').val());
                //let documentId = $('#documentId_').val();
                let fileId = $('#newFile').val();
                let actionType = $('#action_type').val();

                if (!fileId) {
                    alert('Please select a file.');
                    return;
                }

                $.ajax({
                    url: '/project/copy_move_doc_to_another_file', // Adjust the route to your API endpoint
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
                        $('#copyToModal').modal('hide');
                    },
                    error: function() {
                        alert('Failed to assign document. Please try again.');
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
                                         <a class="dropdown-item" id="editDocsForAllBtn" href="javascript:void(0);">Edit Documents</a>
                                         <a class="dropdown-item" id="downloadForAllBtn" href="javascript:void(0);">Download Documents</a>
                                         <a class="dropdown-item" id="exportExcelForAllBtn" href="javascript:void(0);">Export Excel</a>
                                         <a class="dropdown-item copyForAllBtn" id="copyForAllBtn" href="javascript:void(0);"data-action-type2="copy">Copy All Documents To Another File</a>
                                         <a class="dropdown-item copyForAllBtn" id="moveForAllBtn" href="javascript:void(0);"data-action-type2="move">Move All Documents To Another File</a>
                                         <a class="dropdown-item" id="unassignForAllBtn" href="javascript:void(0);">Unassign Documents</a>
                                         <a class="dropdown-item" id="removeForAllBtn" href="javascript:void(0);">Delete Documents From CMW</a>
                                         <a class="dropdown-item for-claim-btn-for-all" data-action-type="forClaim" id="forClaimForAllBtn" href="javascript:void(0);">For Claim</a>
                                         <a class="dropdown-item for-claim-btn-for-all" data-action-type="forLetter" id="forNoticeForAllBtn" href="javascript:void(0);">For Notice</a>
                                         <a class="dropdown-item for-claim-btn-for-all" data-action-type="forChart" id="forChartForAllBtn" href="javascript:void(0);">For Gantt Chart</a>


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
                        const menu = document.getElementById('contextMenu');
                        if (menu) menu.style.display = 'none';
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
            $('.copyForAllBtn').on('click', function() {
                // Get all checked checkboxes

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

                // Set the document IDs in a hidden input (optional)
                $('#documentIdss').val(documentIds.join(','));
                var action_type2 = $(this).data('action-type2');
                $('#action_type2').val(action_type2);
                // Open the modal
                $('#folder_id2').val('');
                let fileDropdown = $('#newFile2');
                fileDropdown.closest('.form-group').addClass(
                    'd-none');
                document.getElementById('type2').innerText = action_type2.charAt(0).toUpperCase() +
                    action_type2.slice(1).toLowerCase();
                $('#copyToForAllModal').modal('show');
            });

            $('#saveCopyDocs').click(function() {
                let documentIds = $('#documentIdss').val().split(',');

                //let documentId = $('#documentId_').val();
                let fileId = $('#newFile2').val();
                let actionType = $('#action_type2').val();

                if (!fileId) {
                    alert('Please select a file.');
                    return;
                }

                $.ajax({
                    url: '/project/copy_move_doc_to_another_file', // Adjust the route to your API endpoint
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
                        alert('Failed to assign document. Please try again.');
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
                        'Are you sure you want to delete these documents from CMW entirely? This action cannot be undone.'
                    )) {
                    $.ajax({
                        url: '/project/delete-doc-from-cmw-entirely', // Adjust the route to your API endpoint
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
                            alert('Failed to assign document. Please try again.');
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
                    url: '/project/doc/make-for-claim', // Adjust the route to your API endpoint
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
                                } else if (type == 'forLetter') {
                                    const forNoticeLabel = tr.querySelector(
                                        'label.for_notice');
                                    if (response.value == '1' && forNoticeLabel && !
                                        forNoticeLabel.classList
                                        .contains('active')) {
                                        forNoticeLabel.classList.add('active');
                                        forNoticeLabel.style.backgroundColor =
                                            'rgb(45, 209, 45)';
                                    } else if (response.value == '0' &&
                                        forNoticeLabel && forNoticeLabel.classList
                                        .contains('active')) {

                                        forNoticeLabel.classList.remove('active');
                                        forNoticeLabel.style.backgroundColor =
                                            'rgb(169, 169, 169)';
                                    }
                                } else if (type == 'forChart') {
                                    const forChartLabel = tr.querySelector(
                                        'label.for_timeline');
                                    if (response.value == '1' && forChartLabel && !
                                        forChartLabel.classList
                                        .contains('active')) {
                                        forChartLabel.classList.add('active');
                                        forChartLabel.style.backgroundColor =
                                            'rgb(45, 209, 45)';
                                    } else if (response.value == '0' && forChartLabel &&
                                        forChartLabel.classList
                                        .contains('active')) {

                                        forChartLabel.classList.remove('active');
                                        forChartLabel.style.backgroundColor =
                                            'rgb(169, 169, 169)';
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
            $('#unassignForAllBtn').on('click', function() {
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

                        'Are you sure you want to unassign these documents from this file? This action cannot be undone.'
                    )) {
                    $.ajax({
                        url: '/project/unassign-doc',
                        type: 'POST',
                        data: {
                            _token: $('input[name="_token"]').val(), // CSRF token
                            document_ids: documentIds, // Pass the array here
                        },
                        success: function(response) {
                            // Loop through IDs and remove each corresponding row
                            documentIds.forEach(function(id) {
                                document.getElementById('dddd_' + id)?.remove();
                            });
                            document.getElementById('actionList').style.display = 'none';
                            showHint(response.message); // Show success message
                        },
                        error: function() {
                            alert('Failed to unassign documents. Please try again.');
                        }
                    });
                }
            })

            $('#editDocsForAllBtn').on('click', function() {
                // Get all checked checkboxes

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

                // Set the document IDs in a hidden input (optional)
                $('#documentIdds').val(documentIds.join(','));


                //$('#folder_id2').val('');
                //let fileDropdown = $('#newFile2');


                $('#editDocInfoModal').modal('show');
            });
            $('#editDocInfo').click(function() {
                $.ajax({
                    url: '/project/edit-docs-info', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_ids: $('#documentIdds').val().split(','),
                        doc_type: $('#newDocTypeForAll').val(),
                        from: $('#newFromStakeHolderForAll').val(),
                        to: $('#newToStakeHolderForAll').val(),
                        owner: $('#newOwnerForAll').val(),
                    },
                    success: function(response) {
                        if (response.status == 'error') {
                            alert("⚠️ " + response.message);
                        } else {
                            // Show success message
                            location.reload();

                        }
                        $('#copyToForAllModal').modal('hide');

                        //showHint(response.message);
                    },
                    error: function() {
                        alert('Failed to assign document. Please try again.');
                    }
                });
            });

            $('#downloadForAllBtn').on('click', function() {
                // Get all checked checkboxes

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

                // Set the document IDs in a hidden input (optional)

                $.ajax({
                    url: '/download-specific-documents', // Replace with real route
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_ids: documentIds,
                        file_id: $('#download-allDoc').data('file-id')

                    },
                    success: function(response) {
                        // showHint(response.message || 'Download started!');
                        if (response.download_url) {
                            window.location.href = response.download_url; // يبدأ التحميل فعليًا
                        }
                        document.getElementById('actionList').style.display = 'none';
                        showHint(response.message);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Failed to process. Please try again.');
                    }
                });

            });
            $('#exportExcelForAllBtn').on('click', function() {
                // Get all checked checkboxes

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

                // Set the document IDs in a hidden input (optional)

                $.ajax({
                    url: '/export-excel-specific-documents', // Replace with real route
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_ids: documentIds,
                        file_id: $('#download-allDoc').data('file-id')

                    },
                    success: function(response) {
                        // showHint(response.message || 'Download started!');
                        if (response.download_url) {
                            window.location.href = response.download_url; // يبدأ التحميل فعليًا
                        }
                        document.getElementById('actionList').style.display = 'none';
                        showHint(response.message);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('Failed to process. Please try again.');
                    }
                });

            });

        })
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const filters = {
                all_with_tags: false,
                all_for_claim: false,
                all_for_notice: false,
                all_for_timeline: false,
                all_blue_flag: false,
                all_red_flag: false,
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
                    if (filters.all_red_flag) show = show && row.querySelector(".red_flag.active");
                    if (filters.all_blue_flag) show = show && row.querySelector(".blue_flag.active");

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
                } else if (id === 'all_red_flag') {
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

            document.getElementById("all_with_tags").addEventListener("click", () => toggleFilter("all_with_tags"));
            document.getElementById("all_for_claim").addEventListener("click", () => toggleFilter("all_for_claim"));
            document.getElementById("all_for_notice").addEventListener("click", () => toggleFilter(
                "all_for_notice"));
            document.getElementById("all_for_timeline").addEventListener("click", () => toggleFilter(
                "all_for_timeline"));
            document.getElementById("all_red_flag").addEventListener("click", () => toggleFilter(
                "all_red_flag"));
            document.getElementById("all_blue_flag").addEventListener("click", () => toggleFilter(
                "all_blue_flag"));

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
    <script>
        $(document).ready(function() {
            $('.threadsBtn').on('click', function() {
                const threads = $(this).data('document-threads') || [];
                var documentSub = $(this).data('document-sub');
                var documentRef = $(this).data('document-ref');
                $('#ref').val(documentRef);
                $('#sub').val(documentSub);

                const container = $('#threadsContainer');
                container.empty(); // Clear previous content

                // Loop through threads and create a radio button for each
                threads.forEach((thread, index) => {
                    const radioHtml = `
                            

                            <div class="custom-control custom-radio">
                                    <input type="radio" id="thread${index}" name="threadOption" value="${thread.replace(/\\/g, '')}"
                                        class="custom-control-input thread-radio">
                                    <label class="custom-control-label" for="thread${index}">${thread.replace(/\\/g, '')}</label>
                                </div>
                        `;
                    container.append(radioHtml);
                });
                const docs = $('#docs');
                docs.addClass('d-none');
                $('#ThreadsModal').modal('show');

            });


            $(document).on('change', '.thread-radio', function() {
                const selectedThread = $(this).val();
                $('#docAssignments').addClass('d-none');
                $('#assigneToFile').addClass('d-none');
                // Send AJAX request to get documents based on reference
                $.ajax({
                    url: '/get-documents-by-thread', // Replace with your actual route
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                        reference: selectedThread
                    },
                    success: function(response) {
                        const resultDiv = $('#documentsResult');
                        const docs = $('#docs'); // Your target div
                        resultDiv.empty(); // Clear previous labels

                        if (response.documents && response.documents.length > 0) {
                            docs.removeClass('d-none');

                            response.documents.forEach((doc, index) => {
                                let x = doc.reference;
                                const label =
                                    `<label class="d-block doc-label" style="cursor:pointer;" oncontextmenu="showContextMenu(event, '${doc.storage_file.path}','${doc.slug}','${x.replace(/'/g, "\\'")}')"  data-doc-id="${doc.id}"data-doc-slug="${doc.slug}" data-doc-ref="${doc.reference}"data-type="document">📄 ${doc.reference}</label>`;
                                resultDiv.append(label);
                            });

                        } else {
                            docs.addClass('d-none');
                        }
                    },
                    error: function() {
                        alert('Failed to fetch documents.');
                    }
                });
            });

            const docFileRouteBase = "{{ route('goToDocFile', ['docId' => '__DOC__', 'fileId' => '__FILE__']) }}";
            $('#documentsResult').on('click', '.doc-label', function() {
                let docSlug = $(this).data('doc-slug');
                let docId = $(this).data('doc-id');
                let docRef = $(this).data('doc-ref');
                var type = $(this).data('type');
                fetch("{{ route('set.session') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            key: 'file_doc_type',
                            value: type
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data); // should log { status: 'ok' }
                    });
                $.ajax({
                    url: '/document/get-files/' + docSlug, // Your actual route
                    type: 'GET',

                    success: function(response) {

                        const fileDiv = $('#docAssignments');
                        const fileDiv2 = $('#assigneToFile');

                        fileDiv.empty();
                        fileDiv.append(`<hr>`);
                        fileDiv.append(
                            `<div style="text-align:center;"><p>Assignments of "${docRef}"</p></div>`
                        );
                        if (response.files && response.files.length > 0) {
                            // response.files.forEach(file => {
                            //     fileDiv.append(`<div>📎 ${file.name}</div>`);
                            // });
                            $.each(response.files, function(index, file) {
                                const fileUrl = docFileRouteBase
                                    .replace('__DOC__', docId)
                                    .replace('__FILE__', file.id);
                                fileDiv.append(
                                    `<p style="font-size:1rem;"><a href="${fileUrl}" target="_blank"><span class="fa fa-star"></span> <span>${file.folder.name}</span>  <span style="font-family: Helvetica, Arial, Sans-Serif;">&#x2192;</span>  <span>${file.name}</span></a></p>`
                                );
                            });
                            fileDiv.removeClass('d-none');
                        } else {
                            fileDiv.html('<div>No files found for this document.</div>')
                                .removeClass('d-none');
                        }
                        fileDiv2.addClass('d-none');
                    },
                    error: function() {
                        alert('Failed to fetch files.');
                    }
                });
            });

            // $('#Assign-To-File').on('click', function() {
            //     var documentSlug = $(this).data('document-slug');
            //     var documentRef = $(this).data('document-ref');
            //     console.log(documentSlug,documentRef);
            //     $('#document_id_2').val(documentSlug);
            //     $('#file_ref_').text(documentRef);
            //     const fileDiv = $('#docAssignments');
            //     const fileDiv2 = $('#assigneToFile');
            //     fileDiv.addClass('d-none');
            //     fileDiv2.removeClass('d-none');

            // });
            $('#folder_id_2').change(function() {
                let folderId = $(this).val();

                if (!folderId) return; // Stop if no folder is selected

                $.ajax({
                    url: '/project/folder/get-files/' +
                        folderId, // Adjust the route to your API endpoint
                    type: 'GET',
                    success: function(response) {
                        let fileDropdown = $('#newFile_2');
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
            $('#saveAssigne2').click(function() {
                let documentId = $('#document_id_2').val();
                let fileId = $('#newFile_2').val();

                if (!fileId) {
                    alert('Please select a file.');
                    return;
                }

                $.ajax({
                    url: '/project/document/assign-document-bySlug', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        slug: documentId,
                        file_id: fileId
                    },
                    success: function(response) {
                        let fileDropdown = $('#newFile_2');
                        fileDropdown.closest('.form-group').addClass(
                            'd-none');
                        $('#folder_id_2').val('');
                        showHint(response.message);
                    },
                    error: function() {
                        alert('Failed to assign document. Please try again.');
                    }
                });
            });

        });

        $(document).on('click', '#Assign-To-File', function() {
            const documentSlug = window.currentDocSlug;
            const documentRef = window.currentDocRef;

            $('#document_id_2').val(documentSlug);
            $('#file_ref_').text(documentRef);


            let fileDropdown = $('#newFile_2');
            fileDropdown.closest('.form-group').addClass(
                'd-none');
            $('#folder_id_2').val('');
            const fileDiv = $('#docAssignments');
            const fileDiv2 = $('#assigneToFile');
            fileDiv.addClass('d-none');
            fileDiv2.removeClass('d-none');
        });

        window.showContextMenu = function(event, url, docSlug, reference) {
            event.preventDefault(); // Stop default right-click menu

            const menu = document.getElementById('contextMenu');
            menu.style.left = `${event.pageX - 270}px`;
            menu.style.top = `${event.pageY - 60}px`;
            menu.style.display = 'block';

            const previewLink = document.getElementById('Preview-Document');
            previewLink.href = '/' + url;
            previewLink.target = '_blank';
            const JUMPLink = document.getElementById('Jump');
            JUMPLink.href = `{{ url('/project/all-documents?slug=${docSlug}') }}`;
            JUMPLink.target = '_blank';
            assignBtn = document.getElementById('Assign-To-File');
            window.currentDocSlug = docSlug;
            window.currentDocRef = reference;
        };
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            flatpickr(".date", {
                enableTime: false,
                dateFormat: "Y-m-d", // Format: YYYY-MM-DD
                altInput: true,
                altFormat: "d.M.Y",
            });
            flatpickr(".datennn", {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true, // optional, e.g. "Jan" instead of "January"
                        dateFormat: "Y-m", // format submitted to the server
                        altFormat: "F Y", // format shown in the input
                        theme: "light" // or "dark"
                    })
                ]
            });

        });
    </script>
@endpush
