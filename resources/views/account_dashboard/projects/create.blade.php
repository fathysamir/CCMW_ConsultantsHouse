@extends('account_dashboard.layout.app')
@section('title', 'Create Project')
@section('content')
    <style>
        #epsTree {
            list-style-type: none;
            padding-left: 20px;
        }

        #epsTree li {
            margin: 5px 0;
            cursor: pointer;
        }

        #epsTree ul {
            padding-left: 20px;
            border-left: 1px solid #ccc;
        }

        .category-name {
            font-weight: bold;
        }
    </style>
    <h2 class="page-title">Create New Project</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route($route) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="simpleinputName">Project Name</label>
                                    <input type="text" name="name" required id="simpleinputName" class="form-control"
                                        placeholder="Name" value="{{ old('name') }}">
                                    @if ($errors->has('name'))
                                        <p class="text-error more-info-err" style="color: red;">
                                            {{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="example-site">Site #</label>
                                    <input type="text" id="example-site" name="code" class="form-control" required
                                        placeholder="Site #"value="{{ old('code') }}">
                                    @if ($errors->has('code'))
                                        <p class="text-error more-info-err" style="color: red;">
                                            {{ $errors->first('code') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contract_date">Contract Date</label>
                                    <input type="date" name="contract_date" required id="contract_date"
                                        style="background-color:#fff;" class="form-control date"
                                        placeholder="Contract Date"value="{{ old('contract_date') }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="commencement_date">Commencement Date</label>
                                    <input type="date" id="commencement_date"
                                        name="commencement_date"style="background-color:#fff;" class="form-control date"
                                        required placeholder="Commencement Date"value="{{ old('commencement_date') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="Conditions_of_Contract">Conditions of Contract</label>
                            <textarea name="condation_contract" rows="3" id="Conditions_of_Contract" class="form-control"
                                placeholder="Conditions of Contract">{{ old('condation_contract') }}</textarea>
                        </div>

                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="Original_Value">Original Value</label>
                                    <input type="number" name="original_value" id="Original_Value" class="form-control"
                                        placeholder="Original Value" step="0.01"value="{{ old('original_value') }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="currency">Currency</label>
                                    <input type="text" id="currency" name="currency" class="form-control"
                                        placeholder="Currency"value="{{ old('currency') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="revised_value">Revised Value</label>
                                    <input type="number" id="revised_value" name="revised_value" class="form-control"
                                        placeholder="Revised Value"step="0.01"value="{{ old('revised_value') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="measurement_basis">Measurement Basis</label>
                            <input type="text" name="measurement_basis" id="measurement_basis" class="form-control"
                                placeholder="Measurement Basis"value="{{ old('measurement_basis') }}">
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes">Notes</label>
                            <textarea name="notes" rows="3" id="notes" class="form-control" placeholder="Notes">{{ old('notes') }}</textarea>
                        </div>






                        <div class="form-group mb-3">
                            <label for="customFile">Project Logo</label>
                            <div class="custom-file">
                                <input name="logo" type="file" class="custom-file-input" id="customFile"
                                    onchange="previewImage(event)">
                                <label class="custom-file-label" for="customFile">Choose Image</label>
                            </div>
                            <!-- Image Preview -->
                            <div class="mt-3">
                                <img id="imagePreview" src="" alt="Image Preview" class="img-thumbnail"
                                    style="display: none; max-width: 200px; height: auto;">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="summary">Summary Scope of works</label>
                            <textarea name="summary" rows="5" id="summary" class="form-control" placeholder="Summary Scope of works">{{ old('summary') }}</textarea>
                        </div>
<?php
  $stakeholders_counter=0;
  $milestones_counter=0;
?>
                        <fieldset class="border p-3 mb-3">
                            <legend style="width: 190px;margin-bottom: 0px;">Key Stakeholders</legend>
                            <div class="d-flex justify-content-between align-items-center" style="margin-top: -25px;">
                                <legend class="w-auto px-2 text-primary mb-0"></legend>
                                <button type="button" class="btn btn-primary btn-sm"id="addStakeholder">Add
                                    Stakeholder</button>
                            </div>
                            <div class="row">
                                <!-- Name Input -->
                                <div class="col-md-3"><label>Role</label></div>
                                <div class="col-md-3"><label>Name</label></div>
                                <div class="col-md-3"><label>For Chronology</label></div>
                                <div class="col-md-2"><label>Article</label></div>
                                <div class="col-md-1"></div>
                            </div>
                            <div id="stakeholdersContainer">
                                <div class="row stakeholder-row">
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <select name="stakeholders[{{$stakeholders_counter}}][role]" required class="form-control">
                                                <option value='' selected disabled>Select Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role }}">{{ $role }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <input type="text" name="stakeholders[{{$stakeholders_counter}}][name]" class="form-control"
                                                placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <input type="text" name="stakeholders[{{$stakeholders_counter}}][chronology]" class="form-control"
                                                placeholder="Chronology">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mb-3">
                                            <select name="stakeholders[{{$stakeholders_counter}}][article]" class="form-control">
                                                <option value=""></option>
                                                <option value="the">The</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group mb-3">
                                            <div class="p-1 remove-stakeholder"><span
                                                    class="fe fe-24 fe-minus-circle"></span></div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                        <fieldset class="border p-3 mb-3">
                            <legend style="width: 120px;margin-bottom: 0px;">Milestones</legend>
                            <div class="d-flex justify-content-between align-items-center" style="margin-top: -25px;">
                                <legend class="w-auto px-2 text-primary mb-0"></legend>
                                <button type="button" class="btn btn-primary btn-sm"id="addMilestone">Add
                                    Milestone</button>
                            </div>
                            <div class="row">
                                <!-- Name Input -->
                                <div class="col-md-5"><label>Name</label></div>
                                <div class="col-md-3"><label>Contractual Finish Date</label></div>
                                <div class="col-md-3"><label>Revised Finish Date</label></div>
                                <div class="col-md-1"></div>
                            </div>
                            <div id="milestonesContainer">
                                <div class="row milestone-row">
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <input type="text" required name="milestones[{{$milestones_counter}}][name]" class="form-control"
                                                placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <input type="date" name="milestones[{{$milestones_counter}}][contract_finish_date]"
                                                class="form-control date"
                                                placeholder="Contractual Finish Date"style="background-color:#fff;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <input type="date" name="milestones[{{$milestones_counter}}][revised_finish_date]"
                                                class="form-control date"
                                                placeholder="Revised Finish Date"style="background-color:#fff;">
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group mb-3">
                                            <div class="p-1 remove-milestone"><span
                                                    class="fe fe-24 fe-minus-circle"></span></div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </fieldset>
                        <fieldset class="border p-3 mb-3">
                            <legend style="width: 45px;margin-bottom: 0px;">EPS</legend>

                            <label for="Original_Value">Chose Project Location</label>
                            <div class="form-group">
                                <ul id="epsTree">
                                    @foreach ($EPS as $category)
                                        @include('account_dashboard.partials.category_tree', [
                                            'category' => $category,
                                        ])
                                    @endforeach
                                </ul>
                                @if ($errors->has('selected_category'))
                                <p class="text-error more-info-err" style="color: red;">
                                    {{ $errors->first('selected_category') }}</p>
                            @endif
                            </div>
                        </fieldset>
                        <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary"
                            style="margin-top: 10px;">Create</button>
                    </form>
                </div> <!-- /.col -->

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            flatpickr(".date", {
                enableTime: false,
                dateFormat: "Y-m-d", // Format: YYYY-MM-DD
            });

            document.querySelectorAll("#epsTree li").forEach(li => {
                li.addEventListener("dblclick", function(event) {
                    let sublist = this.querySelector("ul");
                    if (sublist) {
                        sublist.style.display = sublist.style.display === "none" ? "block" : "none";
                    }
                    event.stopPropagation(); // Prevent event bubbling
                });
            });
            document.querySelectorAll('.category-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    document.querySelectorAll('.category-checkbox').forEach(cb => {
                        if (cb !== this) cb.checked = false;
                    });
                });
            });
        });
    </script>
    <script>
        function previewImage(event) {
            var input = event.target;
            var reader = new FileReader();

            reader.onload = function() {
                var img = document.getElementById('imagePreview');
                img.src = reader.result;
                img.style.display = 'block'; // Show the image
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]); // Read the uploaded image
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            let stakeholderCounter = 1;
            let milestoneCounter = 1;
            $("#addStakeholder").click(function() {
                let newRow = `
                    <div class="row stakeholder-row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <select name="stakeholders[${stakeholderCounter}][role]"required class="form-control">
                                    <option value='' selected disabled>Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <input type="text" name="stakeholders[${stakeholderCounter}][name]" class="form-control" placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <input type="text" name="stakeholders[${stakeholderCounter}][chronology]" class="form-control" placeholder="Chronology">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <select name="stakeholders[${stakeholderCounter}][article]" class="form-control">
                                    <option value=""></option>
                                    <option value="the">The</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mb-3">
                                <div class="p-1 remove-stakeholder"><span class="fe fe-24 fe-minus-circle"></span></div>
                            </div>
                        </div>
                    </div>
                `;
                $("#stakeholdersContainer").append(newRow);
                stakeholderCounter++;
            });

            $(document).on("click", ".remove-stakeholder", function() {
                $(this).closest(".stakeholder-row").remove();
            });

            $("#addMilestone").click(function() {
                let newRow = `
                     <div class="row milestone-row">
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <input type="text"required name="milestones[${milestoneCounter}][name]" class="form-control"
                                            placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <input type="date" name="milestones[${milestoneCounter}][contract_finish_date]" class="form-control date"
                                                placeholder="Contractual Finish Date"style="background-color:#fff;">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <input type="date" name="milestones[${milestoneCounter}][revised_finish_date]" class="form-control date"
                                                placeholder="Revised Finish Date"style="background-color:#fff;">
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="form-group mb-3">
                                            <div class="p-1 remove-milestone"><span
                                                    class="fe fe-24 fe-minus-circle"></span></div>

                                        </div>
                                    </div>
                                </div>
                `;
                $("#milestonesContainer").append(newRow);
                milestoneCounter++;
                flatpickr(".date", {
                    enableTime: false,
                    dateFormat: "Y-m-d", // Format: YYYY-MM-DD
                });
            });

            $(document).on("click", ".remove-milestone", function() {
                $(this).closest(".milestone-row").remove();
            });
        });
    </script>
@endpush
