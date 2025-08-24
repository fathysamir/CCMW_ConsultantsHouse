@extends('project_dashboard.layout.app')
@section('title', 'Project Home')
@section('content')

    <style>
        body {
            height: 100vh;
            /* تحديد ارتفاع الصفحة بنسبة لحجم الشاشة */
            overflow: hidden;
            /* منع التمرير */
        }

        #btn-outline-primary {
            color: blue;
        }

        #btn-outline-primary:hover {
            color: white;
            /* Change text color to white on hover */
        }

        .my-4 {
            margin-bottom: 1rem !important;
        }

        .chart-container {
            border: 1px solid #eea303;
            border-radius: 5px;
            width: 100%;
            padding: 10px;

        }

        .row_d {
            display: flex;
            align-items: center;
            margin: 0px 0 10.8px 0;
        }

        .label {
            width: 160px;
            font-weight: bold;
        }

        .label2 {
            padding-top: 5px;
            padding-bottom: 6.5px;

            font-size: 12px;
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

        .black {
            background-color: #000000;
        }

        .Barbel {
            background-color: #850bcb;
        }

        .orange {
            background-color: #ef8607;
        }

        .darkolivegreen {
            background-color: #556b2f;
        }

        .foshia {
            background-color: #915c83
        }

        .summary-box {
            display: flex;
            align-items: center;
            border: 1px solid #eea303;
            border-radius: 5px;
            width: 100%;
            padding: 10px;

        }

        .info {
            flex: 1;
        }

        .info-row {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }

        .info-label {
            flex: 1;
            font-size: 14px;
        }



        canvas {
            width: 80px;
            height: 80px;
        }

        .chart-container2 {
            width: 100%;
            margin: auto;
            text-align: center;



        }

        h3 {
            font-size: 14px;
            margin-bottom: 5px;
            color: #0b3a4b;
        }

        .chart-container_G {
            position: relative;
            height: 97%;
            width: 100%;



        }

        .fancy-btn {
            background: linear-gradient(135deg, #2398ff, #00f2fe);
            color: white;
            border: none;
            padding-top: 3px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 30px;
            cursor: pointer;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.8);
            transition: all 0.3s ease;
            width: 100%;
            height: 27%;
        }

        .fancy-btn:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4), 0 0 20px rgba(79, 172, 254, 0.6);
            transform: translateY(-2px);
        }

        .fancy-btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .fancy-btn2 {
            background: linear-gradient(135deg, #4a4a4a, #bbb5b5);
            color: white;
            border: none;
            padding-top: 3px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            height: 27%;
        }

        .fancy-btn2:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6), 0 0 20px rgba(100, 100, 100, 0.6);
            transform: translateY(-2px);
        }

        .fancy-btn2:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
    </style>
    <style>
        .label-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .tooltip-text {
            visibility: hidden;
            background-color: black;
            color: #fff;
            text-align: center;
            padding: 5px 8px;
            border-radius: 4px;
            position: absolute;
            z-index: 999;
            bottom: 125%;
            /* position above the label */
            left: 60%;
            transform: translateX(-50%);
            /* white-space: nowrap; */
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s;
        }


        .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            /* Arrow points down */
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: black transparent transparent transparent;
        }

        .label-wrapper:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
            width: 200px !important;
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

    <div style="display: flex; width:100%; margin-top:-15px;">
        <div class="col-md-6" style="padding-right:0px !important;padding-left:0px !important">
            <div style="display: flex; width:100%; height:324px;">
                <div class="col-md-8" style="padding-right:0px !important;padding-left:0px !important">
                    <div class="summary-box">
                        <div class="info">
                            <div class="info-row" style="margin-top: 0px;">
                                <div class="label-wrapper">
                                    <div class="label">Total Documents</div>
                                    <div class="tooltip-text">
                                        <span>{!! $labels['allUserDocuments'] !!}</span>
                                    </div>

                                </div>
                                <div class="count-box blue" id="total-docs">{{ $allUserDocuments }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label-wrapper">
                                    <div class="label">Active Documents</div>
                                    <div class="tooltip-text">
                                        <span>{!! $labels['allActiveUserDocuments'] !!}</span>
                                    </div>
                                </div>
                                <div class="count-box green" id="active-docs">{{ $allActiveUserDocuments }}</div>
                            </div>
                            <div class="info-row" style="margin-bottom: 0px;">
                                <div class="label-wrapper">
                                    <div class="label">Assessed Not To Pursue</div>
                                    <div class="tooltip-text">
                                        <span style="width:100% !important;">
                                            {!! $labels['allInactiveUserDocuments'] !!}
                                        </span>
                                    </div>
                                </div>
                                <div class="count-box red" id="not-pursue">{{ $allInactiveUserDocuments }}</div>
                            </div>
                        </div>
                        <canvas id="pieChart" width="80" height="80"></canvas>

                    </div>
                    <div class="chart-container" style="margin-top: 5px;">
                        <div class="row_d">

                            <div class="label-wrapper">
                                <div class="label">Chronology Documents</div>
                                <div class="tooltip-text">
                                    <span>{!! $labels['allAssignmentUserDocuments'] !!}</span>
                                </div>

                            </div>
                            <div class="count-box blue" id="assignment_docs">{{ $allAssignmentUserDocuments }}</div>
                            <div class="bar-container">
                                <div class="bar blue" id="bar-assignment-docs"></div>
                            </div>
                        </div>

                        <div class="row_d">

                            <div class="label-wrapper">
                                <div class="label">For Claim</div>
                                <div class="tooltip-text">
                                    <span>{!! $labels['allForClaimUserDocuments'] !!}</span>
                                </div>

                            </div>
                            <div class="count-box green" id="forClaim_docs">{{ $allForClaimUserDocuments }}</div>
                            <div class="bar-container">
                                <div class="bar green" id="bar-forClaim-docs"></div>
                            </div>
                        </div>

                        <div class="row_d">

                            <div class="label-wrapper">
                                <div class="label">Need Narrative</div>
                                <div class="tooltip-text">
                                    <span>{!! $labels['allNeedNarrativeUserDocuments'] !!}</span>
                                </div>

                            </div>
                            <div class="count-box brown" id="count-narrative">{{ $allNeedNarrativeUserDocuments }}</div>
                            <div class="bar-container">
                                <div class="bar brown" id="bar-narrative"></div>
                            </div>
                        </div>
                        <div class="row_d">

                            <div class="label-wrapper">
                                <div class="label">Have Contractual Tag</div>
                                <div class="tooltip-text">
                                    <span>{!! $labels['allHaveConTagsUserDocuments'] !!}</span>
                                </div>

                            </div>
                            <div class="count-box red" id="count-have-tags">{{ $allHaveConTagsUserDocuments }}</div>
                            <div class="bar-container">
                                <div class="bar red" id="bar-have-tags"></div>
                            </div>
                        </div>
                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Notice Of Claim</div>
                                <div class="tooltip-text">
                                    <span>{!! $labels['allHaveConTagsNoticeClaimUserDocuments'] !!}</span>
                                </div>

                            </div>
                            <div class="count-box Barbel" id="count-have-tags-noticed">
                                {{ $allHaveConTagsNoticeClaimUserDocuments }}</div>
                            <div class="bar-container">
                                <div class="bar Barbel" id="bar-have-tags-noticed"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" style="padding-right:0px !important; height:100%;">
                    <div class="chart-container" style="height: 49%;">
                        <div class="chart-container2">
                            <div class="label-wrapper">
                                <div class="label"style="width:100%;">Analysis % Complete of the Open Claim Files</div>
                                <div class="tooltip-text"style=" width: 450px !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['percent1'] !!}
                                    </span>
                                </div>
                            </div>
                            <canvas id="gaugeChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-container" style="margin-top: 5px;height: 49%;">
                        <div class="chart-container2">
                            <div class="label-wrapper">
                                <div class="label" style="width:100%;">Window Analysis % Complete</div>
                                <div class="tooltip-text">
                                    <span style="width:100% !important;">
                                        {!! $labels['percent2'] !!}
                                    </span>
                                </div>
                            </div>
                            <canvas id="gaugeChart2"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-container" style="margin-top: 5px; height:295px;">
                <div style="display: flex; width:100%;">
                    <div class="col-md-8" style="padding-right:0px !important;padding-left:0px !important">
                        <div class="row_d" style="@if ($FileVariation6['label'] == '') margin-bottom:19px; @endif">
                            <div class="label-wrapper" style="width: 85%;">
                                <div class="label2">T - Total Variation Files</div>
                                <div class="tooltip-text"style="left: 30%;">
                                    <span style="width:100% !important;">
                                        {!! $labels['ActiveOpenClaimFileVariation'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box darkolivegreen" id="">
                                {{ $ActiveOpenClaimFileVariation }}
                            </div>
                        </div>
                        <div class="row_d"style="@if ($FileVariation6['label'] == '') margin-bottom:19px; @endif">
                            <div class="label-wrapper"style="width: 85%;">
                                <div class="label2">1 - Need {{ $FileVariation1['label'] }}</div>
                                <div class="tooltip-text"style="left: 30%;">
                                    <span style="width:100% !important;">
                                        {!! $labels['FileVariation1'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box darkolivegreen" id="">{{ $FileVariation1['value'] }}</div>
                        </div>
                        <div class="row_d"style="@if ($FileVariation6['label'] == '') margin-bottom:19px; @endif">
                            <div class="label-wrapper"style="width: 85%;">
                                <div class="label2">2 - Need {{ $FileVariation2['label'] }}</div>
                                <div class="tooltip-text"style="left: 30%;">
                                    <span style="width:100% !important;">
                                        {!! $labels['FileVariation2'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box darkolivegreen" id="">{{ $FileVariation2['value'] }}</div>
                        </div>
                        <div class="row_d"style="@if ($FileVariation6['label'] == '') margin-bottom:19px; @endif">
                            <div class="label-wrapper"style="width: 85%;">
                                <div class="label2">3 - Need {{ $FileVariation3['label'] }}</div>
                                <div class="tooltip-text"style="left: 30%;">
                                    <span style="width:100% !important;">
                                        {!! $labels['FileVariation3'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box darkolivegreen" id="">{{ $FileVariation3['value'] }}</div>
                        </div>
                        <div class="row_d"style="@if ($FileVariation6['label'] == '') margin-bottom:19px; @endif">
                            <div class="label-wrapper"style="width: 85%;">
                                <div class="label2">4 - Need {{ $FileVariation4['label'] }}</div>
                                <div class="tooltip-text"style="left: 30%;">
                                    <span style="width:100% !important;">
                                        {!! $labels['FileVariation4'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box darkolivegreen" id="">{{ $FileVariation4['value'] }}</div>
                        </div>
                        <div class="row_d"style="@if ($FileVariation6['label'] == '') margin-bottom:19px; @endif">
                            <div class="label-wrapper"style="width: 85%;">
                                <div class="label2">5 - Need {{ $FileVariation5['label'] }}</div>
                                <div class="tooltip-text"style="left: 30%;">
                                    <span style="width:100% !important;">
                                        {!! $labels['FileVariation5'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box darkolivegreen" id="">{{ $FileVariation5['value'] }}</div>
                        </div>
                        @if ($FileVariation6['label'] != '')
                            <div class="row_d"style="margin-bottom:16px;">
                                <div class="label-wrapper"style="width: 85%;">
                                    <div class="label2">6 - Need {{ $FileVariation6['label'] }}</div>
                                    <div class="tooltip-text" style="left: 30%;">
                                        <span style="width:100% !important;">
                                            {!! $labels['FileVariation6'] !!}
                                        </span>
                                    </div>
                                </div>
                                <div class="count-box darkolivegreen" id="">{{ $FileVariation6['value'] }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4" style="padding-right:0px !important;padding-left:0px !important">
                        <div class="chart-container_G">
                            <canvas id="documentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6"style="padding-right:0px !important">
            <div style="display: flex; width:100%;max-height:500px;">
                <div class="col-md-4" style="padding-right:0px !important;padding-left:0px !important; height:500px;">
                    <div class="chart-container" style="text-align: center; height:100%">
                        <div style="height: 23%">

                            <div class="label-wrapper">
                                <div class="label" style="width:100%;padding-top: 10%;">Active Documents Pending Analysis
                                </div>
                                <div class="tooltip-text" style="bottom: 47px !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['allPendingAnalysisUserDocuments'] !!}
                                    </span>
                                </div>
                            </div>
                            <div style="display: block; justify-content: center; align-items: center;">
                                <div class="count-box green" id="count-pending-analysis" style="display: inline-block">
                                    {{ $allPendingAnalysisUserDocuments }}
                                </div>
                            </div>
                        </div>

                        <div style="height: 23%">

                            <div class="label-wrapper">
                                <div class="label"style="width:100%;padding-top: 10%;">Active Documents Pending
                                    Assignments
                                </div>
                                <div class="tooltip-text" style="bottom: 47px !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['allPendingAssignmentUserDocuments'] !!}
                                    </span>
                                </div>
                            </div>
                            <div style="display: block; justify-content: center; align-items: center;">
                                <div class="count-box Barbel" id="count-pending-assignment"style="display: inline-block">
                                    {{ $allPendingAssignmentUserDocuments }}
                                </div>
                            </div>
                        </div>

                        <hr style="border-top: 3px solid #168bff;">
                        <div style="height: 23%">

                            <div class="label-wrapper">
                                <div class="label"style="width:100%;padding-top: 10%;">Active Open Claim Files Need First
                                    Claim
                                    Notice</div>
                                <div class="tooltip-text" style="bottom: 47px !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['ActiveOpenClaimFilesNeed1ClaimNotice'] !!}
                                    </span>
                                </div>
                            </div>
                            <div style="display: block; justify-content: center; align-items: center;">
                                <div class="count-box green" id="count-need-1-claim-notice"style="display: inline-block">
                                    {{ $ActiveOpenClaimFilesNeed1ClaimNotice }}
                                </div>
                            </div>
                        </div>
                        <div style="height: 23%">

                            <div class="label-wrapper">
                                <div class="label"style="width:100%; padding-top: 10%;">Active Open Claim Files Need
                                    Further
                                    Notice</div>
                                <div class="tooltip-text"style="bottom: 47px !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['ActiveOpenClaimFilesNeedFurtherNotice'] !!}
                                    </span>
                                </div>
                            </div>
                            <div style="display: block; justify-content: center; align-items: center; margin-bottom:20px;">
                                <div class="count-box Barbel" id="count-pending-assignment"style="display: inline-block">
                                    {{ $ActiveOpenClaimFilesNeedFurtherNotice }}
                                </div>
                            </div>
                        </div>


                    </div>

                </div>
                <div class="col-md-8" style="padding-right:0px !important;height:500px;">

                    <div class="summary-box">
                        <div class="info">
                            <div class="info-row" style="margin-top: 0px;">
                                <div class="label-wrapper">
                                    <div class="label">Active Claim Files</div>

                                    <div class="tooltip-text">
                                        <span style="width:100% !important;">
                                            {!! $labels['ActiveClaimFile'] !!}
                                        </span>
                                    </div>
                                </div>
                                <div class="count-box blue" id="ActiveClaimFile">{{ $ActiveClaimFile }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label-wrapper">
                                    <div class="label">Open Claim Files</div>

                                    <div class="tooltip-text">
                                        <span style="width:100% !important;">
                                            {!! $labels['ActiveOpenClaimFile'] !!}
                                        </span>
                                    </div>
                                </div>
                                <div class="count-box green" id="ActiveOpenClaimFile">{{ $ActiveOpenClaimFile }}</div>
                            </div>
                            <div class="info-row" style="margin-bottom: 0px;">
                                <div class="label-wrapper">
                                    <div class="label">Closed Claim Files</div>

                                    <div class="tooltip-text">
                                        <span style="width:100% !important;">
                                            {!! $labels['ActiveClosedClaimFile'] !!}
                                        </span>
                                    </div>
                                </div>
                                <div class="count-box red" id="ActiveClosedClaimFile">{{ $ActiveClosedClaimFile }}</div>
                            </div>
                        </div>
                        <canvas id="pieChart2" width="80" height="80"></canvas>

                    </div>
                    <div class="chart-container" style="margin-top: 5px;height: 75.7%;">
                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Active Open Claim Files</div>

                                <div class="tooltip-text">
                                    <span style="width:100% !important;">
                                        {!! $labels['ActiveOpenClaimFile'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box blue" id="ActiveOpenClaimFile2">{{ $ActiveOpenClaimFile }}</div>
                            <div class="bar-container">
                                <div class="bar blue" id="bar-ActiveOpenClaimFile2"></div>
                            </div>
                        </div>

                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Time</div>

                                <div class="tooltip-text" style="left: 10% !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['ActiveOpenClaimFileTime'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box green" id="ActiveOpenClaimFileTime">{{ $ActiveOpenClaimFileTime }}
                            </div>
                            <div class="bar-container">
                                <div class="bar green" id="bar-ActiveOpenClaimFileTime"></div>
                            </div>
                        </div>

                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Prolongation Cost</div>

                                <div class="tooltip-text" style="left: 25% !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['ActiveOpenClaimFileProlongationCost'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box red" id="ActiveOpenClaimFileProlongationCost">
                                {{ $ActiveOpenClaimFileProlongationCost }}</div>
                            <div class="bar-container">
                                <div class="bar red" id="bar-ActiveOpenClaimFileProlongationCost"></div>
                            </div>
                        </div>
                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Variation</div>

                                <div class="tooltip-text"style="left: 20% !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['ActiveOpenClaimFileVariation'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box brown" id="ActiveOpenClaimFileVariation">
                                {{ $ActiveOpenClaimFileVariation }}</div>
                            <div class="bar-container">
                                <div class="bar brown" id="bar-ActiveOpenClaimFileVariation"></div>
                            </div>
                        </div>
                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Disruption</div>

                                <div class="tooltip-text"style="left: 20% !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['ActiveOpenClaimFileDisruption'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box black" id="ActiveOpenClaimFileDisruption">
                                {{ $ActiveOpenClaimFileDisruption }}</div>
                            <div class="bar-container">
                                <div class="bar black" id="bar-ActiveOpenClaimFileDisruption"></div>
                            </div>
                        </div>

                        <hr style="border-top: 3px solid #168bff;">

                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Need Chronology</div>

                                <div class="tooltip-text" style="left: 40% !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['needChronology'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box Barbel" id="needChronology">{{ $needChronology }}
                            </div>
                            <div class="bar-container">
                                <div class="bar Barbel" id="bar-needChronology"></div>
                            </div>
                        </div>

                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Need Synopsis</div>

                                <div class="tooltip-text"style="left: 40% !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['needSynopsis'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box orange" id="needSynopsis">
                                {{ $needSynopsis }}</div>
                            <div class="bar-container">
                                <div class="bar orange" id="bar-needSynopsis"></div>
                            </div>
                        </div>
                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Need Contractual A</div>

                                <div class="tooltip-text"style="left: 40% !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['needContractualA'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box darkolivegreen" id="needContractualA">
                                {{ $needContractualA }}</div>
                            <div class="bar-container">
                                <div class="bar darkolivegreen" id="bar-needContractualA"></div>
                            </div>
                        </div>
                        <div class="row_d">
                            <div class="label-wrapper">
                                <div class="label">Need Cause & Effect A</div>

                                <div class="tooltip-text"style="left: 40% !important;">
                                    <span style="width:100% !important;">
                                        {!! $labels['needCauseEffectA'] !!}
                                    </span>
                                </div>
                            </div>
                            <div class="count-box foshia" id="needCauseEffectA">
                                {{ $needCauseEffectA }}</div>
                            <div class="bar-container">
                                <div class="bar foshia" id="bar-needCauseEffectA"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="chart-container" style="margin-top: 5px; height:119px;">
                <button
                    class="{{ $project_dashboard_class_name }}"onclick="window.location.href='{{ url('/project') }}'">Project
                    Dashboard</button>
                <button class="{{ $my_dashboard_class_name }}"
                    style="margin-top: 8px;"onclick="window.location.href='{{ url('/project?user=' . auth()->user()->code) }}'">My
                    Dashboard</button>
                <div style="display: flex;margin-top: 8px; width:100%">
                    <select class="form-control" id="owner" style="width: 80%;">
                        <option value="" selected disabled>Select User</option>
                        @foreach ($project_users as $user)
                            <option value="{{ $user->code }}" {{ $selectedValue == $user->code ? 'selected' : '' }}>
                                {{ $user->name }}</option>
                        @endforeach
                    </select>
                    <button id="dashboardBtn" class="{{ $user_dashboard_class_name }}" disabled
                        style="margin-left:5px;">
                        Selected User Dashboard
                    </button>
                </div>
            </div>

        </div>

    </div>


@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('documentChart').getContext('2d');

            // Lightened green gradient with RGBA
            const gradient = ctx.createLinearGradient(0, 0, 400, 400);
            gradient.addColorStop(0, 'rgba(85, 107, 47, 1)'); // light olive green
            gradient.addColorStop(1, 'rgba(180, 211, 178, 0.2)'); // soft pale green

            let labels = ['T', '5', '4', '3', '2', '1'];
            let values = [
                {{ $ActiveOpenClaimFileVariation }},
                {{ $FileVariation5['value'] }},
                {{ $FileVariation4['value'] }},
                {{ $FileVariation3['value'] }},
                {{ $FileVariation2['value'] }},
                {{ $FileVariation1['value'] }}
            ];

            if ("{{ $FileVariation6['label'] }}" !== '') {
                labels.splice(1, 0, '6');
                values.splice(1, 0, {{ $FileVariation6['value'] }});
            }

            const chartData = {
                labels: labels,
                datasets: [{
                    label: ' ',
                    data: values,
                    backgroundColor: gradient,
                    borderColor: 'rgba(85, 107, 47, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    hoverBackgroundColor: 'rgba(200, 230, 200, 1)',
                    barThickness: 17,
                    maxBarThickness: 20
                }]
            };

            const config = {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' files';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                stepSize: 5
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuad'
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>
    <script>
        const selectOwner = document.getElementById('owner');
        const dashboardBtn = document.getElementById('dashboardBtn');

        // Enable button when selection changes
        selectOwner.addEventListener('change', function() {
            dashboardBtn.disabled = !this.value;
        });

        // Redirect on click
        dashboardBtn.addEventListener('click', function() {
            const selectedUser = selectOwner.value;
            if (selectedUser) {
                window.location.href = "{{ url('/project') }}" + "?user=" + encodeURIComponent(selectedUser);
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        const ctx = document.getElementById('gaugeChart').getContext('2d');

        const segmentSizes = [10, 15, 20, 25, 30]; // must sum to 100
        const segmentColors = ['#39ab19', '#9cd323', '#fbc02d', '#ff8000', '#d32f2f'];
        let startThickness = [];
        let endThickness = [];
        const minThickness = 1;
        const maxThickness = 12.5;
        const steps = 5;

        // Linearly spaced values between min and max
        const thicknessStep = (maxThickness - minThickness) / steps;

        startThickness[0] = minThickness;
        endThickness[0] = minThickness + thicknessStep;

        startThickness[1] = endThickness[0];
        endThickness[1] = startThickness[1] + thicknessStep;

        startThickness[2] = endThickness[1];
        endThickness[2] = startThickness[2] + thicknessStep;

        startThickness[3] = endThickness[2];
        endThickness[3] = startThickness[3] + thicknessStep;

        startThickness[4] = endThickness[3];
        endThickness[4] = maxThickness;
        const taperedArcPlugin = {
            id: 'taperedArc',
            beforeDatasetsDraw(chart) {
                const {
                    ctx,
                    chartArea,
                    chartArea: {
                        top,
                        bottom,
                        left,
                        right
                    }
                } = chart;
                const cx = left + (right - left) / 2;
                const cy = bottom;
                const radius = Math.min(right - left, bottom - top) / 1.2;

                const total = segmentSizes.reduce((a, b) => a + b, 0);
                let startAngle = Math.PI;

                for (let i = 0; i < segmentSizes.length; i++) {
                    const segAngle = (segmentSizes[i] / total) * Math.PI;
                    const endAngle = startAngle + segAngle;

                    // Draw with taper from startThickness → endThickness within the segment
                    ctx.beginPath();
                    ctx.strokeStyle = segmentColors[i];
                    ctx.lineCap = 'butt';

                    // We draw tiny arcs from start to end, increasing thickness gradually
                    const steps = 50;
                    for (let s = 0; s < steps; s++) {
                        const t1 = s / steps;
                        const t2 = (s + 1) / steps;
                        const angle1 = startAngle + (segAngle * t1);
                        const angle2 = startAngle + (segAngle * t2);

                        const thickness1 = startThickness[i] + (endThickness[i] - startThickness[i]) * t1;
                        const thickness2 = startThickness[i] + (endThickness[i] - startThickness[i]) * t2;

                        // Draw arc segment with average thickness
                        const avgThickness = (thickness1 + thickness2) / 2;
                        ctx.lineWidth = avgThickness;
                        ctx.beginPath();
                        ctx.arc(cx, cy - 8, radius - avgThickness / 2, angle1, angle2);
                        ctx.stroke();
                    }

                    startAngle = endAngle;
                }
            }
        };

        // Fixed thickness for each segment

        const needlePlugin = {
            id: 'needle',
            beforeInit(chart) {
                chart._needleValue = 0; // Start from 0
            },
            afterDraw(chart) {
                const {
                    ctx,
                    chartArea,
                    options
                } = chart;
                const target = options.needleValue || 0;

                const cx = (chartArea.left + chartArea.right) / 2;
                const cy = chartArea.bottom;
                const outerRadius = Math.min(chartArea.width, chartArea.height * 2) / 2 - 10;

                // Speed control: smaller value = slower animation
                const speed = 0.005;

                if (Math.abs(chart._needleValue - target) > speed) {
                    chart._needleValue += (target > chart._needleValue ? speed : -speed);
                } else {
                    chart._needleValue = target;
                }

                const angle = Math.PI + (chart._needleValue / 100) * Math.PI;

                ctx.save();
                ctx.translate(cx, cy - 8);
                ctx.rotate(angle);
                ctx.beginPath();
                ctx.moveTo(0, -3);
                ctx.lineTo(outerRadius - 6, 0);
                ctx.lineTo(0, 3);
                ctx.closePath();
                ctx.fillStyle = '#0b3a4b';
                ctx.fill();
                ctx.restore();

                // Center dot
                ctx.beginPath();
                ctx.arc(cx, cy - 8, 6, 0, Math.PI * 2);
                ctx.fillStyle = '#0b3a4b';
                ctx.fill();

                // Text
                ctx.font = 'bold 16px Arial';
                ctx.fillStyle = '#0b3a4b';
                ctx.textAlign = 'center';
                ctx.fillText(Math.round(chart._needleValue) + '%', cx, cy - outerRadius / 2 + 0);

                // Continue animation
                if (chart._needleValue !== target) {
                    window.requestAnimationFrame(() => chart.draw());
                }
            }
        };


        // Register plugin
        Chart.register(needlePlugin);




        let percentage = {{ $percent1 }}; // your dynamic value

        const gaugeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Very Low', 'Low', 'Medium', 'High', 'Critical'],
                datasets: [{
                    data: segmentSizes,
                    backgroundColor: ['transparent'], // hide default arcs
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: {
                responsive: true,
                aspectRatio: 2,
                needleValue: percentage, // 👈 pass value here
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            },
            plugins: [taperedArcPlugin, needlePlugin]
        });
    </script>
    <script>
        const ctx2 = document.getElementById('gaugeChart2').getContext('2d');

        let percentage2 = {{ $percent2 }}; // your dynamic value

        const gaugeChart2 = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Very Low', 'Low', 'Medium', 'High', 'Critical'],
                datasets: [{
                    data: segmentSizes,
                    backgroundColor: ['transparent'], // hide default arcs
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: {
                responsive: true,
                aspectRatio: 2,
                needleValue: percentage2, // 👈 pass value here
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            },
            plugins: [taperedArcPlugin, needlePlugin]
        });
    </script>
    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
        });
    </script>

    <script>
        const valuesBox4 = {
            main: {{ $ActiveOpenClaimFile }},
            time: {{ $ActiveOpenClaimFileTime }},
            ProlongationCost: {{ $ActiveOpenClaimFileProlongationCost }},
            Variation: {{ $ActiveOpenClaimFileVariation }},
            Disruption: {{ $ActiveOpenClaimFileDisruption }},
            needChronology: {{ $needChronology }},
            needSynopsis: {{ $needSynopsis }},
            needContractualA: {{ $needContractualA }},
            needCauseEffectA: {{ $needCauseEffectA }},

        };
        const max4 = Math.max(...Object.values(valuesBox4));

        // Set bar widths as percentage of max
        document.getElementById("bar-ActiveOpenClaimFile2").style.width = `${(valuesBox4.main / max4) * 100}%`;
        document.getElementById("bar-ActiveOpenClaimFileTime").style.width = `${(valuesBox4.time / max4) * 100}%`;
        document.getElementById("bar-ActiveOpenClaimFileProlongationCost").style.width =
            `${(valuesBox4.ProlongationCost / max4) * 100}%`;
        document.getElementById("bar-ActiveOpenClaimFileVariation").style.width = `${(valuesBox4.Variation / max4) * 100}%`;
        document.getElementById("bar-ActiveOpenClaimFileDisruption").style.width =
            `${(valuesBox4.Disruption / max4) * 100}%`;
        document.getElementById("bar-needChronology").style.width = `${(valuesBox4.needChronology / max4) * 100}%`;
        document.getElementById("bar-needSynopsis").style.width = `${(valuesBox4.needSynopsis / max4) * 100}%`;
        document.getElementById("bar-needContractualA").style.width = `${(valuesBox4.needContractualA / max4) * 100}%`;
        document.getElementById("bar-needCauseEffectA").style.width = `${(valuesBox4.needCauseEffectA / max4) * 100}%`;

        const valuesBox2 = {
            main: {{ $allAssignmentUserDocuments }},
            forClaim: {{ $allForClaimUserDocuments }},
            narrative: {{ $allNeedNarrativeUserDocuments }},
            haveTags: {{ $allHaveConTagsUserDocuments }},
            haveTagsNoticed: {{ $allHaveConTagsNoticeClaimUserDocuments }},
        };
        const max2 = Math.max(...Object.values(valuesBox2));

        // Set bar widths as percentage of max
        document.getElementById("bar-assignment-docs").style.width = `${(valuesBox2.main / max2) * 100}%`;
        document.getElementById("bar-forClaim-docs").style.width = `${(valuesBox2.forClaim / max2) * 100}%`;
        document.getElementById("bar-narrative").style.width = `${(valuesBox2.narrative / max2) * 100}%`;
        document.getElementById("bar-have-tags").style.width = `${(valuesBox2.haveTags / max2) * 100}%`;
        document.getElementById("bar-have-tags-noticed").style.width = `${(valuesBox2.haveTagsNoticed / max2) * 100}%`;
    </script>
    <script>
        document.getElementById("total-docs").addEventListener("click", function() {
            window.location.href = "/project/all-documents?authUser=on";
        });
        document.getElementById("active-docs").addEventListener("click", function() {
            window.location.href = "/project/all-documents?authUser=on&active_docs=1";
        });
        document.getElementById("not-pursue").addEventListener("click", function() {
            window.location.href = "/project/all-documents?authUser=on&active_docs=0";
        });
        document.getElementById("count-pending-analysis").addEventListener("click", function() {
            window.location.href = "/project/all-documents?authUser=on&analysis_complete=0&active_docs=1";
        });
        document.getElementById("count-pending-assignment").addEventListener("click", function() {
            window.location.href = "/project/all-documents?authUser=on&not_assignment=on&active_docs=1";
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx3 = document.getElementById('pieChart').getContext('2d');

        const ctx4 = document.getElementById('pieChart2').getContext('2d');

        const active = {{ $allActiveUserDocuments }};
        const notPursue = {{ $allInactiveUserDocuments }};

        const open = {{ $ActiveOpenClaimFile }};
        const close = {{ $ActiveClosedClaimFile }};
        const data = {

            datasets: [{
                data: [active, notPursue],
                backgroundColor: ['#39ab19', 'red'],
                borderWidth: 0,
            }],
        };
        const data2 = {

            datasets: [{
                data: [open, close],
                backgroundColor: ['#39ab19', 'red'],
                borderWidth: 0,
            }],
        };

        const pieChart = new Chart(ctx3, {
            type: 'pie',
            data: data,
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },
            }
        });
        const pieChart2 = new Chart(ctx4, {
            type: 'pie',
            data: data2,
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },
            }
        });
    </script>

    <!-- Chart.js CDN -->
@endpush
