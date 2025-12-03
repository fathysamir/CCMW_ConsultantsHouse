@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Update File')
@section('content')
    <style>
        .date {
            background-color: #fff !important;
        }

        .select2-selection__choice {
            margin-top: 5px !important;
        }
    </style>
    <h2 class="page-title">Update File</h2>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('project.files.update', $file->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="code">File #</label>
                                    <input type="number" name="code" id="code" class="form-control"
                                        placeholder="File ID"value="{{ old('code', $file->code) }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-9">
                                <div class="form-group mb-3">
                                    <label for="simpleinputName">Name <span style="color: red">*</span></label>
                                    <input required type="text" name="name" required id="simpleinputName"
                                        class="form-control" placeholder="Name"value="{{ old('name', $file->name) }}">
                                </div>
                            </div>


                        </div>

                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="owner">File Owner <span style="color: red">*</span></label>
                                    <select class="form-control" id="owner" required name="owner_id">
                                        <option value="" selected disabled>please select</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                @if ($file->user_id == $user->id) selected @endif>
                                                {{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-9">
                                @if ($folder->potential_impact == '1')
                                    <label for="multi-select2_3">Impact Milestones.</label>
                                    <select class="form-control xxx" id="multi-select2_3" name="milestones[]" multiple>
                                        @foreach ($milestones as $milestone)
                                            <option value="{{ $milestone->id }}"
                                                {{ $file->milestones ? (in_array($milestone->id, array_map('intval', explode(',', $file->milestones))) ? 'selected' : '') : '' }}>
                                                {{ $milestone->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="against_id">{{ $folder->label1 }}</label>
                            <select class="form-control" id="against_id" name="against_id">
                                <option value="" selected disabled>please select</option>
                                @foreach ($stake_holders as $stake_holder)
                                    <option value="{{ $stake_holder->id }}"
                                        @if ($file->against_id == $stake_holder->id) selected @endif>{{ $stake_holder->name }} -
                                        {{ $stake_holder->role }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6" style="padding-right:0px !important;">
                                <div class="row">
                                    <!-- Name Input -->
                                    <div class="col-md-3" style="padding-right:0px !important;">
                                        <div class="form-group mb-3">
                                            <label for="start_date">{{ $folder->label2 }}</label>
                                            <input type="date" name="start_date" id="start_date"
                                                class="form-control date"
                                                placeholder="Start Date"value="{{ old('start_date', $file->start_date) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group mb-3">
                                            <div
                                                style="display: flex; justify-content: space-between; align-items: center;">
                                                <label for="sup_doc_1">Supporting Document</label>
                                                <!-- Conditionally show the "View PDF" icon if the old document exists -->
                                                @if ($file->sup_doc_1 && $file->sup_document_1->storage_file_id)
                                                    <div style="display: flex;cursor: pointer;">
                                                        <img src="{{ asset('/dashboard/assets/selected_images/eye3.png') }}"
                                                            width="50"
                                                            style="margin-bottom: -20px;position: relative; top: -10px;"id="viewPdf1"
                                                            title="PDF"
                                                            data-file-path="{{ $file->sup_document_1->storageFile->path }}">
                                                    </div>
                                                @else
                                                    <div style="display: flex;margin-right:6%;cursor: pointer;">
                                                        <img class="d-none"
                                                            src="{{ asset('/dashboard/assets/selected_images/eye3.png') }}"
                                                            width="50" title="PDF"
                                                            style="margin-bottom: -10px;"id="viewPdf1">
                                                    </div>
                                                @endif
                                            </div>
                                            <select class="form-control select2" id="sup_doc_1" name="sup_doc_1">
                                                <option value="">Select Document</option>
                                                @foreach ($all_documents as $doc)
                                                    <option value="{{ $doc->id }}"
                                                        data-file-path="{{ $doc->storageFile->path }}"
                                                        @if ($file->sup_doc_1 == $doc->id) selected @endif>
                                                        {{ $doc->reference }} ➡️
                                                        {{ $doc->subject }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="Description1">Description</label>
                                            <textarea name="description1" rows="5" id="Description1" class="form-control" placeholder="Description">{{ old('description1', $file->description1) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="row">
                                    <!-- Name Input -->
                                    <div class="col-md-3" style="padding-right:0px !important;">
                                        <div class="form-group mb-3">
                                            <label for="end_date">{{ $folder->label3 }}</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control date"
                                                placeholder="End Date"value="{{ old('end_date', $file->end_date) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group mb-3">
                                            <div
                                                style="display: flex; justify-content: space-between; align-items: center;">
                                                <label for="sup_doc_2">Supporting Document</label>
                                                <!-- Conditionally show the "View PDF" icon if the old document exists -->
                                                @if ($file->sup_doc_2 && $file->sup_document_2->storage_file_id)
                                                    <div style="display: flex;cursor: pointer;">
                                                        <img src="{{ asset('/dashboard/assets/selected_images/eye3.png') }}"
                                                            width="50"
                                                            style="margin-bottom: -20px;position: relative; top: -10px;"id="viewPdf2"
                                                            title="PDF"
                                                            data-file-path="{{ $file->sup_document_2->storageFile->path }}">
                                                    </div>
                                                @else
                                                    <div style="display: flex;margin-right:6%;cursor: pointer;">
                                                        <img class="d-none"
                                                            src="{{ asset('/dashboard/assets/selected_images/eye3.png') }}"
                                                            width="50" title="PDF"
                                                            style="margin-bottom: -10px;"id="viewPdf2">
                                                    </div>
                                                @endif
                                            </div>
                                            <select class="form-control select2" id="sup_doc_2" name="sup_doc_2">
                                                <option value="">Select Document</option>
                                                @foreach ($all_documents as $doc)
                                                    <option
                                                        value="{{ $doc->id }}"data-file-path="{{ $doc->storageFile->path }}"
                                                        @if ($file->sup_doc_2 == $doc->id) selected @endif>
                                                        {{ $doc->reference }} ➡️
                                                        {{ $doc->subject }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="Description2">Description</label>
                                            <textarea name="description2" rows="5" id="Description2" class="form-control" placeholder="Description">{{ old('description2', $file->description2) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="form-group mb-3">
                            <label for="Note">Note</label>
                            <textarea name="notes" rows="7" id="Note" class="form-control" placeholder="Note">{{ old('notes', $file->notes) }}</textarea>
                        </div>


                        <div class="col-md-12">
                            @if ($folder->potential_impact == '1')
                                <div class="row">
                                    <p style="margin-right: 10px;">Potential Impact : </p>
                                    <div class="custom-control custom-checkbox" style="margin-right: 20px;">
                                        <input type="checkbox" class="custom-control-input" name="time"id="time"
                                            @if ($file->time == '1') checked @endif>
                                        <label class="custom-control-label" for="time">Time</label>
                                    </div>
                                    <div class="custom-control custom-checkbox"style="margin-right: 20px;">
                                        <input type="checkbox" class="custom-control-input"
                                            name="prolongation_cost"id="prolongation_cost"
                                            @if ($file->prolongation_cost == '1') checked @endif>
                                        <label class="custom-control-label" for="prolongation_cost">Prolongation
                                            Cost</label>
                                    </div>
                                    <div class="custom-control custom-checkbox"style="margin-right: 20px;">
                                        <input type="checkbox" class="custom-control-input"
                                            name="disruption_cost"id="disruption_cost"
                                            @if ($file->disruption_cost == '1') checked @endif>
                                        <label class="custom-control-label" for="disruption_cost">Disruption
                                            Cost</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                            name="variation"id="variation"
                                            @if ($file->variation == '1') checked @endif>
                                        <label class="custom-control-label" for="variation">Variation</label>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Email Input -->



                        <div style="display: flex; width:100%;">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="custom-control custom-checkbox"style="margin-right: 20px;">
                                        <input type="checkbox" class="custom-control-input" name="closed"id="closed"
                                            @if ($file->closed == '1') checked @endif>
                                        <label class="custom-control-label" for="closed">Closed</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                            name="assess_not_pursue"id="assess_not_pursue"
                                            @if ($file->assess_not_pursue == '1') checked @endif>
                                        <label class="custom-control-label" for="assess_not_pursue">Assessed Not To
                                            Pursue</label>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <p style="margin-bottom:0px;">Percentage Analysis Complete : </p>
                                    <input type="number" name="Percentage_Analysis_Complete"
                                        class="form-control"value="{{ $file->analyses_complete }}"
                                        style="width: 12%;margin-left:2%; margin-top:-5px;" min="0" max="100"
                                        oninput="this.value = Math.min(Math.max(0, this.value), 100)">
                                    <p style="margin-left:3px;"> %</p>


                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary"
                            style="margin-top: 10px;">Save</button>
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
            const eye1 = document.getElementById("viewPdf1");
            const eye2 = document.getElementById("viewPdf2");
            
            $('#sup_doc_1').on('select2:select', function(e) {
                // This gives us the <option> element that Select2 actually selected
                const originalOption = e.params.data.element;

                // Read the file path
                const filePath = originalOption.getAttribute("data-file-path");

                const eye1 = document.getElementById("viewPdf1");

                if (filePath) {
                    eye1.setAttribute("data-file-path", filePath);
                    eye1.classList.remove("d-none");
                } else {
                    eye1.classList.add("d-none");
                    eye1.removeAttribute("data-file-path");
                }
            });

            // when clicking the eye icon → open pdf
            eye1.addEventListener("click", function() {
                const pdfPath = this.getAttribute("data-file-path");
                if (pdfPath) {
                    window.open('/' + pdfPath, "_blank");
                }
            });
            eye2.addEventListener("click", function() {
                const pdfPath = this.getAttribute("data-file-path");
                if (pdfPath) {
                    window.open('/' + pdfPath, "_blank");
                }
            });
            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds

            // const viewPdfIcon1 = document.getElementById('viewPdf1');

            // if (viewPdfIcon1) {
            //     viewPdfIcon1.addEventListener('click', function() {
            //         const filePath = this.getAttribute('data-file-path');
            //         if (filePath) {
            //             window.open('/' + filePath, '_blank'); // Open the file in a new tab
            //         }
            //     });
            // }
            // const viewPdfIcon2 = document.getElementById('viewPdf2');

            // if (viewPdfIcon2) {
            //     viewPdfIcon2.addEventListener('click', function() {
            //         const filePath = this.getAttribute('data-file-path');
            //         if (filePath) {
            //             window.open('/' + filePath, '_blank'); // Open the file in a new tab
            //         }
            //     });
            // }
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

        });
    </script>
@endpush
