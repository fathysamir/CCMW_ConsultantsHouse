@extends('account_dashboard.layout.app')
@section('title', 'Admin Account Home')
@section('content')
    <style>
        #btn-outline-primary {
            color: rgb(22, 89, 233);
        }

        #btn-outline-primary:hover {
            color: white;
            /* Change text color to white on hover */
        }

        #btn-outline-warning {
            color: rgb(226, 163, 91);
        }

        #btn-outline-warning:hover {
            color: white;
            /* Change text color to white on hover */
        }
    </style>

    <div class="row align-items-center my-4" style="margin-top: 0px !important; justify-content: center;">
        <div class="col">
            <h2 class="h3 mb-0 page-title">{{ $account->name }}</h2>
        </div>
        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('send_invitations', $Account_Permissions ?? []))
            <div class="col-auto" style="padding-right: 0px;">
                <button type="button" data-toggle="modal" data-target="#sendInvitationModal"
                    class="btn mb-2 btn-outline-warning"id="btn-outline-warning">Send Invitation</button>
            </div>
        @endif
        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('create_projects', $Account_Permissions ?? []))
            <div class="col-auto">
                <a type="button" href="{{ route('account.create_project_view') }}"
                    class="btn mb-2 btn-outline-primary"id="btn-outline-primary">Create Project</a>
            </div>
        @endif
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
        <a href="{{ route('account.projects') }}"class="col-md-3" style="text-decoration: none;">
            <div class="col-md-12">
                <div class="card shadow mb-4"style="border-radius:15px;height: 80%;">
                    <div
                        class="card-body text-center"style="border-radius:15px;box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);">
                        <div class="card-text my-2">
                            <strong class="card-title my-0">Number Of Projects</strong>
                            <h3 class=" text-muted mb-0">{{ $project_count }}</h3>
                        </div>
                    </div> <!-- ./card-text -->

                </div>
            </div>
        </a>
        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_users', $Account_Permissions ?? []))
        <a href="{{ route('account.users') }}"class="col-md-3" style="text-decoration: none;">
            <div class="col-md-12">
                <div class="card shadow mb-4"style="border-radius:15px;height: 80%;">
                    <div
                        class="card-body text-center"style="border-radius:15px;box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);">
                        <div class="card-text my-2">
                            <strong class="card-title my-0">Number Of Users</strong>
                            <h3 class=" text-muted mb-0">{{ $account->users->count() }}</h3>
                        </div>
                    </div> <!-- ./card-text -->

                </div>
            </div>
        </a>
        @endif
        @if (auth()->user()->roles->first()->name == 'Super Admin' || in_array('show_eps', $Account_Permissions ?? []))
            <a href="{{ route('account.EPS') }}"class="col-md-3" style="text-decoration: none;">
                <div class="col-md-12">
                    <div class="card shadow mb-4"style="border-radius:15px;height: 80%;">
                        <div
                            class="card-body text-center"style="border-radius:15px;box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.3);justify-content: center;">
                            <div class="card-text my-2"style="margin:1.4rem 0rem 1.4rem 0rem !important;">
                                <strong class="card-title my-0">EPS</strong>
                            </div>
                        </div> <!-- ./card-text -->

                    </div>
                </div>
            </a>
        @endif
        <div class="col-md-9">
        </div> <!-- .col -->
    </div>
    <div class="modal fade" id="sendInvitationModal" tabindex="-1" role="dialog"
        aria-labelledby="sendInvitationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendInvitationModalLabel">Send Invitation To Users</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="sendInvitationForm"method="post" action="{{ route('send_invitation') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="col-auto"style="text-align: center;">
                            <button type="button" class="btn mb-2 btn-outline-success" id="add_email">Add Email</button>
                        </div>
                        <div class="row form-group pr-2  pl-2">
                            <input type="email" class="form-control" style="width: 95%;" required name="emails[]">
                            <button type="button" class="close ml-2">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit"form="sendInvitationForm" class="btn btn-primary">Send</button>
                </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addEmailBtn = document.getElementById('add_email');
            const form = document.getElementById('sendInvitationForm');

            addEmailBtn.addEventListener('click', function() {
                const emailGroup = document.createElement('div');
                emailGroup.className = 'row form-group pr-2 pl-2';

                emailGroup.innerHTML = `
                  <input type="email" class="form-control" style="width: 95%;" required name="emails[]">
                  <button type="button" class="close ml-2" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              `;

                // Add event listener to the close button
                emailGroup.querySelector('.close').addEventListener('click', function() {
                    emailGroup.remove();
                });

                form.insertBefore(emailGroup, addEmailBtn.closest('.col-auto').nextSibling);
            });

            // Attach remove functionality to existing close button
            document.querySelectorAll('#sendInvitationForm .close').forEach(closeBtn => {
                closeBtn.addEventListener('click', function() {
                    closeBtn.closest('.form-group').remove();
                });
            });
        });
    </script>
@endpush
