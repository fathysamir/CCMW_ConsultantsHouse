@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Create Contract Tag')
@section('content')
<h2 class="page-title">Create New Contract Tag</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
               

               
                <div class="col-md-12">
                    <form method="post" action="{{route('accounts.contract-tags.store')}}" enctype="multipart/form-data">
                        @csrf
                    <div class="form-group mb-3">
                        <label for="simpleinputName">Name</label>
                        <input type="text" name="name" required id="simpleinputName" class="form-control" placeholder="Name">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-description">Description</label>
                        <textarea id="example-email" rows="5" name="description" class="form-control" placeholder="Description"></textarea>
                    </div>
                    
                    
                    <div class="form-group mb-3">
                        <label for="example-sub_clause">Subclause</label>
                        <input type="text" name="sub_clause" id="example-sub_clause" class="form-control" placeholder="Subclause">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-var_process">Variation Proccess Step</label>
                        <input type="number" name="var_process" id="example-var_process" class="form-control" placeholder="Variation Proccess Step:">
                    </div>
                    <div class="form-group mb-3">
                        <label for="example-order">Order</label>
                        <input type="number" name="order" id="example-order" class="form-control" placeholder="Order">
                    </div>
                    
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="is_notice"id="is_notice">
                        <label class="custom-control-label" for="is_notice">Is Notice</label>
                      </div>
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="for_letter"id="for_letter">
                        <label class="custom-control-label" for="for_letter">For Letter</label>
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
@endpush

