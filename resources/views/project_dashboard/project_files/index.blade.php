@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Files')
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
            width: 1%;
        }



        .table-container th:nth-child(2),
        .table-container td:nth-child(2) {
            width: 4%;
        }

        .table-container th:nth-child(3),
        .table-container td:nth-child(3) {
            width: 51%;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 10%;
        }

        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 10%;
        }

        .table-container th:nth-child(6),
        .table-container td:nth-child(6) {
            width: 10%;
        }

        .table-container th:nth-child(7),
        .table-container td:nth-child(7) {
            width: 10%;
        }

        .table-container th:nth-child(8),
        .table-container td:nth-child(8) {
            width: 4%;
        }

        /* Maintain styles from your original table */
        .table-container tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }
    </style>
    <style>
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

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">{{ $folder->name }}</h2>
        </div>
        <div class="col-auto">
            @if ($folder->name != 'Recycle Bin' && $folder->name != 'Archive')
                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('create_file', $Project_Permissions ?? []))
                    <a type="button" href="{{ route('project.files.create') }}"
                        class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create File</a>
                @endif
            @endif
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

                                    <th><b>NO.</b></th>
                                    <th><b>File Name</b></th>
                                    <th><b>{{ $folder->label1 }}</b></th>
                                    <th><b>{{ $folder->label2 }}</b></th>
                                    <th><b>{{ $folder->label3 }}</b></th>
                                    <th><b>File Owner</b></th>

                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($all_files as $file)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input row-checkbox"data-file-id="{{ $file->id }}"
                                                    id="checkbox-{{ $file->id }}" value="{{ $file->id }}">
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $file->id }}"></label>
                                            </div>
                                        </td>

                                        <td>{{ $file->code }}</td>
                                        <td><a class="l-link"style="color:rgb(80, 78, 78);" style="color:"
                                                href="{{ route('project.file-documents.index', $file->slug) }}">{{ $file->name }}</a>
                                        </td>
                                        <td>{{ $file->against ? $file->against->role : '_' }}</td>
                                        <td>{{ $file->start_date ? date('d-M-Y', strtotime($file->start_date)) : '_' }}
                                        </td>
                                        <td>{{ $file->end_date ? date('d-M-Y', strtotime($file->end_date)) : '_' }}</td>
                                        <td>{{ $file->user ? $file->user->name : '_' }}</td>


                                        <td>
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('edit_file', $Project_Permissions ?? []))
                                                    <a class="dropdown-item"
                                                        href="{{ route('project.files.edit', $file->slug) }}">Edit</a>

                                                    <a id="Change_Owner_btn_{{ $file->id }}"
                                                        class="dropdown-item change-owner-btn" href="javascript:void(0);"
                                                        data-file-id="{{ $file->id }}"data-file-owner-id="{{ $file->user_id }}">Change
                                                        Owner</a>
                                                @endif
                                                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('cope_move_file', $Project_Permissions ?? []))
                                                    <a class="dropdown-item" href="">Copy</a>
                                                    <a class="dropdown-item" href="">Move</a>
                                                @endif
                                                @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('delete_file', $Project_Permissions ?? []))
                                                    <a class="dropdown-item"
                                                        href="javascript:void(0);"onclick="confirmArchive('{{ route('project.files.archive', $file->id) }}')">Archive</a>
                                                    <a class="dropdown-item text-danger"
                                                        href="javascript:void(0);"onclick="confirmDelete('{{ route('project.files.delete', $file->id) }}')">Delete</a>
                                                @endif
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
    <div class="modal fade" id="changeOwnerModal" tabindex="-1" role="dialog" aria-labelledby="changeOwnerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeOwnerModalLabel">Change File Owner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeOwnerForm">
                        @csrf
                        <input type="hidden" id="fileId" name="file_id">
                        <div class="form-group">
                            <label for="newOwner">Select New Owner</label>
                            <select class="form-control" id="newOwner" name="new_owner_id" required>
                                <option value="" disabled selected>Select Owner</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveOwnerChange">Save changes</button>
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

                    // Create a new dropdown element
                    let new_down_list = document.createElement('div');
                    new_down_list.className = "col-sm-12 col-md-4";
                    new_down_list.innerHTML = `
                                <div class="dropdown" id="Action-DIV">
                                    <button class="btn btn-sm dropdown-toggle  btn-secondary" type="button"
                                        id="actionButton" aria-haspopup="true" aria-expanded="false">
                                        Open Actions
                                    </button>
                                    <div class="dropdown-menu " id="actionList" style="position: absolute;left:-50px; ">
                                        
                                    </div>
                                </div>
                            `;

                    // Append the new dropdown to the row
                    rowDiv.appendChild(new_down_list);

                    // Get the button and dropdown menu
                    const actionButton = new_down_list.querySelector('#actionButton');
                    const actionList = new_down_list.querySelector('#actionList');

                    // Toggle dropdown on button click
                    actionButton.addEventListener('click', function(event) {
                        event.stopPropagation(); // Prevent the click from bubbling up
                        if (actionList.style.display === 'block') {
                            actionList.style.display = 'none';
                        } else {
                            actionList.style.display = 'block';
                        }
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!event.target.closest('.dropdown')) {
                            actionList.style.display = 'none';
                        }
                    });
                }
            }

            // Select all the checkboxes with the class "row-checkbox"
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const actionDiv = document.getElementById('Action-DIV');

            // Initially hide the Action-DIV
            if (actionDiv) {
                actionDiv.style.display = 'none';
            }

            // Add an event listener to each checkbox
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    // Get the number of checkboxes that are checked
                    const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

                    // If more than one checkbox is checked, display the Action-DIV, else hide it
                    if (checkedCheckboxes.length > 1) {
                        actionDiv.style.display = 'block';
                    } else {
                        actionDiv.style.display = 'none';
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
                    actionDiv.style.display = 'block';
                } else {
                    actionDiv.style.display = 'none';
                }
            });



            $('.change-owner-btn').on('click', function() {
                var fileId = $(this).data('file-id');
                var fileOwner = $(this).data('file-owner-id');
                $('#fileId').val(fileId);
                $('#newOwner').val(fileOwner); // Set the document ID in the hidden input
                $('#changeOwnerModal').modal('show'); // Show the modal
            });

            // Handle the form submission via AJAX
            $('#saveOwnerChange').on('click', function() {
                var formData = $('#changeOwnerForm').serialize(); // Serialize form data
                var fileId = $('#fileId').val(); // Get the document ID

                $.ajax({
                    url: "{{ route('project.file.change-owner') }}", // Route for changing owner
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#changeOwnerModal').modal('hide');
                            alert('Owner changed successfully!');
                            // Hide the modal
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Failed to change owner.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
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
                [16, 32, 64, -1],
                [16, 32, 64, "All"]
            ],
            "columnDefs": [{
                "targets": 0, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }, {
                "targets": 7, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }]
        });
    </script>
    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this File? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }

        function confirmArchive(url) {
            if (confirm('Are you sure you want to archive this File? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
@endpush
