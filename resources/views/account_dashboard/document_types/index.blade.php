@extends('account_dashboard.layout.app')
@section('title', 'Admin Account Home - Document Types')
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
            <h2 class="h3 mb-0 page-title">Document Types</h2>
        </div>
        @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                in_array('create_document_type', $Account_Permissions ?? []))
            <div class="col-auto">
                <a type="button" href="{{ route('account.document-types.create') }}"
                    class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create Document Type</a>
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
                                <th>Description</th>
                                <th>Relevant Word</th>
                                <th>Order</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $counter = 1;
                            ?>
                            @foreach ($document_types as $type)
                                <tr>

                                    <td>{{ $counter }}</td>
                                    <td>{{ $type->name }}</td>
                                    <td>
                                        @if (strlen($type->description) > 50)
                                            {{ substr($type->description, 0, 50) }}...
                                        @else
                                            {{ $type->description }}
                                        @endif
                                    </td>
                                    <td>{{ $type->relevant_word?? '__' }}</td>
                                    <td>{{ $type->order }}</td>
                                    <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted sr-only">Action</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('edit_document_type', $Account_Permissions ?? []))
                                                <a class="dropdown-item"
                                                    href="{{ route('account.document-types.edit', $type->id) }}">Edit</a>
                                            @endif
                                            @if (auth()->user()->roles->first()->name == 'Super Admin' ||
                                                    in_array('delete_document_type', $Account_Permissions ?? []))
                                                <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                    onclick="confirmDelete('{{ route('accounts.document-types.delete', $type->id) }}')">Remove</a>
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
            if (confirm('Are you sure you want to delete this document type? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
@endpush
