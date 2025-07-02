@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Files')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
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
            width: 1% !important;
        }



        .table-container th:nth-child(2),
        .table-container td:nth-child(2) {
            width: 4% !important;
        }

        .table-container th:nth-child(3),
        .table-container td:nth-child(3) {
            width: 51% !important;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 10% !important;
        }

        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 10% !important;
        }

        .table-container th:nth-child(6),
        .table-container td:nth-child(6) {
            width: 10% !important;
        }

        .table-container th:nth-child(7),
        .table-container td:nth-child(7) {
            width: 10% !important;
        }

        .table-container th:nth-child(8),
        .table-container td:nth-child(8) {
            width: 4% !important;
        }

        /* Maintain styles from your original table */
        .table-container tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
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
            <h2 class="h3 mb-0 page-title">{{ $folder->name }}</h2>
        </div>
        <div class="col-auto">
            @if ($folder->name != 'Recycle Bin' && $folder->name != 'Archive')
                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('create_file', $Project_Permissions ?? []))
                    <a type="button" href="{{ route('project.files.create') }}"
                        class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create File</a>
                @endif
            @endif
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
                        <table class="table datatables" id="dataTable-1">

                            <thead>
                                <tr>
                                    <th id="check"class="">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="select-all">
                                            <label class="custom-control-label" for="select-all"></label>
                                        </div>
                                    </th>

                                    <th><b>NO.</b></th>
                                    <th><b>File Name</b></th>
                                    <th><b>{{ $folder->label1 }}</b></th>
                                    <th><b>{{ $folder->label2 }}</b></th>
                                    <th><b>{{ $folder->label3 }}</b></th>
                                    <th><b>File Owner</b></th>

                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($all_files as $file)
                                    <tr id="file_{{ $file->slug }}">
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input row-checkbox"data-file-id="{{ $file->id }}"
                                                    id="checkbox-{{ $file->id }}" value="{{ $file->id }}">
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $file->id }}"></label>
                                            </div>
                                        </td>

                                        <td>{{ $file->code }}</td>
                                        <td><a class="l-link"style="color:rgb(80, 78, 78);" style="color:"
                                                href="{{ route('project.file-documents.index', $file->slug) }}">{{ $file->name }}</a>
                                        </td>
                                        <td>{{ $file->against ? $file->against->role : '_' }}</td>
                                        <td>{{ $file->start_date ? date('d-M-Y', strtotime($file->start_date)) : '_' }}
                                        </td>
                                        <td>{{ $file->end_date ? date('d-M-Y', strtotime($file->end_date)) : '_' }}</td>
                                        <td>{{ $file->user ? $file->user->name : '_' }}</td>


                                        <td>
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('edit_file', $Project_Permissions ?? []))
                                                    <a class="dropdown-item"
                                                        href="{{ route('project.files.edit', $file->slug) }}">Edit</a>

                                                    <a id="Change_Owner_btn_{{ $file->id }}"
                                                        class="dropdown-item change-owner-btn" href="javascript:void(0);"
                                                        data-file-id="{{ $file->id }}"data-file-owner-id="{{ $file->user_id }}">Change
                                                        Owner</a>
                                                @endif
                                                <a class="dropdown-item"
                                                    href="{{ route('project.file-documents.index', $file->slug) }}">Chronology
                                                    of Events</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('project.file-attachments.index', ['id' => $file->slug, 'type' => '1']) }}">Synopsis</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('project.file-attachments.index', ['id' => $file->slug, 'type' => '2']) }}">Contractual
                                                    Position</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('project.file-attachments.index', ['id' => $file->slug, 'type' => '3']) }}">Cause-and-Effect
                                                    Analysis</a>

                                                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('cope_move_file', $Project_Permissions ?? []))
                                                    <a class="dropdown-item copy_move_file"href="javascript:void(0);"
                                                        data-file-id="{{ $file->slug }}"data-type="Copy">Copy</a>
                                                    <a class="dropdown-item copy_move_file"href="javascript:void(0);"
                                                        data-file-id="{{ $file->slug }}"data-type="Move">Move</a>
                                                @endif
                                                <a class="dropdown-item export_file" href="javascript:void(0);"
                                                    data-file-id="{{ $file->slug }}">Export</a>
                                                <a class="dropdown-item exportGanttChart" href="javascript:void(0);"
                                                    data-file-id="{{ $file->slug }}">Export Gantt Chart</a>
                                                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('delete_file', $Project_Permissions ?? []))
                                                    <a class="dropdown-item"
                                                        href="javascript:void(0);"onclick="confirmArchive('{{ route('project.files.archive', $file->id) }}')">Archive</a>
                                                    <a class="dropdown-item text-danger"
                                                        href="javascript:void(0);"onclick="confirmDelete('{{ route('project.files.delete', $file->id) }}')">Delete</a>
                                                @endif
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
    <div class="modal fade" id="changeOwnerModal" tabindex="-1" role="dialog" aria-labelledby="changeOwnerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeOwnerModalLabel">Change File Owner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeOwnerForm">
                        @csrf
                        <input type="hidden" id="fileId" name="file_id">
                        <div class="form-group">
                            <label for="newOwner">Select New Owner</label>
                            <select class="form-control" id="newOwner" name="new_owner_id" required>
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
                    <button type="button" class="btn btn-primary" id="saveOwnerChange">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width:800px;">
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
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subtitle1">Section1</label>
                                    <input type="text" required name="subtitle1" class="form-control"
                                        placeholder="Subtitle 1" id="subtitle1" value="Synopsis">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subtitle2">Section2</label>
                                    <input type="text" required name="subtitle2" class="form-control"
                                        placeholder="Subtitle 2" id="subtitle2" value="Chronology of Events">
                                </div>
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subtitle3">Section3</label>
                                    <input type="text" required name="subtitle3" class="form-control"
                                        placeholder="Subtitle 3" id="subtitle3" value="Contractual Position">
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="subtitle4">Section4</label>
                                    <input type="text" required name="subtitle4" class="form-control"
                                        placeholder="Subtitle 4" id="subtitle4" value="Cause-and-Effect Analysis">
                                </div>
                            </div>

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
                                    <input type="number" name="Start" id="Start" class="form-control"
                                        placeholder="Start" value="1" style="width: 30%;margin-left:2%;"min="1"
                                        oninput="this.value = Math.max(1, this.value)">
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
    <div class="modal fade" id="copyToModal" tabindex="-1" role="dialog" aria-labelledby="copyToModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="copyToModalLabel">
                        <spam id="type">Copy</spam> File To another Folder
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="assigneToForm">
                        @csrf
                        <input type="hidden" id="fileId_" name="file_id">
                        <input type="hidden" id="action_type" name="action_type">
                        <div class="form-group">
                            <label for="folder_id">Select Folder</label>
                            <select class="form-control" id="folder_id" required name="folder_id">
                                <option value="" disabled selected>Select Folder</option>
                                @foreach ($folders as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
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

                        <input type="hidden" id="file__slugfff" name="file_slug">
                        <div class="form-group">
                            <label>Time Frame</label>
                            <div style="display: flex;    margin-top: -5px;">
                                <div class="custom-control custom-radio" style="width: 50%;">
                                    <input type="radio" id="auto" name="timeframe" value="auto"
                                        class="custom-control-input" checked>
                                    <label class="custom-control-label" for="auto">Auto</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 50%;">
                                    <input type="radio" id="fixed_dates" name="timeframe" class="custom-control-input"
                                        value="fixed_dates">
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
                                    <input type="radio" id="d2" name="raw_height" class="custom-control-input"
                                        value="1.5" checked>
                                    <label class="custom-control-label" for="d2">1.5</label>
                                </div>
                                <div class="custom-control custom-radio"style="width: 33.3%;">
                                    <input type="radio" id="d3" name="raw_height" class="custom-control-input"
                                        value="2">
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
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
            $('.copy_move_file').on('click', function() {
                var fileId_ = $(this).data('file-id');
                var action_type = $(this).data('type');

                $('#fileId_').val(fileId_);
                $('#action_type').val(action_type);
                $('#folder_id').val('');

                document.getElementById('type').innerText = action_type;

                $('#copyToModal').modal('show'); // Show the modal
            });

            $('#saveCopyDoc').click(function() {

                //let documentId = $('#documentId_').val();
                let folderId = $('#folder_id').val();
                let fileId = $('#fileId_').val();
                let actionType = $('#action_type').val();

                if (!folderId) {
                    alert('Please select a file.');
                    return;
                }

                $.ajax({
                    url: '/project/copy_move_file', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        folder_id: folderId,
                        file_id: fileId,
                        action_type: actionType
                    },
                    success: function(response) {
                        if (response.status == 'error') {
                            alert("⚠️ " + response.message);
                        } else {
                            if (actionType == 'Move') {

                                document.getElementById('file_' + fileId)?.remove();

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
            ///////////////////////////////////////////////////////
            $('input[name="timeframe"]').on('change', function() {
                if ($('#fixed_dates').is(':checked')) {
                    $('#datesGroup').removeClass('d-none');
                    $('#start_date_gantt, #end_date_gantt').attr('required', true);
                } else {
                    $('#datesGroup').addClass('d-none');
                    $('#start_date_gantt, #end_date_gantt').removeAttr('required');
                }
            });

            $('.exportGanttChart').on('click', function() {
                var fileId_ = $(this).data('file-id');
                $('#file__slugfff').val(fileId_);
                $('input[name="timeframe"][value="auto"]').prop('checked', true);
                $('#datesGroup').addClass('d-none');
                $('#start_date_gantt, #end_date_gantt').removeAttr('required');
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
            /////////////////////////////////////////////////
            $('.dropdown-toggle').dropdown();


            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
            $("#check").removeClass("sorting_asc");

            $('.export_file').on('click', function() {
                const fileId = $(this).data('file-id');
                $('#file_id111').val(fileId);
                $('#exportModal').modal('show');
            });
            $('input[name="formate_type2"]').on('change', function() {
                if ($('#formate2').is(':checked')) {
                    $('#extraOptions2').removeClass('d-none');
                    $('#Prefix2').attr('required', true);
                    $('#sn2').attr('required', true);
                    $('#Start').attr('required', true);
                    $('input[name="ref_part2"]').attr('required', true);
                } else {
                    $('#extraOptions2').addClass('d-none');

                    // Clear all inputs inside extraOptions
                    $('#extraOptions2').find('input[type="text"], input[type="number"]').val('');
                    $('#extraOptions2').find('input[type="radio"]').prop('checked', false);

                    // Remove required attributes
                    $('#Prefix2').removeAttr('required');
                    $('#sn2').removeAttr('required');
                    $('#Start').removeAttr('required');
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
                    url: '/export-fill', // Replace with real route
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
                const checkboxes = document.getElementsByClassName('row-checkbox');
                for (let checkbox of checkboxes) {
                    checkbox.checked = this.checked;
                }
                const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

                // If more than one checkbox is checked, display the Action-DIV, else hide it
                if (checkedCheckboxes.length > 1) {
                    actionDiv.style.display = 'block';
                } else {
                    actionDiv.style.display = 'none';
                }
            });



            $('.change-owner-btn').on('click', function() {
                var fileId = $(this).data('file-id');
                var fileOwner = $(this).data('file-owner-id');
                $('#fileId').val(fileId);
                $('#newOwner').val(fileOwner); // Set the document ID in the hidden input
                $('#changeOwnerModal').modal('show'); // Show the modal
            });

            // Handle the form submission via AJAX
            $('#saveOwnerChange').on('click', function() {
                var formData = $('#changeOwnerForm').serialize(); // Serialize form data
                var fileId = $('#fileId').val(); // Get the document ID

                $.ajax({
                    url: "{{ route('project.file.change-owner') }}", // Route for changing owner
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#changeOwnerModal').modal('hide');
                            alert('Owner changed successfully!');
                            // Hide the modal
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Failed to change owner.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });
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
                "targets": 7, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }]
        });
    </script>
    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this File? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }

        function confirmArchive(url) {
            if (confirm('Are you sure you want to archive this File? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>


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
