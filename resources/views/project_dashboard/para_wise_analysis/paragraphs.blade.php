@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Para Wise Analysis - Paragraphs')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">
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
            width: 5% !important;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 25% !important;
        }

        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 25% !important;
        }

        .table-container th:nth-child(6),
        .table-container td:nth-child(6) {
            width: 26% !important;
        }

        .table-container th:nth-child(7),
        .table-container td:nth-child(7) {
            width: 10% !important;
        }

        .table-container th:nth-child(8),
        .table-container td:nth-child(8) {
            width: 2% !important;
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
            <h3 class="h3 mb-0 page-title"><a href="{{ route('project.para-wise-analysis') }}">Para-wise Analysis</a><span
                    id="chevronIcon"
                    class="fe fe-24 fe-chevrons-right"style="position: relative; top: 2px;"></span>{{ $para_wise->title }}
            </h3>
        </div>
        <div class="col-auto">

            <a type="button" href="{{ route('project.para-wise-analysis.create_paragraph', $para_wise->slug) }}"
                class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create</a>
            <a type="button" href="javascript:void(0);"
                class="btn mb-2 btn-outline-primary"data-para-wise-id="{{ $para_wise->slug }}"
                id="export-allParaWises">Export
                to MS Word</a>

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
                        <table class="table datatables" id="dataTable-1" style="font-size: 12px;">

                            <thead>
                                <tr>
                                    <th id="check"class="">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="select-all">
                                            <label class="custom-control-label" for="select-all"></label>
                                        </div>
                                    </th>

                                    <th>
                                        <label id="all_has_reply"
                                            style="margin-right:0.02rem;cursor: pointer;margin-bottom:0px;margin-top: 6px;"
                                            title="Paragraphs Have Reply">
                                            <span class="fe fe-24 fe-file-text" style="font-size: 17px;"></span>
                                        </label>
                                        <label id="all_blue_flag"
                                            style="margin-right:0.02rem;cursor: pointer;margin-bottom:0px;margin-top: 6px;"
                                            title="Blue Flags">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
                                        <label id="all_red_flag"
                                            style="margin-right:0.02rem;cursor: pointer;margin-bottom:0px;margin-top: 6px;"
                                            title="Red Flags">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
                                        <label id="all_green_flag"
                                            style="cursor: pointer;margin-bottom:0px;margin-top: 6px;" title="Green Flags">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
                                    </th>
                                    <th><b>Para.No.</b></th>
                                    <th><b>Paragraph</b></th>
                                    <th><b>Reply</b></th>
                                    <th><b>Note</b></th>
                                    <th><b>Reply to Paras Nos.</b></th>
                                    <th></th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($paragraphs as $paragraph)
                                    <tr
                                        @if ($specific_paragraph == $paragraph->slug) style="background-color: #AFEEEE" class="specific_specific_paragraph" @endif>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input row-checkbox"data-paragraph-id="{{ $paragraph->id }}"
                                                    id="checkbox-{{ $paragraph->id }}" value="{{ $paragraph->id }}">
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $paragraph->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <label class="replyed @if ($paragraph->replyed) active @endif"
                                                style="margin-right:0.02rem;cursor: pointer;margin-bottom:0px;margin-top: 6px;"
                                                title="Paragraphs Have Reply"
                                                data-paragraph-id="{{ $paragraph->id }}"data-flag="replyed">
                                                @if ($paragraph->replyed)
                                                    <span class="fe fe-24 fe-file-text"
                                                        style="font-size: 17px; color:#d001fe"></span>
                                                @else
                                                    <span class="fe fe-24 fe-file-text" style="font-size: 17px;"></span>
                                                @endif
                                            </label>
                                            <label
                                                class="blue_flag change-flag @if ($paragraph->blue_flag) active @endif"
                                                style="margin-right:0.02rem;cursor: pointer;margin-bottom:0px;margin-top: 6px;"data-paragraph-id="{{ $paragraph->id }}"
                                                data-flag="blue_flag" title="Blue Flag">
                                                @if ($paragraph->blue_flag)
                                                    <i class="fa-solid fa-flag" style="color: #0000ff;"></i>
                                                @else
                                                    <i class="fa-regular fa-flag"></i>
                                                @endif

                                            </label>
                                            <label
                                                class="red_flag change-flag @if ($paragraph->red_flag) active @endif"
                                                data-paragraph-id="{{ $paragraph->id }}" data-flag="red_flag"
                                                style="margin-right:0.02rem;cursor: pointer;margin-bottom:0px;margin-top: 6px;"
                                                title="Red Flag">
                                                @if ($paragraph->red_flag)
                                                    <i class="fa-solid fa-flag"style="color: #ff0000;"></i>
                                                @else
                                                    <i class="fa-regular fa-flag"></i>
                                                @endif
                                            </label>
                                            <label
                                                class="green_flag change-flag @if ($paragraph->green_flag) active @endif"
                                                data-paragraph-id="{{ $paragraph->id }}" data-flag="green_flag"
                                                style="cursor: pointer;margin-bottom:0px;margin-top: 6px;"
                                                title="Green Flag">
                                                @if ($paragraph->green_flag)
                                                    <i class="fa-solid fa-flag"style="color: #00ff00;"></i>
                                                @else
                                                    <i class="fa-regular fa-flag"></i>
                                                @endif
                                            </label>
                                        </td>
                                        <td><strong style="color:blue;"><a class="l-link"style="color:rgb(80, 78, 78);"
                                                    style="color:"
                                                    href="{{ route('project.para-wise-analysis.edit_paragraph', $paragraph->slug) }}">{{ floatval($paragraph->number) }}</a></strong>
                                        </td>

                                        <td>{{ extractTextSnippet($paragraph->paragraph) }}</td>

                                        <td>{{ $paragraph->reply ? extractTextSnippet($paragraph->reply) : '__' }}</td>
                                        <td>{{ $paragraph->notes
                                            ? (strlen($paragraph->notes) > 50
                                                ? substr($paragraph->notes, 0, 50) . '...'
                                                : $paragraph->notes)
                                            : '__' }}
                                        </td>
                                        <td>
                                            @if ($paragraph->para_numbers)
                                                @php
                                                    // تأكد إن القيم متخزنة مفصولة بفاصلة أو مسافة
                                                    $numbers = preg_split(
                                                        '/[\s,]+/',
                                                        $paragraph->para_numbers,
                                                        -1,
                                                        PREG_SPLIT_NO_EMPTY,
                                                    );
                                                @endphp

                                                @foreach ($numbers as $index => $num)
                                                    <a href="#">
                                                        {{ floatval(\App\Models\Paragraph::where('id',$num)->first()->number) }}
                                                    </a>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @else
                                                __
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">

                                                <a class="dropdown-item edit-paraWise"
                                                    href="{{ route('project.para-wise-analysis.edit_paragraph', $paragraph->slug) }}">
                                                    Edit
                                                </a>
                                                <a class="dropdown-item text-danger"
                                                    href="javascript:void(0);"onclick="confirmDelete('{{ route('project.para-wise-analysis.delete_paragraph', $paragraph->slug) }}')">Delete</a>
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
    <div class="modal fade" id="exportParaWiseModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Settings To Export Para Wises
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportForm">
                        @csrf
                        <input type="hidden" id="paraWise_ID" name="paraWise_ID">
                        <div class="form-group">
                            <label for="newDocTypeForAll">Heading 1 Number</label>
                            <input type="Number" required name="Chapter" class="form-control" placeholder="Heading 1"
                                id="Chapter" value="1" min="1"
                                oninput="this.value = Math.max(1, this.value)">
                        </div>
                        <div class="form-group">
                            <label for="header1">Heading 1 Title</label>
                            <input type="text" required name="header1" class="form-control" placeholder="Header 1"
                                id="header1">
                        </div>
                        <div class="form-group">
                            <label for="newDocTypeForAll">Heading 2 Number</label>
                            <input type="Number" required name="Section" class="form-control" placeholder="Heading 2"
                                id="Section" value="0" min="0"
                                oninput="this.value = Math.max(0, this.value)">
                        </div>
                        <div class="form-group">
                            <label for="header2">Heading 2 Title</label>
                            <input type="text" required name="header2" class="form-control" placeholder="Header 2"
                                id="header2">
                        </div>
                        <div class=" form-group mb-0" style="display: inline-flex;width: 100%;">
                            <label>Select Style : </label>
                            <div style="width: 70%;margin-left:2%;font-size: 0.8rem;">

                                <div class="custom-control custom-radio">
                                    <input type="radio"required id="b_p_r_s" name="style" value="b_p_r_s"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="b_p_r_s">Background - Paragraph - Reply style</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio"required id="p_r_s" name="style" class="custom-control-input"
                                        value="p_r_s">
                                    <label class="custom-control-label" for="p_r_s">Paragraph - Reply style</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio"required name="style" checked value="r_s" id="r_s"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="r_s">Reply style</label>
                                </div>
                            </div>
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

@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).on('click', '.change-flag', function() {
            var label = $(this);
            var paragraphId = label.data('paragraph-id');
            var flag = label.data('flag');
            var icon = label.find("i");

            $.ajax({
                url: "/paragraphs/change-flag", // غيّر المسار حسب route عندك
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    paragraph_id: paragraphId,
                    flag: flag
                },
                success: function(response) {
                    if (response.status === "success") {
                        // Toggle active class
                        if (label.hasClass("active")) {
                            label.removeClass("active");
                            icon.removeClass("fa-solid").addClass("fa-regular").css("color", "");
                        } else {
                            label.addClass("active");
                            icon.removeClass("fa-regular").addClass("fa-solid");

                            if (flag === "blue_flag") icon.css("color", "#0000ff");
                            if (flag === "red_flag") icon.css("color", "#ff0000");
                            if (flag === "green_flag") icon.css("color", "#00ff00");
                        }
                    } else {
                        alert("Something went wrong!");
                    }
                },
                error: function() {
                    alert("Server error, please try again!");
                }
            });
        });
    </script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const targetRow = document.querySelector('.specific_paragraph');
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
            $("#check").removeClass("sorting_asc");
            var table = $('#dataTable-1').DataTable();
            var activeFlags = [];

            // الألوان حسب نوع الفلاغ
            var flagColors = {
                "all_blue_flag": "#0000ff",
                "all_red_flag": "#ff0000",
                "all_green_flag": "#00ff00",
                "all_has_reply": "#d001fe"
            };

            // ماب تربط بين زرار الهيدر واسم الكلاس في الصف
            var flagMap = {
                "all_blue_flag": "blue_flag",
                "all_red_flag": "red_flag",
                "all_green_flag": "green_flag",
                "all_has_reply": "replyed" // هنا التعديل
            };

            // عند الضغط على أي فلاغ في الهيدر
            $('#all_has_reply, #all_blue_flag, #all_red_flag, #all_green_flag').on('click', function() {
                var flagId = $(this).attr('id'); // مثال: all_blue_flag
                var flag = flagMap[flagId]; // ناخد الكلاس الصح للصف

                var icon = $(this).find("i, span"); // الأيقونة الداخلية

                // Toggle
                if ($(this).hasClass('active')) {
                    // إلغاء التفعيل
                    $(this).removeClass('active');
                    icon.removeClass("fa-solid").addClass("fa-regular").css("color", "");
                    activeFlags = activeFlags.filter(f => f !== flag);
                } else {
                    // تفعيل
                    $(this).addClass('active');

                    if (flag === "replyed") {
                        // replyed أيقونة span fe-file-text
                        icon.css("color", flagColors[flagId]);
                    } else {
                        icon.removeClass("fa-regular").addClass("fa-solid");
                        icon.css("color", flagColors[flagId]);
                    }

                    activeFlags.push(flag);
                }

                table.draw();
            });

            // فلتر مخصص للـ DataTable
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (activeFlags.length === 0) return true;

                var row = table.row(dataIndex).node();
                var matches = true;

                activeFlags.forEach(function(flag) {
                    if (!$(row).find('.' + flag).hasClass('active')) {
                        matches = false;
                    }
                });

                return matches;
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


            const parentDiv = document.getElementById('dataTable-1_wrapper');

            if (parentDiv) {
                const rowDiv = parentDiv.querySelector('.row');

                if (rowDiv) {
                    const colDivs = rowDiv.querySelectorAll('.col-md-6');

                    if (colDivs.length > 0) {
                        colDivs[0].classList.remove('col-md-6');
                        colDivs[0].classList.add('col-md-2');
                    }

                }
            }

            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.getElementsByClassName('row-checkbox');
                for (let checkbox of checkboxes) {
                    checkbox.checked = this.checked;
                }
                const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

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

            $('#export-allParaWises').on('click', function() {
                const paraWiseID = $(this).data('para-wise-id');
                $('#paraWise_ID').val(paraWiseID);
                $('#exportParaWiseModal').modal('show');
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
            $('#export').on('click', function() {
                const form = $('#exportForm');

                // Optional client-side check before AJAX send
                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }

                const formData = form.serialize();

                $.ajax({
                    url: '/export-word-para-wise', // Replace with real route
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // showHint(response.message || 'Download started!');
                        if (response.download_url) {
                            window.location.href = response.download_url; // يبدأ التحميل فعليًا
                        }
                        $('#exportParaWiseModal').modal('hide');
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
                "targets": 3, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 4, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 5, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 6, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 7, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, ]
        });
    </script>
    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this Paragraph? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
@endpush
