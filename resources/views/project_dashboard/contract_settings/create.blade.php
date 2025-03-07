@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Create Contract Setting')
@section('content')
<h2 class="page-title">Create New {{ucwords(str_replace('_', ' ', $type))}}</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
               

               
                <div class="col-md-12">
                    <form method="post" action="{{route('accounts.contract-settings.store')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{$type}}" name="type">
                    <div class="form-group mb-3">
                        <label for="simpleinputName">Name</label>
                        <input type="text" name="name" required id="simpleinputName" class="form-control" placeholder="Name">
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

