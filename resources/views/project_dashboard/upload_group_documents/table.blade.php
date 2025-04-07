<div class="row my-4">
    <!-- Small table -->
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-body">
                <!-- Table container with fixed height -->
                <div class="table-container">

                    <!-- Table -->
                    <table class="table datatables" id="dataTable-1">

                        <thead>
                            <tr>
                                <th id="check"class="">
                                    No.
                                </th>

                                <th><b>Type</b></th>
                                <th><b>Reference</b></th>
                                <th><b>Subject</b></th>
                                <th><b>Date</b></th>
                                <th><b>From</b></th>
                                <th><b>To</b></th>
                                <th><b>Analyzed By</b></th>


                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $counter = 1;
                            @endphp
                            @foreach ($all_documents as $document)
                                <tr data-id="{{ $document->id }}" ondblclick="window.open('{{ route('group-documents.view_doc', $document->id) }}', '_blank')" style="cursor: pointer;@if($document->confirmed=='0')   background-color: #ff9999 !important;  @endif">
                                    <td>
                                        {{ $counter++ }}
                                    </td>

                                    <td>{{ $document->doc_type_id ? $document->docType->name : '_' }}</td>
                                    <td>{{ $document->reference ?? '_' }}</td>
                                    <td>{{ $document->subject ?? '_' }}</td>
                                    <td>{{ $document->start_date ? date('d-M-Y', strtotime($document->start_date)) : '_' }}
                                    </td>
                                    <td>{{ $document->fromStakeHolder ? $document->fromStakeHolder->narrative : '_' }}
                                    </td>
                                    <td>{{ $document->toStakeHolder ? $document->toStakeHolder->narrative : '_' }}</td>
                                    <td>{{ $document->user_id ? $document->user->name : '_' }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#check").removeClass("sorting_asc");
        const parentDiv = document.getElementById('dataTable-1_wrapper');

        if (parentDiv) {
            const rowDiv = parentDiv.querySelector('.row');

            if (rowDiv) {
                const colDivs = rowDiv.querySelectorAll('.col-md-6');

                if (colDivs.length > 0) {
                    colDivs[0].classList.remove('col-md-6');
                    colDivs[0].classList.add('col-md-2');
                }
            }
            any
        }
    });
</script>
<script src="{{ asset('dashboard/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('dashboard/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $('#dataTable-1').DataTable({
        autoWidth: true,
        responsive: true,
        "lengthMenu": [
            [16, 32, 64, -1],
            [16, 32, 64, "All"]
        ],
        "columnDefs": [{
            "targets": 0, // Target the first column (index 0)
            "orderable": false // Disable sorting for this column
        },{
            "targets": 7, // Target the first column (index 0)
            "orderable": false // Disable sorting for this column
        }]
    });
</script>
