@extends('dashboard.layout.app')
@section('title', 'Admin Home - Contract Tags')
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
            <h2 class="h3 mb-0 page-title">Contract Tags</h2>
        </div>
        <div class="col-auto">
            <a type="button" href="{{ route('accounts.contract-tags.create') }}"
                class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create Tag</a>
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
                    <!-- table -->
                    <table class="table datatables" id="dataTable-1">
                        <thead>
                            <tr>

                                <th>#</th>
                                <th>Name</th>
                                <th>Subclause</th>
                                <th>Variation Proccess Step</th>
                                <th>Order</th>
                                <th>Is Notice</th>
                                <th>For Letter</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $counter=1;    
                            ?>
                            @foreach ($contract_tags as $tag)
                                <tr>

                                    <td>{{$counter}}</td>
                                    <td>{{ $tag->name }}</td>
                                    <td>{{ $tag->sub_clause }}</td>
                                    <td>{{ $tag->var_process }}</td>
                                    <td>{{ $tag->order }}</td>
                                    <td>{{ $tag->is_notice == '1' ? 'Yes' : 'No' }}</td>
                                    <td>{{ $tag->for_letter == '1' ? 'Yes' : 'No' }}</td>

                                    <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted sr-only">Action</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item"
                                                href="{{ route('accounts.contract-tags.edit', $tag->id) }}">Edit</a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);"
                                                onclick="confirmDelete('{{ route('accounts.contract-tags.delete', $tag->id) }}')">Remove</a>
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
            if (confirm('Are you sure you want to delete this contract tag? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
@endpush
