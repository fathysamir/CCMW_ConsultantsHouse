@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Windows')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
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
                                    <th><b>end Date</b></th>
                                    <th><b>Duration</b></th>
                                    <th><b>Culpable</b></th>
                                    <th><b>Excusable</b></th>
                                    <th><b>Compensable</b></th>
                                    <th><b>Compensable Transfer</b></th>

                                    <th><b></b></th>
                                </tr>
                            </thead>
                            <tbody>
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

                                                    <a class="dropdown-item bas_button" href="javascript:void(0);"data-program="BAS">
                                                        BAS
                                                    </a>
                                                    <a class="dropdown-item imp_button" href="javascript:void(0);"data-program="IMP">
                                                        IMP
                                                    </a>
                                                    <a class="dropdown-item upd_button" href="javascript:void(0);"data-program="UPD">
                                                        UPD
                                                    </a>
                                                    <a class="dropdown-item but_button" href="javascript:void(0);"data-program="BUT">
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
                            <input required type="date"style="background-color:#fff;" name="start_date" id="start_date"
                                class="form-control date" placeholder="Start Date"value="">
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
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this Window? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
@endpush
