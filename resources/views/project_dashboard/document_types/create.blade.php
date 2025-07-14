@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Create Document Type')
@section('content')
    <h2 class="page-title">Create New Document Type</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('accounts.document-types.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="simpleinputName">Name</label>
                            <input type="text" name="name" required id="simpleinputName" class="form-control"
                                placeholder="Name">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-description">Description</label>
                            <textarea id="example-email" rows="5" name="description" class="form-control" placeholder="Description"></textarea>
                        </div>
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="from_id">From</label>
                                    <select class="form-control" id="from_id" name="from_id">
                                        <option selected disabled>please select</option>
                                        @foreach ($stake_holders as $stake_holder)
                                            <option value="{{ $stake_holder->id }}">{{ $stake_holder->narrative }} -
                                                {{ $stake_holder->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="to_id">To</label>
                                    <select class="form-control" id="to_id" name="to_id">
                                        <option selected disabled>please select</option>
                                        @foreach ($stake_holders as $stake_holder)
                                            <option value="{{ $stake_holder->id }}">{{ $stake_holder->narrative }} -
                                                {{ $stake_holder->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                        </div>

                        <div class="row">
                            <!-- Type Input -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="example-order">Order</label>
                                    <input type="number" name="order" id="example-order" class="form-control"
                                        placeholder="Order">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="relevant_word">Relevant Verb</label>
                                    <input type="text" name="relevant_word" id="relevant_word" class="form-control"
                                        placeholder="Relevant Word">
                                </div>
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="shortcut"id="shortcut">
                            <label class="custom-control-label" for="shortcut">Add To Shortcut Menu</label>
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
@endpush
