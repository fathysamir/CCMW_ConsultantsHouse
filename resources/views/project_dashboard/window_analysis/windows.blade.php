@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Windows')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .is-invalid {
            border-color: red !important;
            box-shadow: 0 0 4px rgba(255, 0, 0, 0.4);
        }

        .driving-activity-row {
            display: flex;
            width: 100%;
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            align-items: flex-end;
            transition: box-shadow 0.2s ease;
        }

        .driving-activity-row:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .date {
            background-color: #fff !important;
        }

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
            width: 12% !important;
        }

        .table-container th:nth-child(3),
        .table-container td:nth-child(3) {
            width: 12% !important;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 12% !important;
        }

        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 12% !important;
        }

        .table-container th:nth-child(6),
        .table-container td:nth-child(6) {
            width: 12% !important;
        }

        .table-container th:nth-child(7),
        .table-container td:nth-child(7) {
            width: 12% !important;
        }

        .table-container th:nth-child(8),
        .table-container td:nth-child(8) {
            width: 12% !important;
        }

        .table-container th:nth-child(9),
        .table-container td:nth-child(9) {
            width: 12% !important;
        }

        .table-container th:nth-child(10),
        .table-container td:nth-child(10) {
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
            <h2 class="h3 mb-0 page-title">Windows</h2>
        </div>
        <div class="col-auto">
            <a href="javascript:void(0)" class="btn mb-2 btn-outline-primary" id="openCreateWindow">
                Create Window
            </a>
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
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-container">
                        <table class="table datatables" id="dataTable-1">
                            <thead>
                                <tr>
                                    <th id="check" class="">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="select-all">
                                            <label class="custom-control-label" for="select-all"></label>
                                        </div>
                                    </th>
                                    <th><b>Window No</b></th>
                                    <th><b>Start Date</b></th>
                                    <th><b>End Date</b></th>
                                    <th><b>Duration</b></th>
                                    <th><b>Culpable</b></th>
                                    <th><b>Excusable</b></th>
                                    <th><b>Compensable</b></th>
                                    <th><b>Compensable Transfer</b></th>

                                    <th><b></b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $prev_window = '';
                                @endphp
                                @foreach ($all_windows as $window)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input row-checkbox"
                                                    data-activity-slug="{{ $window->slug }}"
                                                    id="checkbox-{{ $window->slug }}" value="{{ $window->slug }}">
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $window->slug }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $window->no }}</td>
                                        <td>{{ date('d-M-Y', strtotime($window->start_date)) }}</td>
                                        <td>{{ date('d-M-Y', strtotime($window->end_date)) }}</td>
                                        <td>{{ $window->duration }}</td>
                                        <td>{{ $window->culpable }}</td>
                                        <td>{{ $window->excusable }}</td>
                                        <td>{{ $window->compensable }}</td>
                                        <td>{{ $window->transfer_compensable }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="text-muted sr-only">Action</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item edit_window" href="javascript:void(0)"
                                                        data-window-slug="{{ $window->slug }}"
                                                        data-window-no="{{ $window->no }}"
                                                        data-start="{{ $window->start_date }}"
                                                        data-end="{{ $window->end_date }}">
                                                        Edit
                                                    </a>

                                                    <a class="dropdown-item form_button"
                                                        href="javascript:void(0);"data-program="BAS"
                                                        data-window-no="{{ $window->no }}"
                                                        data-prev_window-slug="{{ $prev_window }}"
                                                        data-start="{{ $window->start_date }}"
                                                        data-end="{{ $window->end_date }}"
                                                        data-window-slug="{{ $window->slug }}">
                                                        BAS
                                                    </a>
                                                    <a class="dropdown-item form_button"
                                                        href="javascript:void(0);"data-program="IMP"
                                                        data-window-no="{{ $window->no }}"
                                                        data-prev_window-slug="{{ $prev_window }}"
                                                        data-start="{{ $window->start_date }}"
                                                        data-end="{{ $window->end_date }}"
                                                        data-window-slug="{{ $window->slug }}">
                                                        IMP
                                                    </a>
                                                    <a class="dropdown-item form_button"
                                                        href="javascript:void(0);"data-program="UPD"
                                                        data-window-no="{{ $window->no }}"
                                                        data-prev_window-slug="{{ $prev_window }}"
                                                        data-start="{{ $window->start_date }}"
                                                        data-end="{{ $window->end_date }}"
                                                        data-window-slug="{{ $window->slug }}">
                                                        UPD
                                                    </a>
                                                    <a class="dropdown-item form_button"
                                                        href="javascript:void(0);"data-program="BUT"
                                                        data-window-no="{{ $window->no }}"
                                                        data-prev_window-slug="{{ $prev_window }}"
                                                        data-start="{{ $window->start_date }}"
                                                        data-end="{{ $window->end_date }}"
                                                        data-window-slug="{{ $window->slug }}">
                                                        BUT
                                                    </a>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                        onclick="confirmDelete('{{ route('project.window.delete', $window->slug) }}')">
                                                        Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $prev_window = $window->slug;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="windowModal" tabindex="-1" role="dialog" aria-labelledby="windowModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="windowForm" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="windowModalLabel">Create Activity</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="window_slug" name="slug">
                        <div class="form-group">
                            <label for="window_no">Window No <span style="color: red">*</span></label>
                            <input type="text" class="form-control" id="window_no" name="window_no" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="start_date">srart Date <span style="color: red">*</span></label>
                            <input required type="date"style="background-color:#fff;" name="start_date"
                                id="start_date" class="form-control date" placeholder="Start Date"value="">
                        </div>
                        <div class="form-group mb-3">
                            <label for="end_date">End Date <span style="color: red">*</span></label>
                            <input required type="date"style="background-color:#fff;" name="end_date" id="end_date"
                                class="form-control date" placeholder="End Date"value="">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="save_window">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="formsModal" tabindex="-1" role="dialog" aria-labelledby="formsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 800px;">
            <div class="modal-content">
                <form id="formsForm" enctype="multipart/form-data" method="POST" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="formsModalLabel">Create Activity</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div style="display:flex;width:100%;margin-top:10px;">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label>Window No : <b>
                                        <spam id="w_no"></spam>
                                    </b></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3" style="text-align: center;">
                                <label>Start Date : <b>
                                        <spam id="s_date"></spam>
                                    </b></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3"style="text-align: right;">
                                <label>End Date : <b>
                                        <spam id="e_date"></spam>
                                    </b></label>
                            </div>
                        </div>
                    </div>
                    <div id="prev_window_label_div">
                        <div class="col-md-4">
                            <label id="prev_window"
                                style="text-decoration: underline;color:#007bff;cursor: pointer;">Import from prev.
                                <spam id="prev_window_label"></spam>
                            </label>
                        </div>
                    </div>
                    <input type="hidden" id="window_slug2" name="slug">
                    <input type="hidden" id="program" name="program">
                    <input type="hidden" id="prev_window_slug">

                    <div class="modal-body" id="driving-activities-container">



                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="save_form">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


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

            // Select all the checkboxes with the class "row-checkbox"
            const checkboxes = document.querySelectorAll('.row-checkbox');

            // Initially hide the Action-DIV


            // Add an event listener to each checkbox
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    // Get the number of checkboxes that are checked
                    const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

                    // If more than one checkbox is checked, display the Action-DIV, else hide it
                    if (checkedCheckboxes.length > 1) {

                    } else {

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

                } else {

                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const form2 = $('#formsForm');
            const modal2 = $('#formsModal');
            const formatDate = (dateStr) => {
                const date = new Date(dateStr);
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }; // e.g. 13-Oct-2025
                return date.toLocaleDateString('en-GB', options).replace(/ /g, '-');
            };
            $('#prev_window').click(function() {
                let program_2 = $('#program').val()
                let prev='';
                if (program_2 == 'BAS') {
                    prev = 'UPD'
                } else if (program_2 == 'UPD') {
                    prev = 'IMP'
                } else if (program_2 == 'IMP') {
                    prev = 'IMP'
                }
                let slug_2 = $('#prev_window_slug').val()
                $.ajax({
                    url: '/get-window-driving-activity',
                    type: 'GET',
                    data: {
                        slug: slug_2,
                        program: program_2,
                        prev: prev
                    },

                    success: function(response) {
                        if (response.success) {
                            $('#drivingActivitiesContainer').empty();
                            $('#drivingActivitiesContainer').append(response.html)
                            flatpickr(".date", {
                                enableTime: false,
                                dateFormat: "Y-m-d", // Format: YYYY-MM-DD
                                altInput: true,
                                altFormat: "d.M.Y",
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', error);
                        alert('Failed to upload file');
                    }
                });
            });
            $(document).on('click', '.form_button', function() {
                const slug = $(this).data('window-slug');
                const program = $(this).data('program');
                const prev_window = $(this).data('prev_window-slug');
                const w_no = $(this).data('window-no');
                const s_date = $(this).data('start');
                const e_date = $(this).data('end');
                $('#prev_window_label_div').hide();
                if (program == 'BAS') {
                    $('#prev_window_label_div').show();
                    $('#prev_window_label').text('UPD')
                } else if (program == 'UPD') {
                    $('#prev_window_label_div').show();
                    $('#prev_window_label').text('IMP')
                } else if (program == 'IMP') {
                    $('#prev_window_label_div').show();
                    $('#prev_window_label').text('IMP')
                }

                $('#window_slug2').val(slug);
                $('#prev_window_slug').val(prev_window);
                $('#program').val(program);
                $('#formsModalLabel').text(program);
                $('#w_no').text(w_no);
                $('#s_date').text(formatDate(s_date));
                $('#e_date').text(formatDate(e_date));
                form2.attr('action', "{{ route('project.window.form.store') }}");

                $.ajax({
                    url: '/get-window-driving-activity',
                    type: 'GET',
                    data: {
                        slug: slug,
                        program: program
                    },

                    success: function(response) {
                        if (response.success) {
                            $('#driving-activities-container').empty();
                            $('#driving-activities-container').append(response.html)
                            flatpickr(".date", {
                                enableTime: false,
                                dateFormat: "Y-m-d", // Format: YYYY-MM-DD
                                altInput: true,
                                altFormat: "d.M.Y",
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', error);
                        alert('Failed to upload file');
                    }
                });
                modal2.modal('show');
            });
            $(document).on('click', '.add-row', function() {
                let currentRow = $(this).closest('.driving-activity-row');
                let container = $('#drivingActivitiesContainer');
                let currentIndex = container.find('.driving-activity-row').length;

                // Clone only the *base structure* (first row template)
                let baseRow = $('.driving-activity-row').first().clone();

                // Clear all values and set unique names
                baseRow.find('select, input').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        name = name.replace(/\[\d+\]/, '[' + currentIndex + ']');
                        $(this).attr('name', name);
                    }
                    $(this).val('');
                });

                // Remove any duplicate date fields if they exist (keep only one)
                baseRow.find('.date').not(':first').remove();

                // Insert clean new row after the current row
                currentRow.after(baseRow);

                // Reinitialize Flatpickr for date field
                baseRow.find('.date').each(function() {
                    if (this._flatpickr) {
                        this._flatpickr.destroy();
                    }
                    flatpickr(this, {
                        enableTime: false,
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d-M-Y"
                    });
                });

                refreshSelectOptions()
            });

            // Handle remove row
            $(document).on('click', '.remove-row', function() {
                const rows = $('.driving-activity-row');
                if (rows.length > 1) {
                    $(this).closest('.driving-activity-row').remove();
                    refreshSelectOptions()
                } else {
                    alert('At least one row must remain.');
                }
            });

            function getSelectedPairs() {
                let pairs = [];
                $('.driving-activity-row').each(function() {
                    const milestone = $(this).find('.milestone-select').val();
                    const activity = $(this).find('.activity-select').val();

                    if (milestone && activity) {
                        pairs.push({
                            milestone,
                            activity
                        });
                    }
                });
                return pairs;
            }

            // Function to refresh dropdowns to avoid duplicates
            function refreshSelectOptions() {
                const usedPairs = getSelectedPairs();

                $('.driving-activity-row').each(function() {
                    const milestoneSelect = $(this).find('.milestone-select');
                    const activitySelect = $(this).find('.activity-select');

                    const currentMilestone = milestoneSelect.val();
                    const currentActivity = activitySelect.val();

                    // Reset visibility (show all)
                    milestoneSelect.find('option').show();
                    activitySelect.find('option').show();

                    // Hide used pairs in other rows
                    usedPairs.forEach(pair => {
                        // If this row is not the one currently using that pair
                        if (!(pair.milestone === currentMilestone && pair.activity ===
                                currentActivity)) {
                            // Hide same milestone if activity matches
                            milestoneSelect.find(`option[value="${pair.milestone}"]`).each(
                                function() {
                                    if (activitySelect.val() === pair.activity) {
                                        $(this).hide();
                                    }
                                });

                            // Hide same activity if milestone matches
                            activitySelect.find(`option[value="${pair.activity}"]`).each(
                                function() {
                                    if (milestoneSelect.val() === pair.milestone) {
                                        $(this).hide();
                                    }
                                });
                        }
                    });
                });
            }

            // Watch for change events on milestone or activity selects
            $(document).on('change', '.milestone-select, .activity-select', function() {
                refreshSelectOptions();
            });
            $(document).on('submit', '#formsForm', function(e) {
                e.preventDefault(); // stop default submit for validation check

                let isValid = true;

                // Loop through all required inputs and selects
                $(this).find('input[required], select[required]').each(function() {
                    if (!$(this).val() || $(this).val() === '') {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    alert('Please fill all required fields before submitting.');
                    return false;
                }

                // ✅ if valid → proceed to submit form
                this.submit();
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

            const form = $('#windowForm');
            const modal = $('#windowModal');

            // 🔹 Initialize flatpickr with linked start/end
            const startPicker = flatpickr("#start_date", {
                enableTime: false,
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d.M.Y",
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        endPicker.set('minDate', dateStr);
                        // If end date is before new start date, clear it
                        const endDate = endPicker.selectedDates[0];
                        if (endDate && endDate < selectedDates[0]) {
                            endPicker.clear();
                        }
                    } else {
                        endPicker.set('minDate', null);
                    }
                }
            });

            const endPicker = flatpickr("#end_date", {
                enableTime: false,
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d.M.Y",
            });

            // 🔹 Open Create Modal
            $(document).on('click', '#openCreateWindow', function() {
                $('#windowModalLabel').text('Create Window');
                form.trigger('reset');
                form.removeClass('was-validated');
                $('#window_slug').val('');

                // Reset flatpickrs
                startPicker.clear();
                endPicker.clear();
                endPicker.set('minDate', null);

                form.attr('action', "{{ route('project.window.store') }}");
                modal.modal('show');
            });

            // 🔹 Open Edit Modal
            $(document).on('click', '.edit_window', function() {
                const slug = $(this).data('window-slug');
                const no = $(this).data('window-no');
                const start = $(this).data('start');
                const end = $(this).data('end');

                $('#windowModalLabel').text('Edit Window');
                $('#window_no').val(no);
                $('#window_slug').val(slug);

                // Set Flatpickr dates safely
                startPicker.setDate(start, true);
                endPicker.set('minDate', start);
                endPicker.setDate(end, true);

                // Set form action
                let updateUrl = "{{ route('project.window.update', ':slug') }}";
                updateUrl = updateUrl.replace(':slug', slug);
                form.attr('action', updateUrl);

                modal.modal('show');
            });

            // 🔹 Validate form before submit
            form.on('submit', function(e) {
                let valid = true;

                form.find('input[required]').each(function() {
                    if (!$(this).val().trim()) {
                        $(this).addClass('is-invalid');
                        valid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    form.addClass('was-validated');
                }
            });

            // 🔹 Remove invalid class when user starts typing
            form.find('input').on('input change', function() {
                $(this).removeClass('is-invalid');
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
                "targets": 9, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }]
        });
    </script>
    <script>
        function updateFileName(input) {
            const fileName = input.files[0]?.name || 'Choose File';
            const labelId = input.id + 'Label';
            document.getElementById(labelId).textContent = fileName;
        }

        function previewImage(event, id) {
            var input = event.target;
            var reader = new FileReader();

            reader.onload = function() {
                var img = document.getElementById(id);
                img.src = reader.result;
                img.style.display = 'block'; // Show the image
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]); // Read the uploaded image
            }
        }
    </script>
    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this Window? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
@endpush
