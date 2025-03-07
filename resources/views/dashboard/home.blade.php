@extends('dashboard.layout.app')
@section('title', 'Admin Home')
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
    <h2 class="h3 mb-0 page-title">Accounts</h2>
  </div>
  <div class="col-auto">
    <a type="button" href="{{route('accounts.create')}}" class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create Account</a>
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
  @if(!empty($all_accounts) && $all_accounts->count())
    @foreach($all_accounts as $account)
      <div class="col-md-3">
        <div class="card shadow mb-4">
          <a href="{{route('switch.account',$account->id)}}"style="text-decoration: none;">
            <div class="card-body text-center">
              <div class="avatar avatar-lg mt-4">
                
                  <img   @if(getFirstMediaUrl($account,$account->logoCollection)!=null) style="width: 64px !important; height:64px !important;" src="{{getFirstMediaUrl($account,$account->logoCollection)}}" @else src="{{asset('dashboard/assets/images/images.png')}}" style="width: 78px !important; height:65px !important;" @endif alt="..." class="avatar-img rounded-circle">
               
              </div>
              <div class="card-text my-2">
                <strong class="card-title my-0">{{$account->name}}</strong>
                <p class="small text-muted mb-0">{{$account->email}}</p>
                <p class="small"><span class="badge badge-light text-muted">{{$account->phone_no}}</span></p>
              </div>
            </div> <!-- ./card-text -->
          </a>
          
          <div class="card-footer">
            <div class="row align-items-center justify-content-between">
              <div class="col-auto">
                <small>
                  <span class="dot dot-lg @if($account->active=='1') bg-success @else bg-secondary @endif mr-1"></span> Activation </small>
              </div>
              <div class="col-auto">
                <div class="file-action">
                  <button type="button" class="btn btn-link dropdown-toggle more-vertical p-0 text-muted mx-auto" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="text-muted sr-only">Action</span>
                  </button>
                  <div class="dropdown-menu m-2">
                    <a class="dropdown-item" href="#"><i class="fe fe-meh fe-12 mr-4"></i>Profile</a>
                    <a class="dropdown-item" href="#"><i class="fe fe-message-circle fe-12 mr-4"></i>Chat</a>
                    <a class="dropdown-item" href="#"><i class="fe fe-mail fe-12 mr-4"></i>Contact</a>
                    <a class="dropdown-item" href="#"><i class="fe fe-delete fe-12 mr-4"></i>Delete</a>
                  </div>
                </div>
              </div>
            </div>
          </div> <!-- /.card-footer -->
        </div>
      </div>
    @endforeach
  @else
  <div class="col-md-12" style="text-align: center;">
    <h2>There are no accounts .</h2>
  </div>
  @endif
  <div class="col-md-9">
  </div> <!-- .col -->
</div>
<div class="d-flex justify-content-center">
 
    {!! $all_accounts->appends(['search' => request('search')])->links("pagination::bootstrap-4") !!}

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
