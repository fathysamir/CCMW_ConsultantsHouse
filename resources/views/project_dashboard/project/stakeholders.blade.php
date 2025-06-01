@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Stakeholders')
@section('content')
    <h2 class="page-title">Project Stakeholders</h2>
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
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('projects.update_stakeholders', $project->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <fieldset class="border p-3 mb-3">
                            <legend style="width: 190px;margin-bottom: 0px;">Key Stakeholders</legend>
                            <div class="d-flex justify-content-between align-items-center" style="margin-top: -25px;">
                                <legend class="w-auto px-2 text-primary mb-0"></legend>
                                <button type="button" class="btn btn-primary btn-sm"id="addStakeholder">Add
                                    Stakeholder</button>
                            </div>
                            <div class="row">
                                <!-- Name Input -->
                                <div class="col-md-3"><label>Role</label></div>
                                <div class="col-md-3"><label>Name</label></div>
                                <div class="col-md-3"><label>For Chronology</label></div>
                                <div class="col-md-2"><label>Article</label></div>
                                <div class="col-md-1"></div>
                            </div>
                            <div id="stakeholdersContainer">
                                @foreach ($project->stakeHolders as $key => $stakeholder)
                                    <div class="row stakeholder-row">
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <select name="old_stakeholders[{{ $stakeholder->id }}][role]" required
                                                    class="form-control">
                                                    <option value='' disabled>Select Role</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role }}"
                                                            @if ($stakeholder->role == $role) selected @endif>
                                                            {{ $role }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <input type="text" name="old_stakeholders[{{ $stakeholder->id }}][name]"
                                                    class="form-control" placeholder="Name"
                                                    value="{{ $stakeholder->name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <input type="text"
                                                    name="old_stakeholders[{{ $stakeholder->id }}][chronology]"
                                                    class="form-control"
                                                    placeholder="Chronology"value="{{ $stakeholder->narrative }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group mb-3">
                                                <select name="old_stakeholders[{{ $stakeholder->id }}][article]"
                                                    class="form-control">
                                                    <option value=""
                                                        @if ($stakeholder->article == null) selected @endif></option>
                                                    <option value="the"
                                                        @if ($stakeholder->article == 'the') selected @endif>The</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group mb-3">
                                                <div class="p-1 remove-stakeholder"><span
                                                        class="fe fe-24 fe-minus-circle"></span></div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                        </fieldset>
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

        let stakeholderCounter = $(".stakeholder-row").length;;
        let milestoneCounter = $(".milestone-row").length;;
        $("#addStakeholder").click(function() {
            let newRow = `
                    <div class="row stakeholder-row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <select name="stakeholders[${stakeholderCounter}][role]"required class="form-control">
                                    <option value='' selected disabled>Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <input type="text" name="stakeholders[${stakeholderCounter}][name]" class="form-control" placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <input type="text" name="stakeholders[${stakeholderCounter}][chronology]" class="form-control" placeholder="Chronology">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <select name="stakeholders[${stakeholderCounter}][article]" class="form-control">
                                    <option value=""></option>
                                    <option value="the">The</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mb-3">
                                <div class="p-1 remove-stakeholder"><span class="fe fe-24 fe-minus-circle"></span></div>
                            </div>
                        </div>
                    </div>
                `;
            $("#stakeholdersContainer").append(newRow);
            stakeholderCounter++;
        });

        $(document).on("click", ".remove-stakeholder", function() {
            $(this).closest(".stakeholder-row").remove();
        });
    </script>
@endpush
