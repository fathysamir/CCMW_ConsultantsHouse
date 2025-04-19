@extends('account_dashboard.layout.app')
@section('title', 'Admin Account Home - Project Folders')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">

    <style>
        #btn-outline-primary {
            color: blue;
        }

        #btn-outline-primary:hover {
            color: white;
            /* Change text color to white on hover */
        }
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Project Folders</h2>
        </div>
        @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                in_array('create_project_folder', $Account_Permissions ?? []))
            <div class="col-auto">
                <a type="button" href="{{ route('account.project-folders.create') }}"
                    class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create Folder</a>
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
                    <table class="table datatables" id="dataTable-1">
                        <thead>
                            <tr>

                                <th>#</th>
                                <th>Name</th>
                                <th>Order</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            ?>
                            @foreach ($folders as $folder)
                                <tr>

                                    <td>{{ $counter }}</td>
                                    <td>{{ $folder->name }}</td>


                                    <td>{{ $folder->order }}</td>
                                    <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted sr-only">Action</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                                                    in_array('edit_project_folder', $Account_Permissions ?? []))
                                                <a class="dropdown-item"
                                                    href="{{ route('account.project-folders.edit', $folder->id) }}">Edit</a>
                                            @endif
                                            @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                                                    in_array('delete_project_folder', $Account_Permissions ?? []))
                                                <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                    onclick="confirmDelete('{{ route('accounts.project-folders.delete', $folder->id) }}')">Remove</a>
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
                [16, 32, 64, -1],
                [16, 32, 64, "All"]
            ]
        });
    </script>
    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this Folder? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
@endpush
