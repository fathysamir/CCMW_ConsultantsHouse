@extends('project_dashboard.layout.app')
@section('title', 'Project Home')
@section('content')
    <style>
        #btn-outline-primary {
            color: blue;
        }

        #btn-outline-primary:hover {
            color: white;
            /* Change text color to white on hover */
        }
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">{{ $project->name }}</h2>
        </div>
        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('assign_users', $Account_Permissions ?? []))
            <div class="col-auto" style="padding-right: 0px;">
                <button type="button" data-toggle="modal" data-target="#assignUsersModal"
                    class="btn mb-2 btn-outline-warning"id="btn-outline-warning">Assign Users</button>
            </div>
        @endif
    </div>
    <div class="modal fade" id="assignUsersModal" tabindex="-1" role="dialog" aria-labelledby="assignUsersModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignUsersModalLabel">Assign Users To Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="sendInvitationForm"method="post" action="{{ route('assign_users') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="multi-select2">Select Users</label>
                            <select class="form-control select2-multi xxx" id="multi-select2" name="assigned_users[]"multiple>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if(in_array($user->id, $assigned_users)) selected @endif>{{ $user->name? $user->name . ' - ' . $user->email : $user->email }}</option>
                                @endforeach
                            </select>
                        </div> <!-- form-group -->

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit"form="sendInvitationForm" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
    @if (session('error'))
        <div id="errorAlert" class="alert alert-danger"
            style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:brown;border-radius: 20px; color:beige;">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div id="successAlert"
            class="alert alert-success"style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:green;border-radius: 20px; color:beige;">
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
