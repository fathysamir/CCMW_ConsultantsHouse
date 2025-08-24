@extends('dashboard.layout.app')
@section('title', 'Admin Home - Edit Contract Tag')
@section('content')
    <h2 class="page-title">Update Contract Tag</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('accounts.contract-tags.update', $contract_tag->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="simpleinputName">Name</label>
                            <input type="text" name="name" required id="simpleinputName" class="form-control"
                                placeholder="Name" value="{{ $contract_tag->name }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-description">Description</label>
                            <textarea id="example-email" rows="5" name="description" class="form-control" placeholder="Description">{{ $contract_tag->description }}</textarea>
                        </div>


                        <div class="form-group mb-3">
                            <label for="example-sub_clause">Subclause</label>
                            <input type="text" name="sub_clause" id="example-sub_clause" class="form-control"
                                placeholder="Subclause" value="{{ $contract_tag->sub_clause }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-var_process">Variation Proccess Step</label>
                            <input type="number" name="var_process" id="example-var_process" class="form-control"
                                placeholder="Variation Proccess Step:"value="{{ $contract_tag->var_process }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-order">Order</label>
                            <input type="number" name="order" id="example-order" class="form-control" placeholder="Order"
                                value="{{ $contract_tag->order }}">
                        </div>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"
                                @if ($contract_tag->is_notice == '1') checked @endif name="is_notice"id="is_notice">
                            <label class="custom-control-label" for="is_notice">Is Notice</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox"
                                class="custom-control-input"@if ($contract_tag->for_letter == '1') checked @endif
                                name="for_letter"id="for_letter">
                            <label class="custom-control-label" for="for_letter">For Letter</label>
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
