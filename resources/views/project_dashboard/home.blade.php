@extends('project_dashboard.layout.app')
@section('title', 'Project Home')
@section('content')
<style>
  #btn-outline-primary {
    color: blue;
  }
  #btn-outline-primary:hover {
    color: white; /* Change text color to white on hover */
  }
</style>

<div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
  <div class="col">
    <h2 class="h3 mb-0 page-title">{{$project->name}}</h2>
  </div>
  <div class="col-auto">
    {{-- <a type="button" href="{{route('account.create_project_view')}}" class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create Project</a> --}}
  </div>
</div>
@if(session('error'))
<div id="errorAlert" class="alert alert-danger" style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:brown;border-radius: 20px; color:beige;">
    {{ session('error') }}
</div>
@endif

@if(session('success'))
<div id="successAlert" class="alert alert-success"style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:green;border-radius: 20px; color:beige;">
    {{ session('success') }}
</div>
@endif
<div class="row">
  
  <div class="col-md-9">
  </div> <!-- .col -->
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
