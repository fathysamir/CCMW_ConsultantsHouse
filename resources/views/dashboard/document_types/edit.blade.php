@extends('dashboard.layout.app')
@section('title', 'Admin Home - Edit Document Type')
@section('content')
    <h2 class="page-title">Update Contract Tag</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('accounts.document-types.update', $document_type->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="simpleinputName">Name</label>
                            <input type="text" name="name" required id="simpleinputName" class="form-control"
                                placeholder="Name" value="{{ $document_type->name }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-description">Description</label>
                            <textarea id="example-email" rows="5" name="description" class="form-control" placeholder="Description">{{ $document_type->description }}</textarea>
                        </div>



                        <div class="form-group mb-3">
                            <label for="example-order">Order</label>
                            <input type="number" name="order" id="example-order" class="form-control" placeholder="Order"
                                value="{{ $document_type->order }}">
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

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
        });
    </script>
@endpush
