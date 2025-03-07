@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Create New File')
@section('content')
    <h2 class="page-title">Create New File</h2>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('project.files.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="code">File #</label>
                                    <input type="text" name="code" id="code" class="form-control"
                                        placeholder="File ID"value="{{ old('code') }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-9">
                                <div class="form-group mb-3">
                                    <label for="simpleinputName">Name <span style="color: red">*</span></label>
                                    <input required type="text" name="name" required id="simpleinputName"
                                        class="form-control" placeholder="Name"value="{{ old('name') }}">
                                </div>
                            </div>


                        </div>


                        <div class="form-group mb-3">
                            <label for="owner">File Owner <span style="color: red">*</span></label>
                            <select class="form-control" id="owner" required name="owner_id">
                                <option value="" selected disabled>please select</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="against_id">{{$folder->label1}}</label>
                            <select class="form-control" id="against_id" name="against_id">
                                <option value="" selected disabled>please select</option>
                                @foreach ($stake_holders as $stake_holder)
                                    <option value="{{ $stake_holder->id }}"
                                       >{{ $stake_holder->name }} -
                                        {{ $stake_holder->role }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date">{{$folder->label2}}</label>
                                    <input type="date"style="background-color:#fff;" name="start_date" id="start_date"
                                        class="form-control date"
                                        placeholder="Start Date"value="{{ old('start_date') }}">
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="end_date">{{$folder->label3}}</label>
                                    <input type="date"style="background-color:#fff;" name="end_date" id="end_date"
                                        class="form-control date"
                                        placeholder="End Date"value="{{ old('end_date') }}">
                                </div>
                            </div>


                        </div>
                        <div class="form-group mb-3">
                            <label for="Note">Note</label>
                            <textarea name="notes" rows="7" id="Note" class="form-control" placeholder="Note">{{ old('notes') }}</textarea>
                        </div>

                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                @if ($folder->potential_impact == '1')
                                    <div class="row">
                                        <p style="margin-right: 10px;">Potential Impact : </p>
                                        <div class="custom-control custom-checkbox" style="margin-right: 20px;">
                                            <input type="checkbox" class="custom-control-input" name="time"id="time">
                                            <label class="custom-control-label" for="time">Time</label>
                                        </div>
                                        <div class="custom-control custom-checkbox"style="margin-right: 20px;">
                                            <input type="checkbox" class="custom-control-input"
                                                name="prolongation_cost"id="prolongation_cost">
                                            <label class="custom-control-label" for="prolongation_cost">Prolongation
                                                Cost</label>
                                        </div>
                                        <div class="custom-control custom-checkbox"style="margin-right: 20px;">
                                            <input type="checkbox" class="custom-control-input"
                                                name="disruption_cost"id="disruption_cost">
                                            <label class="custom-control-label" for="disruption_cost">Disruption
                                                Cost</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                name="variation"id="variation">
                                            <label class="custom-control-label" for="variation">Variation</label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="custom-control custom-checkbox"style="margin-right: 20px;">
                                        <input type="checkbox" class="custom-control-input" name="closed"id="closed">
                                        <label class="custom-control-label" for="closed">Closed</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                            name="assess_not_pursue"id="assess_not_pursue">
                                        <label class="custom-control-label" for="assess_not_pursue">Assessed Not To
                                            Pursue</label>
                                    </div>

                                </div>
                            </div>


                        </div>

                        <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary"
                            style="margin-top: 10px;">Create</button>
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
