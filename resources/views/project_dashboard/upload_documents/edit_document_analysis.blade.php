@extends('project_dashboard.layout.app')
@section('title', 'Project Home - Edit Document Analysis')
@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .date {
            background-color: #fff !important;
        }
    </style>
    <h2 class="page-title">Document Analysis</h2>
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
                    <form id="DocumentAnalysis" method="post"
                        action="{{ route('project.update-document-analysis', $document->slug) }}"
                        enctype="multipart/form-data">
                        @csrf


                        <div class="row">
                            <!-- Type Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="Project">Project</label>
                                    <input type="text" id="Project" class="form-control"
                                        value="{{ $document->project->name }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="Type">Doc.Type</label>
                                    <input type="text" id="Type" class="form-control"
                                        value="{{ $document->docType->name }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="reference">Ref.No</label>
                                    <input type="text" id="reference" class="form-control"
                                        value="{{ $document->reference }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Type Input -->
                            <div class="col-md-9">
                                <div class="form-group mb-3">
                                    <label for="Subject">Subject</label>
                                    <input type="text" id="Subject" class="form-control"
                                        value="{{ $document->subject }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="Revision">Revision</label>
                                    <input type="text" id="Revision" class="form-control"
                                        value="{{ $document->revision }}" disabled>
                                </div>
                            </div>


                        </div>

                        <div class="row">
                            <!-- Type Input -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="Date">Date</label>
                                    <input required type="date" style="background-color:#fff;" id="Date"
                                        class="form-control date" placeholder="Start Date"
                                        value="{{ $document->start_date }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="end_date">Return Date</label>
                                    <input required type="date" style="background-color:#fff;" id="end_date"
                                        class="form-control date" placeholder="Return Date"
                                        value="{{ $document->end_date }}" disabled>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="Status">Status</label>
                                    <input type="text" id="Status" class="form-control"
                                        value="{{ $document->status }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="Note">Note</label>
                            <textarea disabled name="notes" rows="5" id="Note" class="form-control" placeholder="Note">{{ $document->notes }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="multi-select2">Thread</label>
                            <select class="form-control select2-multi" id="multi-select2" name="threads[]" multiple
                                disabled>
                                @foreach ($threads as $thread)
                                    <option
                                        value="{{ $thread }}"{{ $document->threads && in_array($thread, json_decode($document->threads, true)) ? 'selected' : '' }}>
                                        {{ $thread }}</option>
                                @endforeach
                                @if ($document->threads)
                                    @foreach (json_decode($document->threads, true) as $thread2)
                                        @if (!in_array($thread2, $threads->toArray()))
                                            <option value="{{ $thread2 }}"selected>
                                                {{ $thread2 }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div style="display: flex;">
                            <p style="margin-right: 40px;">Potential Impact : </p>

                            <div class="custom-control custom-checkbox"style="margin-right: 20px;">
                                <input type="checkbox" class="custom-control-input"
                                    name="time_prolongation_cost"id="time_prolongation_cost"
                                    {{ $analysis->time_prolongation_cost ? 'checked' : '' }}>
                                <label class="custom-control-label" for="time_prolongation_cost">Time & Prolongation
                                    Cost</label>
                            </div>
                            <div class="custom-control custom-checkbox"style="margin-right: 20px;">
                                <input type="checkbox" class="custom-control-input"
                                    name="disruption_cost"id="disruption_cost"
                                    {{ $analysis->disruption_cost ? 'checked' : '' }}>
                                <label class="custom-control-label" for="disruption_cost">Disruption</label>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="variation"id="variation"
                                    {{ $analysis->variation ? 'checked' : '' }}>
                                <label class="custom-control-label" for="variation">Variation</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="impacted_zone">Impacted Zone of the Project</label>
                            <div id="editor1" class="quill-editor"style="min-height:250px;">{!! $analysis->impacted_zone !!}
                            </div>
                            <input type="hidden" name="impacted_zone" id="impacted_zone">
                        </div>

                        <div class="form-group mb-3">
                            <label for="concerned_part">The Concerned Part(s) of the Document</label>
                            <div id="editor2" class="quill-editor"style="min-height:250px;">{!! $analysis->concerned_part !!}
                            </div>
                            <input type="hidden" name="concerned_part" id="concerned_part">
                        </div>
                        <div class="form-group mb-3">
                            <label for="why_need_analysis">Why this Document Needs Analysis and Uploading to "CMW"</label>
                            <div id="editor3" class="quill-editor"style="min-height:250px;">{!! $analysis->why_need_analysis !!}
                            </div>
                            <input type="hidden" name="why_need_analysis" id="why_need_analysis">
                        </div>
                        <div class="form-group mb-3">
                            <label for="affected_works">Details of the Affected Works</label>
                            <div id="editor4" class="quill-editor"style="min-height:250px;">{!! $analysis->affected_works !!}
                            </div>
                            <input type="hidden" name="affected_works" id="affected_works">
                        </div>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"id="analysis_complete"
                                name="analysis_complete"{{ $document->analysis_complete ? 'checked' : '' }}>
                            <label class="custom-control-label" for="analysis_complete">Analysis Complete</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"id="assess_not_pursue"
                                name="assess_not_pursue"{{ $document->assess_not_pursue ? 'checked' : '' }}>
                            <label class="custom-control-label" for="assess_not_pursue">Assessed Not To Pursue</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input"id="Assigned" disabled
                                {{ count($document->files) > 0 ? 'checked' : '' }}>
                            <label class="custom-control-label" for="Assigned">Assigned</label>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="owner">Document Owner </label>
                                    <select class="form-control" id="owner" required name="user_id">
                                        <option value="" disabled>please select</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ $document->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="analysis_date">Date of Analysis</label>
                                    <input required type="date" style="background-color:#fff;" name="analysis_date"
                                        id="analysis_date" class="form-control date" placeholder="Date Of Analysis"
                                        value="{{ $analysis->analysis_date }}">
                                </div>
                            </div>

                        </div>




                        <!-- Start Date Input -->


                        <input type="hidden" name="action" id="formAction" value="save">

                        <div class="text-right" style="margin-top: 10px;">
                            <button type="submit"
                                class="btn mb-2 btn-outline-success"onclick="document.getElementById('formAction').value='save'">Save</button>
                            <button type="button" class="btn mb-2 btn-outline-primary"
                                id="assigne-to-btn">Assign</button>
                            <button type="button" class="btn mb-2 btn-outline-secondary">Initiate a Notice</button>
                            <button type="button" class="btn mb-2 btn-outline-secondary">Close</button>
                            <button type="button" class="btn mb-2 btn-outline-secondary">Send e-mail & close</button>
                            <button type="button" class="btn mb-2 btn-outline-secondary">Print</button>
                        </div>
                    </form>
                </div> <!-- /.col -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="assigneToModal" tabindex="-1" role="dialog" aria-labelledby="assigneToModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assigneToModalLabel">Assign Document to File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="container">

                </div>
                <div class="modal-body">
                    <form id="assigneToForm">
                        @csrf
                        <input type="hidden" id="documentId_" name="document_id" value="{{ $document->id }}">
                        <div class="form-group">
                            <label for="folder_id">Select Folder</label>
                            <select class="form-control" id="folder_id" required>
                                <option value="" disabled selected>Select Folder</option>
                                @foreach ($folders as $key => $name)
                                    <option value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-none">
                            <label for="newFile">Select File</label>
                            <select class="form-control" id="newFile" name="file_id">
                                <option value="" disabled selected>Select File</option>

                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveAssigne">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="insertImageModal" tabindex="-1" role="dialog" aria-labelledby="insertImageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="insertImageModalLabel">Insert Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">




                    <div class="form-group mb-3">
                        <label for="customFile">Upload Image:</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input"accept="image/*" id="uploadImageInput">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Image URL:</label>
                        <input type="text" id="imageUrlInput" placeholder="Enter image URL"class="form-control">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Description:</label>
                        <input type="text" id="imageAltInput" placeholder="Describe the image"class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="insertImageBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.dropdown-toggle').dropdown();
            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#assigne-to-btn').on('click', function() {
                $('#folder_id').val('');
                let fileDropdown = $('#newFile');
                var type = 'document';
                var documentId = '{{ $document->slug }}';

                fileDropdown.closest('.form-group').addClass(
                    'd-none');
                fetch("{{ route('set.session') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            key: 'file_doc_type',
                            value: type
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data); // should log { status: 'ok' }
                    });
                $.ajax({
                    url: '/document/get-files/' +
                        documentId, // Adjust the route to your API endpoint
                    type: 'GET',
                    success: function(response) {
                        let container = $('#container');
                        container.empty()
                        $.each(response.files, function(index, file) {
                            container.append(
                                `<p><span class="fa fa-star"></span> <span style="font-size:1.2rem;">${file.folder.name}</span>  <span style="font-family: Helvetica, Arial, Sans-Serif; font-size: 26px;">&#x2192;</span>  <span style="font-size:1.2rem;">${file.name}</span></p>`
                            );
                        });
                    },
                    error: function() {
                        alert('Failed to fetch files. Please try again.');
                    }
                });
                $('#assigneToModal').modal('show'); // Show the modal
            });
            // Handle the form submission via AJAX

            $('#folder_id').change(function() {
                let folderId = $(this).val();

                if (!folderId) return; // Stop if no folder is selected

                $.ajax({
                    url: '/project/folder/get-files/' +
                        folderId, // Adjust the route to your API endpoint
                    type: 'GET',
                    success: function(response) {
                        let fileDropdown = $('#newFile');
                        fileDropdown.empty().append(
                            '<option value="" disabled selected>Select File</option>');

                        if (response.files.length > 0) {
                            $.each(response.files, function(index, file) {
                                fileDropdown.append(
                                    `<option value="${file.id}">${file.name}</option>`
                                );
                            });

                            fileDropdown.closest('.form-group').removeClass(
                                'd-none'); // Show file dropdown
                        } else {
                            fileDropdown.closest('.form-group').addClass(
                                'd-none'); // Hide if no files
                        }
                    },
                    error: function() {
                        alert('Failed to fetch files. Please try again.');
                    }
                });
            });

            $('#saveAssigne').click(function() {
                let documentId = $('#documentId_').val();
                let fileId = $('#newFile').val();

                if (!fileId) {
                    alert('Please select a file.');
                    return;
                }

                $.ajax({
                    url: '/project/document/assign-document', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(), // CSRF token
                        document_id: documentId,
                        file_id: fileId
                    },
                    success: function(response) {
                        alert(response.message); // Show success message
                        $('#assigneToModal').modal('hide'); // Hide modal
                        let assignedCheckbox = $('#Assigned');
                        if (!assignedCheckbox.prop('checked')) {
                            assignedCheckbox.prop('checked', true);
                        }
                    },
                    error: function() {
                        alert('Failed to assign document. Please try again.');
                    }
                });
            });
        });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr(".date", {
                enableTime: false,
                dateFormat: "Y-m-d", // Format: YYYY-MM-DD
                altInput: true,
                altFormat: "d.M.Y",
            });

        });
    </script>
    <script>
        const Size = Quill.import('attributors/style/size');
        Size.whitelist = ['10pt', '11pt', '12pt', '13pt', '14pt', '15pt', '16pt', '17pt', '18pt', '19pt', '20pt',
            '21pt', '22pt', '23pt', '24pt', '25pt', '32pt'
        ];
        Quill.register(Size, true);

        var toolbarOptions = [
            // [{
            //     'font': []
            // }],
            [{
                'header': [1, 2, 3, 4, 5, 6, false]
            }],
            [{
                'size': Size.whitelist
            }],
            ['bold', 'italic', 'underline', 'strike'],
            // ['blockquote', 'code-block'],
            // [{
            //         'header': 1
            //     },
            //     {
            //         'header': 2
            //     }
            // ],
            [{
                    'list': 'ordered'
                },
                {
                    'list': 'bullet'
                }
            ],
            // [{
            //         'script': 'sub'
            //     },
            //     {
            //         'script': 'super'
            //     }
            // ],
            [{
                    'indent': '-1'
                },
                {
                    'indent': '+1'
                }
            ], // outdent/indent
            [{
                'direction': 'rtl'
            }], // text direction
            [{
                    'color': []
                },
                {
                    'background': []
                }
            ], // dropdown with defaults from theme
            [{
                'align': []
            }],
            ['image'],
            ['clean'] // remove formatting button
        ];

        let editors = {};
        let activeEditor = null; // track current editor

        document.querySelectorAll('.quill-editor').forEach((el) => {
            let quill = new Quill('#' + el.id, {
                modules: {
                    toolbar: {
                        container: toolbarOptions,
                        handlers: {
                            image: function() {
                                activeEditor = quill; // set the clicked editor as active
                                $('#insertImageModal').modal('show');
                            }
                        }
                    },
                    imageResize: {
                        displayStyles: {
                            backgroundColor: 'black',
                            border: 'none',
                            color: 'white'
                        },
                        modules: ['Resize', 'DisplaySize', 'Toolbar']
                    }
                },
                theme: 'snow'
            });
            editors[el.id] = quill;
        });

        document.querySelector('.close').addEventListener('click', function() {
            $('#insertImageModal').modal('hide');
        });

        document.getElementById('insertImageBtn').addEventListener('click', async function() {
            if (!activeEditor) {
                alert('No active editor selected.');
                return;
            }

            let imageUrl = document.getElementById('imageUrlInput').value;
            let altText = document.getElementById('imageAltInput').value;
            let fileInput = document.getElementById('uploadImageInput');

            if (fileInput.files.length > 0) {
                let file = fileInput.files[0];
                let formData = new FormData();
                formData.append('image', file);

                try {
                    let response = await fetch('/project/upload-editor-image', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    let result = await response.json();
                    if (result.success) {
                        imageUrl = '/' + result.file.path;
                    } else {
                        alert('Image upload failed');
                        return;
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    return;
                }
            }

            if (imageUrl) {
                let range = activeEditor.getSelection();
                if (!range) {
                    range = {
                        index: activeEditor.getLength()
                    };
                }

                let imgTag = `<img src="${imageUrl}" alt="${altText}">`;
                activeEditor.clipboard.dangerouslyPasteHTML(range.index, imgTag);
            } else {
                alert('Please provide an image URL or upload a file.');
            }

            $('#insertImageModal').modal('hide');

            document.getElementById('imageUrlInput').value = '';
            document.getElementById('uploadImageInput').value = '';
            document.getElementById('imageAltInput').value = '';
        });

        document.querySelector('#DocumentAnalysis').addEventListener('submit', function() {
            Object.keys(editors).forEach((editorId) => {
                // match editor1 -> impacted_zone, editor2 -> concerned_part, etc.
                let hiddenInputId = editorId.replace('editor', '');

                let mapping = {
                    '1': 'impacted_zone',
                    '2': 'concerned_part',
                    '3': 'why_need_analysis',
                    '4': 'affected_works'
                };

                let hiddenInput = document.getElementById(mapping[hiddenInputId]);
                if (hiddenInput) {
                    hiddenInput.value = editors[editorId].root.innerHTML;
                }
            });
        });
    </script>
@endpush
