@extends('project_dashboard.layout.app')
@section('title', 'Project Home')
@section('content')
    <style>
        #btn-outline-primary {
            color: blue;
        }

        #btn-outline-primary:hover {
            color: white;
            /* Change text color to white on hover */
        }
    </style>
    <style>
        .chart-container {
            border: 1px solid #eea303;
            border-radius: 5px;
            width: 100%;
            padding: 10px;

        }

        .row_d {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .label {
            width: 140px;
            font-weight: bold;
        }

        .count-box {
            width: 40px;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            font-size: 14px;
            padding: 2px 0;
            margin-right: 5px;
            cursor: pointer;
        }

        .bar-container {
            height: 12px;
            flex: 1;
            background: #f0f0f0;
            border-radius: 3px;
            overflow: hidden;
        }

        .bar {
            height: 100%;
        }

        .blue {
            background-color: #3d73c5;
        }

        .green {
            background-color: #39ab19;

        }

        .red {
            background-color: red;
        }

        .brown {
            background-color: #b5662c;
        }
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">{{ $project->name }}</h2>
        </div>
        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('assign_users', $Account_Permissions ?? []))
            <div class="col-auto" style="padding-right: 0px;">
                <button type="button" data-toggle="modal" data-target="#assignUsersModal"
                    class="btn mb-2 btn-outline-warning"id="btn-outline-warning">Assign Users</button>
            </div>
        @endif
    </div>
    <div class="modal fade" id="assignUsersModal" tabindex="-1" role="dialog" aria-labelledby="assignUsersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignUsersModalLabel">Assign Users To Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="sendInvitationForm"method="post" action="{{ route('assign_users') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="multi-select2">Select Users</label>
                            <select class="form-control select2-multi xxx" id="multi-select2"
                                name="assigned_users[]"multiple>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if (in_array($user->id, $assigned_users)) selected @endif>
                                        {{ $user->name ? $user->name . ' - ' . $user->email : $user->email }}</option>
                                @endforeach
                            </select>
                        </div> <!-- form-group -->

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit"form="sendInvitationForm" class="btn btn-primary">Save</button>
                </div>
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

    <div style="display: flex; width:100%;">
        <div class="col-md-4">
            <div class="chart-container">
                <div class="row_d">
                    <div class="label">My Documents</div>
                    <div class="count-box blue" id="count-main">{{ $allUserDocuments }}</div>
                    <div class="bar-container">
                        <div class="bar blue" id="bar-main"></div>
                    </div>
                </div>

                <div class="row_d">
                    <div class="label">Pending Analysis</div>
                    <div class="count-box green" id="count-analysis">{{ $allPendingAnalysisUserDocuments }}</div>
                    <div class="bar-container">
                        <div class="bar green" id="bar-analysis"></div>
                    </div>
                </div>

                <div class="row_d">
                    <div class="label">Pending Assignment</div>
                    <div class="count-box red" id="count-assignment">{{ $allPendingAssignmentUserDocuments }}</div>
                    <div class="bar-container">
                        <div class="bar red" id="bar-assignment"></div>
                    </div>
                </div>

                <div class="row_d">
                    <div class="label">Need Narrative</div>
                    <div class="count-box brown" id="count-narrative">{{ $allNeedNarrativeUserDocuments }}</div>
                    <div class="bar-container">
                        <div class="bar brown" id="bar-narrative"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>


@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
        });
    </script>

    <script>
        // Values
        const values = {
            main: {{ $allUserDocuments }},
            analysis: {{ $allPendingAnalysisUserDocuments }},
            assignment: {{ $allPendingAssignmentUserDocuments }},
            narrative: {{ $allNeedNarrativeUserDocuments }},
        };

        // Get max value for scaling
        const max = Math.max(...Object.values(values));

        // Set bar widths as percentage of max
        document.getElementById("bar-main").style.width = `${(values.main / max) * 100}%`;
        document.getElementById("bar-analysis").style.width = `${(values.analysis / max) * 100}%`;
        document.getElementById("bar-assignment").style.width = `${(values.assignment / max) * 100}%`;
        document.getElementById("bar-narrative").style.width = `${(values.narrative / max) * 100}%`;
    </script>
    <script>
        document.getElementById("count-main").addEventListener("click", function() {
            window.location.href = "/project/all-documents?authUser=on";
        });
         document.getElementById("count-analysis").addEventListener("click", function() {
            window.location.href = "/project/all-documents?authUser=on&analysis_complete=0";
        });
         document.getElementById("count-assignment").addEventListener("click", function() {
            window.location.href = "/project/all-documents?authUser=on&not_assignment=on";
        });
    </script>
@endpush
