@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Edit Note')
@section('content')
    <style>
        .date {
            background-color: #fff !important;
        }
    </style>
    <h2 class="page-title">Edit Note</h2>
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
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <form method="post" action="{{ route('project.update-note', $note->slug) }}"
                        enctype="multipart/form-data">
                        @csrf


                        <div class="row">
                            <!-- Type Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="type">Type <span style="color: red">*</span></label>
                                    <input type="text" disabled id="type" class="form-control" placeholder="Note"
                                        value="Note/Activity">
                                </div>
                            </div>

                            <!-- Analyzed By Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="owner">Analyzed By <span style="color: red">*</span></label>
                                    <select class="form-control" id="owner" required name="user_id">
                                        <option value="" disabled>please select</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ $note->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="Subject">Subject <span style="color: red">*</span></label>
                            <input type="text" name="subject" required id="Subject" class="form-control"
                                placeholder="Subject" value="{{ $note->subject }}">
                        </div>
                        <div class="row">
                            <!-- Start Date Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date">Start Date <span style="color: red">*</span></label>
                                    <input required type="date" style="background-color:#fff;" name="start_date"
                                        id="start_date" class="form-control date" placeholder="Start Date"
                                        value="{{ $note->start_date }}">
                                </div>
                            </div>

                            <!-- End Date Input -->
                            <div class="col-md-6" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="end_date">End Date</label>
                                    <input type="date" style="background-color:#fff;" name="end_date" id="end_date"
                                        class="form-control date" placeholder="End Date" value="{{ $note->end_date }}">
                                </div>
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="With-Return"
                                {{ $note->end_date ? 'checked' : '' }}>
                            <label class="custom-control-label" for="With-Return">This Note With Return</label>
                        </div>
                        <!-- Start Date Input -->

                        <div class="form-group mb-3">
                            <label>Assign Document To File</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-control" id="folder_id">
                                        <option value="" disabled selected>Select Folder</option>
                                        @foreach ($folders as $key => $name)
                                            <option value="{{ $key }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 d-none files_">
                                    <select class="form-control" id="newFile" name="file_id">
                                        <option value="" disabled selected>Select File</option>

                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-group mb-3">
                            <label for="Note">Note</label>
                            <textarea name="notes" rows="5" id="Note" class="form-control" placeholder="Note">{{ $note->note }}</textarea>
                        </div>


                        <input type="hidden" name="action" id="formAction" value="save">
                        <div class="text-right" style="margin-top: 10px;">
                            <button type="submit"
                                class="btn mb-2 btn-outline-success"onclick="document.getElementById('formAction').value='save'">Save</button>
                            <button type="submit" class="btn mb-2 btn-outline-primary"
                                onclick="document.getElementById('formAction').value='update'">Update</button>
                            <button type="button"
                                class="btn mb-2 btn-outline-secondary"@if (session()->has('current_view') && session('current_view') == 'file_doc') onclick="window.location.href='/project/file-document-first-analyses/<?php echo session('current_file_doc'); ?>'" @elseif(session()->has('current_view') && session('current_view') == 'file') onclick="window.location.href='/project/file/<?php echo session('current_file2'); ?>/documents'" @else 
                                onclick="window.location.href='/project/all-notes'" @endif>Back</button>
                        </div>
                    </form>
                </div> <!-- /.col -->
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
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

                            fileDropdown.closest('.files_').removeClass(
                                'd-none'); // Show file dropdown
                        } else {
                            fileDropdown.closest('.files_').addClass(
                                'd-none'); // Hide if no files
                        }
                    },
                    error: function() {
                        alert('Failed to fetch files. Please try again.');
                    }
                });
            });
            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
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

            const withReturnCheckbox = document.getElementById("With-Return");
            const endDateDiv = document.getElementById("end_date").closest(".col-md-6");

            // Function to toggle visibility
            function toggleEndDate() {
                if (withReturnCheckbox.checked) {
                    endDateDiv.style.display = "block";
                } else {
                    endDateDiv.style.display = "none";
                }
            }

            // Add event listener to the checkbox
            withReturnCheckbox.addEventListener("change", toggleEndDate);

            // Initialize on page load (in case of form repopulation)
            toggleEndDate();


        });
    </script>
@endpush
