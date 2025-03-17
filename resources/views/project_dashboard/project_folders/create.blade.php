@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Create Project Folder')
@section('content')
<h2 class="page-title">Create New Project Folder</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
               

               
                <div class="col-md-12">
                    <form method="post" action="{{route('accounts.project-folders.store')}}" enctype="multipart/form-data">
                        @csrf
                    <div class="form-group mb-3">
                        <label for="simpleinputName">Name <span style="color: red">*</span></label>
                        <input type="text" name="name" required id="simpleinputName" class="form-control" placeholder="Name">
                    </div>

                    <div class="form-group mb-3">
                        <label for="example-order">Order</label>
                        <input type="number" name="order" id="example-order" class="form-control" placeholder="Order">
                    </div>
                    <div class="form-group mb-3">
                        <label for="against_id">Stakeholder</label>
                        <input type="text"style="background-color:#fff;" name="label1"
                                    id="against_id" class="form-control date"
                                    placeholder="Against"value="{{ old('label1') }}">
                    </div>
                    <div class="row">
                        <!-- Name Input -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="start_date">Date 1</label>
                                <input type="text" name="label2"
                                    id="start_date" class="form-control"
                                    placeholder="Start Date"value="{{ old('label2') }}">
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="end_date">Date 2</label>
                                <input type="text" name="label3" id="end_date"
                                    class="form-control" placeholder="End Date"value="{{ old('label3') }}">
                            </div>
                        </div>


                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="potential_impact"id="customCheck1">
                        <label class="custom-control-label" for="customCheck1">For Claim Files</label>
                      </div>
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="shortcut"id="shortcut">
                        <label class="custom-control-label" for="shortcut">Add To Shortcut Menu</label>
                      </div>
                    
                      <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary" style="margin-top: 10px;">Create</button>
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

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
        });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            flatpickr(".date", {
                enableTime: false,
                dateFormat: "Y-m-d", // Format: YYYY-MM-DD
            });

        });
    </script>
@endpush

