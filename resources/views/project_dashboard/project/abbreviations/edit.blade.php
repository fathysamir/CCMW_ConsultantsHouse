@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Edit Abbreviation')
@section('content')
    <h2 class="page-title">Update Abbreviation</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('project.update_abbreviation', $abbreviation->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="simpleinputName">Abbreviation</label>
                            <input type="text" name="abb" required id="simpleinputName" class="form-control"
                                placeholder="Abbreviation" value="{{ $abbreviation->abb }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-description">Description</label>
                            <textarea id="example-email" rows="5" name="description" class="form-control" placeholder="Description">{{ $abbreviation->description }}</textarea>
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
