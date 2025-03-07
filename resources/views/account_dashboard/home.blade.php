@extends('account_dashboard.layout.app')
@section('title', 'Admin Account Home')
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
    <h2 class="h3 mb-0 page-title">{{$account->name}}</h2>
  </div>
  <div class="col-auto">
    <a type="button" href="{{route('account.create_project_view')}}" class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create Project</a>
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
  <a href="{{route('account.projects')}}"class="col-md-3" style="text-decoration: none;">
    <div class="col-md-12" >
      <div class="card shadow mb-4"style="border-radius:15px;height: 80%;">
          <div class="card-body text-center"style="border-radius:15px;box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);">
            <div class="card-text my-2">
              <strong class="card-title my-0">Number Of Projects</strong>
              <h3 class=" text-muted mb-0">{{$project_count}}</h3>
            </div>
          </div> <!-- ./card-text -->
        
      </div>
    </div>
  </a>
  <a href="#"class="col-md-3"style="text-decoration: none;">
      <div class="col-md-12" >
        <div class="card shadow mb-4"style="border-radius:15px;height: 80%;">
            <div class="card-body text-center"style="border-radius:15px;box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);">
              <div class="card-text my-2">
                <strong class="card-title my-0">Number Of Users</strong>
                <h3 class=" text-muted mb-0">2</h3>
              </div>
            </div> <!-- ./card-text -->
          
        </div>
      </div>
  </a>
  <a href="{{route('account.EPS')}}"class="col-md-3"style="text-decoration: none;">
      <div class="col-md-12" >
        <div class="card shadow mb-4"style="border-radius:15px;height: 80%;">
            <div class="card-body text-center"style="border-radius:15px;box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);justify-content: center;">
              <div class="card-text my-2"style="margin:1.4rem 0rem 1.4rem 0rem !important;">
                <strong class="card-title my-0">EPS</strong>
              </div>
            </div> <!-- ./card-text -->
          
        </div>
      </div>
  </a>
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
