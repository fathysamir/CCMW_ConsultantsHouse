@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Project Abbreviations')
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
            width: 80% !important;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 7% !important;
        }

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
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Abbreviations</h2>
        </div>
        @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                in_array('create_abbreviation', $Project_Permissions ?? []))
            <div class="col-auto">
                <a type="button" href="{{ route('project.create_abbreviation') }}"
                    class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create Abbreviation</a>
            </div>
        @endif
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
                    <!-- table -->
                   <div class="table-container">

                        <!-- Table -->
                        <table class="table datatables" id="dataTable-1" style="font-size: 0.9rem;">
                        <thead>
                            <tr>

                                <th></th>
                                <th>Abbreviation</th>
                                <th>Description</th>

                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            ?>
                            @foreach ($abbreviations as $abbreviation)
                                <tr>

                                    <td>{{ $counter }}</td>
                                    <td>{{ $abbreviation->abb }}</td>
                                    <td>
                                        @if (strlen($abbreviation->description) > 120)
                                            {{ substr($abbreviation->description, 0, 120) }}...
                                        @else
                                            {{ $abbreviation->description }}
                                        @endif
                                    </td>

                                    <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted sr-only">Action</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('edit_abbreviation', $Project_Permissions ?? []))
                                                <a class="dropdown-item"
                                                    href="{{ route('project.edit_abbreviation', $abbreviation->id) }}">Edit</a>
                                            @endif
                                            @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                                                    in_array('delete_abbreviation', $Project_Permissions ?? []))
                                                <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                    onclick="confirmDelete('{{ route('project.delete_abbreviation', $abbreviation->id) }}')">Remove</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                $counter++;
                                ?>
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
    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
        });
    </script>
    <script src="{{ asset('dashboard/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $('#dataTable-1').DataTable({
            autoWidth: true,
            "lengthMenu": [
                [-1, 16, 32, 64],
                ["All", 16, 32, 64]
            ]
        });
    </script>
    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this Abbreviation? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
@endpush
