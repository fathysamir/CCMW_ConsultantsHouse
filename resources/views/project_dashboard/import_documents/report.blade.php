<h4 class="h4 mb-0 mb-0 page-title">• Unimported Rows</h4>
@foreach ($unImportedRows as $key => $row)
    <div class="col-md-12 mt-1" style="border-radius: 10px;">
        <div class="card shadow" style="border-radius: 10px;">
            <div class="card-header" style="background-color:rgb(255, 112, 112); border-radius: 10px;">
                <strong class="card-title">{{ $key }}</strong>
            </div>
            <div class="card-body">
                <p class="text-muted">Reasons for not importing this Row.</p>
                <ul>
                    @foreach ($row as $val)
                        <li>{{ $val }}</li>
                    @endforeach


                </ul>

            </div> <!-- /. card-body -->
        </div> <!-- /. card-body -->
    </div> <!-- /. col -->
@endforeach


<h4 class="h4 mb-0 page-title" style="margin-top: 20px;">• Imported Rows</h4>
@foreach ($importedRows as $key2 => $row2)
    <div class="col-md-12 mt-1" style="border-radius: 10px;">
        <div class="card shadow" style="border-radius: 10px;">
            <div class="card-header" style="background-color:rgb(150, 248, 144); border-radius: 10px;">
                <strong class="card-title">{{ $key2 }}</strong>
            </div>
            <div class="card-body">
                <p class="text-muted">Notes On importing this Row.</p>
                <ul>
                    @foreach ($row2 as $val2)
                        <li>{{ $val2 }}</li>
                    @endforeach

                </ul>

            </div> <!-- /. card-body -->
        </div> <!-- /. card-body -->
    </div> <!-- /. col -->
@endforeach
