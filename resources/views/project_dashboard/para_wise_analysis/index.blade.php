@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Para Wise Analysis')
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
            width: 50% !important;
        }

        .table-container th:nth-child(3),
        .table-container td:nth-child(3) {
            width: 30% !important;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 10% !important;
        }

        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 9% !important;
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
            <h2 class="h3 mb-0 page-title">Para-wise Analysis</h2>
        </div>
        <div class="col-auto">

            <a type="button" href="{{ route('project.para-wise-analysis.create') }}"
                class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create</a>

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

                                    <th><b>Title</b></th>
                                    <th><b>Owner</b></th>
                                    <th><b>% Complete</b></th>
                                    <th></th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($all_para_wises as $para_wise)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input row-checkbox"data-paraWise-id="{{ $para_wise->id }}"
                                                    id="checkbox-{{ $para_wise->id }}" value="{{ $para_wise->id }}">
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $para_wise->id }}"></label>
                                            </div>
                                        </td>

                                        <td>{{ $para_wise->title }}</td>

                                        <td>{{ $para_wise->user->name }}</td>

                                        <td>{{ $para_wise->percentage_complete }}</td>
                                        <td>
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">

                                                <a class="dropdown-item"
                                                    href="{{ route('project.para-wise-analysis.edit', $para_wise->slug) }}">Edit</a>
                                                <a class="dropdown-item text-danger"
                                                    href="javascript:void(0);"onclick="confirmDelete('{{ route('project.para-wise-analysis.delete', $para_wise->slug) }}')">Delete</a>
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
                "targets": 4, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }]
        });
    </script>
    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this Para-wise Analysis? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
@endpush
