@extends('account_dashboard.layout.app')
@section('title', 'Admin Account Home - Edit Contract Setting')
@section('content')
    <h2 class="page-title">Update {{ucwords(str_replace('_', ' ', $contract_setting->type))}}</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('accounts.contract-settings.update', $contract_setting->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="simpleinputName">Name</label>
                            <input type="text" name="name" required id="simpleinputName" class="form-control"
                                placeholder="Name" value="{{ $contract_setting->name }}">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="example-order">Order</label>
                            <input type="number" name="order" id="example-order" class="form-control" placeholder="Order"
                                value="{{ $contract_setting->order }}">
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
