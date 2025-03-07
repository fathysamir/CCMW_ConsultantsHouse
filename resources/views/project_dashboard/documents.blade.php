@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Documents')
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
            width: 10%;
        }

        .table-container th:nth-child(3),
        .table-container td:nth-child(3) {
            width: 13%;
        }

        .table-container th:nth-child(4),
        .table-container td:nth-child(4) {
            width: 37%;
        }

        .table-container th:nth-child(5),
        .table-container td:nth-child(5) {
            width: 7%;
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
            width: 5%;
        }

        .table-container th:nth-child(9),
        .table-container td:nth-child(9) {
            width: 2%;
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
            <h2 class="h3 mb-0 page-title">Documents</h2>
        </div>
        <div class="col-auto">

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

                                    <th><b>Type</b></th>
                                    <th><b>Reference</b></th>
                                    <th><b>Subject</b></th>
                                    <th><b>Date</b></th>
                                    <th><b>From</b></th>
                                    <th><b>To</b></th>

                                    <th><b>Rev.</b></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($all_documents as $document)
                                    <tr>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox"
                                                    class="custom-control-input row-checkbox"data-document-id="{{ $document->id }}"
                                                    id="checkbox-{{ $document->id }}" value="{{ $document->id }}">
                                                <label class="custom-control-label"
                                                    for="checkbox-{{ $document->id }}"></label>
                                            </div>
                                        </td>

                                        <td>{{ $document->docType->name }}</td>
                                        <td>{{ $document->reference }}</td>
                                        <td>{{ $document->subject }}</td>
                                        <td>{{ date('d-M-Y', strtotime($document->start_date)) }}</td>
                                        <td>{{ $document->fromStakeHolder ? $document->fromStakeHolder->role : '_' }}</td>
                                        <td>{{ $document->toStakeHolder ? $document->toStakeHolder->role : '_' }}</td>

                                        <td>{{ $document->revision }}</td>
                                        <td>
                                            <button class="btn btn-sm dropdown-toggle more-horizontal" type="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="text-muted sr-only">Action</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item"
                                                    href="{{ route('project.edit-document', $document->slug) }}">Edit</a>
                                                <a id="Change_Owner_btn_{{ $document->id }}"
                                                    class="dropdown-item change-owner-btn" href="javascript:void(0);"
                                                    data-document-id="{{ $document->id }}"data-document-owner-id="{{ $document->user_id }}">Change
                                                    Owner</a>
                                                <a class="dropdown-item" href="">Analysis Form</a>
                                                <a class="dropdown-item assigne-to-btn" href="javascript:void(0);"
                                                    data-document-id="{{ $document->id }}">Assigne To File</a>
                                                <a class="dropdown-item" href="">Check Assignment</a>
                                                <a class="dropdown-item text-danger"
                                                    href="javascript:void(0);"onclick="confirmDelete('{{ route('project.document.delete', $document->id) }}')">Delete</a>
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
                    <h5 class="modal-title" id="changeOwnerModalLabel">Change Document Owner</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeOwnerForm">
                        @csrf
                        <input type="hidden" id="documentId" name="document_id">
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
    <div class="modal fade" id="assigneToModal" tabindex="-1" role="dialog" aria-labelledby="assigneToModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assigneToModalLabel">Assign Document To File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="assigneToForm">
                        @csrf
                        <input type="hidden" id="documentId_" name="document_id">
                        <div class="form-group">
                            <label for="folder_id">Select Folder</label>
                            <select class="form-control" id="folder_id" required>
                                <option value="" disabled selected>Select Folder</option>
                                @foreach ($folders as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none">
                            <label for="newFile">Select File</label>
                            <select class="form-control" id="newFile" name="file_id">
                                <option value="" disabled selected>Select File</option>

                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveAssigne">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Changing Owner -->
    <div class="modal fade" id="changeOwnerForAllModal" tabindex="-1" role="dialog"
        aria-labelledby="changeOwnerForAllModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeOwnerForAllModalLabel">Change Owner for Selected Documents</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeOwnerForAllForm">
                        @csrf
                        <input type="hidden" id="documentIdsForAll" name="document_ids">
                        <div class="form-group">
                            <label for="newOwnerForAll">Select New Owner</label>
                            <select class="form-control" id="newOwnerForAll" name="new_owner_id" required>
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
                    <button type="button" class="btn btn-primary" id="saveOwnerChangeForAll">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignToForAllModal" tabindex="-1" role="dialog"
        aria-labelledby="assignToForAllModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignToForAllModalLabel">Assign Selected Document To File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="assignToForAllForm">
                        @csrf
                        <input type="hidden" id="documentIdsForAll5" name="document_ids">
                        <div class="form-group">
                            <label for="folder_id2">Select Folder</label>
                            <select class="form-control" id="folder_id2" required>
                                <option value="" disabled selected>Select Folder</option>
                                @foreach ($folders as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none">
                            <label for="newfileForAll">Select File</label>
                            <select class="form-control" id="newfileForAll" name="file_id">
                                <option value="" disabled selected>Select File</option>

                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveAssignToForAll">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeStakeHolderForAllModal" tabindex="-1" role="dialog"
        aria-labelledby="changeStakeHolderForAllModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStakeHolderForAllModalLabel">Change Stake Holders for Selected
                        Documents</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeStakeHolderForAllForm">
                        @csrf
                        <input type="hidden" id="documentIdsForAll2" name="document_ids">
                        <div class="form-group">
                            <label for="newFromStakeHolderForAll">From</label>
                            <select class="form-control" id="newFromStakeHolderForAll" name="new_from_id" required>
                                <option value="" disabled selected>Select Stake Holder</option>
                                @foreach ($stake_holders as $stake_holder)
                                    <option value="{{ $stake_holder->id }}">{{ $stake_holder->name }} -
                                        {{ $stake_holder->role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="newToStakeHolderForAll">To</label>
                            <select class="form-control" id="newToStakeHolderForAll" name="new_to_id" required>
                                <option value="" disabled selected>Select Stake Holder</option>
                                @foreach ($stake_holders as $stake_holder)
                                    <option value="{{ $stake_holder->id }}">{{ $stake_holder->name }} -
                                        {{ $stake_holder->role }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveStakeHolderChangeForAll">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeDocTypeForAllModal" tabindex="-1" role="dialog"
        aria-labelledby="changeDocTypeForAllModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeDocTypeForAllModalLabel">Change Document Type for Selected Documents
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeDocTypeForAllForm">
                        @csrf
                        <input type="hidden" id="documentIdsForAll3" name="document_ids">
                        <div class="form-group">
                            <label for="newOwnerForAll">Select New Document Type</label>
                            <select class="form-control" id="newDocTypeForAll" name="new_doc_type_id" required>
                                <option value="" disabled selected>Select Document Type</option>
                                @foreach ($documents_types as $documents_type)
                                    <option value="{{ $documents_type->id }}">{{ $documents_type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveDocTypeChangeForAll">Save changes</button>
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
            $('.change-owner-btn').on('click', function() {
                var documentId = $(this).data('document-id');
                var documentOwner = $(this).data('document-owner-id');
                $('#documentId').val(documentId);
                $('#newOwner').val(documentOwner); // Set the document ID in the hidden input
                $('#changeOwnerModal').modal('show'); // Show the modal
            });

            $('#saveOwnerChange').on('click', function() {
                var formData = $('#changeOwnerForm').serialize(); // Serialize form data
                var documentId = $('#documentId').val(); // Get the document ID

                $.ajax({
                    url: "{{ route('project.document.change-owner') }}", // Route for changing owner
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

            $('.assigne-to-btn').on('click', function() {
                var documentId_ = $(this).data('document-id');
                $('#documentId_').val(documentId_);
                $('#folder_id').val('');
                let fileDropdown = $('#newFile');
                fileDropdown.closest('.form-group').addClass(
                    'd-none');
                $('#assigneToModal').modal('show'); // Show the modal
            });
            // Handle the form submission via AJAX

            $('#folder_id').change(function() {
                let folderId = $(this).val();

                if (!folderId) return; // Stop if no folder is selected

                $.ajax({
                    url: '/project/folder/get-files/' +
                    folderId, // Adjust the route to your API endpoint
                    type: 'GET',
                    success: function(response) {
                        let fileDropdown = $('#newFile');
                        fileDropdown.empty().append(
                            '<option value="" disabled selected>Select File</option>');

                        if (response.files.length > 0) {
                            $.each(response.files, function(index, file) {
                                fileDropdown.append(
                                    `<option value="${file.id}">${file.name}</option>`
                                );
                            });

                            fileDropdown.closest('.form-group').removeClass(
                                'd-none'); // Show file dropdown
                        } else {
                            fileDropdown.closest('.form-group').addClass(
                                'd-none'); // Hide if no files
                        }
                    },
                    error: function() {
                        alert('Failed to fetch files. Please try again.');
                    }
                });
            });

            $('#saveAssigne').click(function() {
                let documentId = $('#documentId_').val();
                let fileId = $('#newFile').val();

                if (!fileId) {
                    alert('Please select a file.');
                    return;
                }

                $.ajax({
                    url: '/project/document/assign-document', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_id: documentId,
                        file_id: fileId
                    },
                    success: function(response) {
                        alert(response.message); // Show success message
                        $('#assigneToModal').modal('hide'); // Hide modal
                    },
                    error: function() {
                        alert('Failed to assign document. Please try again.');
                    }
                });
            });

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
                                        <a class="dropdown-item" id="changeStakeHolderForAllBtn" href="javascript:void(0);">Change Correspondence</a>
                                        <a class="dropdown-item" id="changeOwnerForAllBtn" href="javascript:void(0);">Change Owner</a>
                                        <a class="dropdown-item" id="changeDocTypeForAllBtn" href="javascript:void(0);">Change Document Type</a>
                                        <a class="dropdown-item" id="assignToForAllBtn" href="javascript:void(0);">Assign To File</a>
                                        <a class="dropdown-item text-danger" id="deleteForAllBtn" href="javascript:void(0);">Delete</a>
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



            $('#changeOwnerForAllBtn').on('click', function() {
                // Get all checked checkboxes
                const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');

                if (checkedCheckboxes.length === 0) {
                    alert('Please select at least one document.');
                    return;
                }

                // Collect the IDs of all checked checkboxes
                const documentIds = [];
                checkedCheckboxes.forEach(function(checkbox) {
                    documentIds.push(checkbox.value);
                });

                // Set the document IDs in a hidden input (optional)
                $('#documentIdsForAll').val(documentIds.join(','));

                // Open the modal
                $('#changeOwnerForAllModal').modal('show');
            });
            // Handle the form submission via AJAX
            $('#saveOwnerChangeForAll').on('click', function() {
                const newOwnerId = $('#newOwnerForAll').val(); // Get the new owner ID
                const documentIds = $('#documentIdsForAll').val().split(','); // Get the document IDs

                if (!newOwnerId || documentIds.length === 0) {
                    alert('Invalid input.');
                    return;
                }

                // Send an AJAX request to update the owner for all selected documents
                $.ajax({
                    url: "{{ route('project.document.change-owner-for-all') }}", // Route for changing owner for all
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        document_ids: documentIds,
                        new_owner_id: newOwnerId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#changeOwnerForAllModal').modal('hide');
                            alert('Owner changed successfully for all selected documents!');
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


            $('#assignToForAllBtn').on('click', function() {
                // Get all checked checkboxes
                const checkedCheckboxes5 = document.querySelectorAll('.row-checkbox:checked');

                if (checkedCheckboxes5.length === 0) {
                    alert('Please select at least one document.');
                    return;
                }

                // Collect the IDs of all checked checkboxes
                const documentIds5 = [];
                checkedCheckboxes5.forEach(function(checkbox) {
                    documentIds5.push(checkbox.value);
                });

                // Set the document IDs in a hidden input (optional)
                $('#documentIdsForAll5').val(documentIds5.join(','));

                // Open the modal
                $('#assignToForAllModal').modal('show');
            });

            $('#folder_id2').change(function() {
                let folderId = $(this).val();

                if (!folderId) return; // Stop if no folder is selected

                $.ajax({
                    url: '/project/folder/get-files/' +
                    folderId, // Adjust the route to your API endpoint
                    type: 'GET',
                    success: function(response) {
                        let fileDropdown = $('#newfileForAll');
                        fileDropdown.empty().append(
                            '<option value="" disabled selected>Select File</option>');

                        if (response.files.length > 0) {
                            $.each(response.files, function(index, file) {
                                fileDropdown.append(
                                    `<option value="${file.id}">${file.name}</option>`
                                );
                            });

                            fileDropdown.closest('.form-group').removeClass(
                                'd-none'); // Show file dropdown
                        } else {
                            fileDropdown.closest('.form-group').addClass(
                                'd-none'); // Hide if no files
                        }
                    },
                    error: function() {
                        alert('Failed to fetch files. Please try again.');
                    }
                });
            });
            // Handle the form submission via AJAX
            $('#saveAssignToForAll').on('click', function() {
                const fileId = $('#newfileForAll').val(); // Get the new owner ID
                const documentIds7 = $('#documentIdsForAll5').val().split(','); // Get the document IDs

                if (!fileId || documentIds7.length === 0) {
                    alert('Invalid input.');
                    return;
                }

                // Send an AJAX request to update the owner for all selected documents
                $.ajax({
                    url: "{{ route('project.document.assign-to-file-for-all') }}", // Route for changing owner for all
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        document_ids: documentIds7,
                        file_id: fileId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#assignToForAllModal').modal('hide');
                            alert('all selected documents assigned to selected file successfully!');
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Failed to assigned to file.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });



            $('#changeStakeHolderForAllBtn').on('click', function() {
                // Get all checked checkboxes
                const checkedCheckboxes2 = document.querySelectorAll('.row-checkbox:checked');

                if (checkedCheckboxes2.length === 0) {
                    alert('Please select at least one document.');
                    return;
                }

                // Collect the IDs of all checked checkboxes
                const documentIds2 = [];
                checkedCheckboxes2.forEach(function(checkbox) {
                    documentIds2.push(checkbox.value);
                });

                // Set the document IDs in a hidden input (optional)
                $('#documentIdsForAll2').val(documentIds2.join(','));

                // Open the modal
                $('#changeStakeHolderForAllModal').modal('show');
            });
            // Handle the form submission via AJAX
            $('#saveStakeHolderChangeForAll').on('click', function() {
                const newFromStakeHolderId = $('#newFromStakeHolderForAll').val(); // Get the new owner ID
                const newToStakeHolderId = $('#newToStakeHolderForAll').val(); // Get the new owner ID

                const documentIds2 = $('#documentIdsForAll2').val().split(','); // Get the document IDs

                if ((!newFromStakeHolderId && !newToStakeHolderId) || documentIds2.length === 0) {
                    alert('Invalid input.');
                    return;
                }

                // Send an AJAX request to update the owner for all selected documents
                $.ajax({
                    url: "{{ route('project.document.change-stake-holders-for-all') }}", // Route for changing owner for all
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        document_ids: documentIds2,
                        newFromStakeHolderId: newFromStakeHolderId,
                        newToStakeHolderId: newToStakeHolderId

                    },
                    success: function(response) {
                        if (response.success) {
                            $('#changeStakeHolderForAllModal').modal('hide');
                            alert(
                                'Stake Holders changed successfully for all selected documents!'
                            );
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Failed to change Stake Holders.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });


            $('#changeDocTypeForAllBtn').on('click', function() {
                // Get all checked checkboxes
                const checkedCheckboxes3 = document.querySelectorAll('.row-checkbox:checked');

                if (checkedCheckboxes3.length === 0) {
                    alert('Please select at least one document.');
                    return;
                }

                // Collect the IDs of all checked checkboxes
                const documentIds3 = [];
                checkedCheckboxes3.forEach(function(checkbox) {
                    documentIds3.push(checkbox.value);
                });

                // Set the document IDs in a hidden input (optional)
                $('#documentIdsForAll3').val(documentIds3.join(','));

                // Open the modal
                $('#changeDocTypeForAllModal').modal('show');
            });

            // Handle the form submission via AJAX
            $('#saveDocTypeChangeForAll').on('click', function() {
                const newDocTypeId = $('#newDocTypeForAll').val(); // Get the new owner ID
                const documentIds3 = $('#documentIdsForAll3').val().split(','); // Get the document IDs

                if (!newDocTypeId || documentIds3.length === 0) {
                    alert('Invalid input.');
                    return;
                }

                // Send an AJAX request to update the owner for all selected documents
                $.ajax({
                    url: "{{ route('project.document.change-doc-type-for-all') }}", // Route for changing owner for all
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        document_ids: documentIds3,
                        doc_type_id: newDocTypeId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#changeDocTypeForAllModal').modal('hide');
                            alert(
                                'Document Type changed successfully for all selected documents!'
                            );
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Failed to change document type.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });


            $('#deleteForAllBtn').on('click', function() {
                // Get all checked checkboxes
                const checkedCheckboxes4 = document.querySelectorAll('.row-checkbox:checked');

                if (checkedCheckboxes4.length === 0) {
                    alert('Please select at least one document.');
                    return;
                }

                // Collect the IDs of all checked checkboxes
                const documentIds4 = [];
                checkedCheckboxes4.forEach(function(checkbox) {
                    documentIds4.push(checkbox.value);
                });

                if (confirm(
                        'Are you sure you want to delete this Documents? This action cannot be undone.')) {
                    if (documentIds4.length === 0) {
                        alert('Invalid input.');
                        return;
                    }

                    // Send an AJAX request to update the owner for all selected documents
                    $.ajax({
                        url: "{{ route('project.document.delete-selected-docs') }}", // Route for changing owner for all
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            document_ids: documentIds4,

                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Selected Documents deleted successfully!');
                                location.reload(); // Reload the page to reflect changes
                            } else {
                                alert('Failed to delete documents.');
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred. Please try again.');
                            console.error(xhr.responseText);
                        }
                    });
                }
                // Open the modal
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
            },{
                "targets": 8, // Target the first column (index 0)
                "orderable": false // Disable sorting for this column
            }]
        });
    </script>
    <script>
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this Document? This action cannot be undone.')) {
                window.location.href = url; // Redirect to delete route
            }
        }
    </script>
    {{-- <script>
        // Function to handle "Select All" checkbox
        document.getElementById('selectAllCheckbox').addEventListener('change', function () {
            const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked; // Set all row checkboxes to the state of the "Select All" checkbox
            });
        });
    
        // Function to handle individual row checkboxes
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                const allChecked = Array.from(rowCheckboxes).every(checkbox => checkbox.checked);
                selectAllCheckbox.checked = allChecked; // Update "Select All" checkbox state
            });
        });
    </script> --}}
@endpush
