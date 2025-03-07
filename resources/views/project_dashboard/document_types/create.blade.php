@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Create Document Type')
@section('content')
<h2 class="page-title">Create New Document Type</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
               

               
                <div class="col-md-12">
                    <form method="post" action="{{route('accounts.document-types.store')}}" enctype="multipart/form-data">
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
                        <label for="example-order">Order</label>
                        <input type="number" name="order" id="example-order" class="form-control" placeholder="Order">
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

