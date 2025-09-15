@extends('project_dashboard.layout.app')
@section('title', 'Admin Home - Create Paragraph')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .ql-toolbar .ql-footnote {
            width: auto !important;
            /* ياخد العرض المناسب للنص */
            padding: 0 0 0 4px;
            /* مسافة جوه الزرار */
            font-size: 14px;
            /* حجم الخط */
            font-weight: bold;
            color: #6c757d;
            ;
        }

        .ql-toolbar .ql-footnote::before {
            content: "Footnote";
        }
    </style>
    <h4 class="h4 mb-0 page-title"><a href="{{ route('project.para-wise-analysis') }}">Para-wise Analysis</a><span
            class="fe fe-24 fe-chevrons-right"style="position: relative; top: 3px;"></span><a
            href="{{ route('project.para-wise-analysis.paragraphs', $para_wise->slug) }}">{{ $para_wise->title }}</a><span
            class="fe fe-24 fe-chevrons-right"style="position: relative; top: 3px;"></span>Create New
        Paragraph
    </h4>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">

                <div class="col-md-12">
                    <form id="create_paragraph"method="post"
                        action="{{ route('project.para-wise-analysis.stor_paragraph') }}" enctype="multipart/form-data">
                        @csrf
                        <input hidden name="para_wise_slug" value="{{ $para_wise->slug }}">
                        <input hidden name="red_flag" id="red_flag_input" value="0">
                        <input hidden name="blue_flag" id="blue_flag_input" value="0">
                        <input hidden name="green_flag" id="green_flag_input" value="0">
                        <div class="row">
                            <!-- Type Input -->
                            <div class="col-md-3" style="line-height: unset;">
                                <div class="form-group "style="margin-bottom:0px;">
                                    <label for="number">Para.No. <span style="color: red">*</span></label>
                                    <input type="number" name="number" required id="number" class="form-control"
                                        placeholder="" value="{{ old('number') }}" step="0.001">
                                </div>
                                @if ($errors->has('number'))
                                    <p class="text-error more-info-err" style="color: red;margin-top: -13px;">
                                        {{ $errors->first('number') }}</p>
                                @endif
                            </div>
                            <div class="col-md-1" style="line-height: unset;padding-left: 0px;">
                                <div class="form-group "style="margin-bottom:0px;">
                                    <label></label>
                                    <div style="display: flex">
                                        <label class="flag-toggle" data-input="blue_flag_input" data-color="#0000ff"
                                            style="margin-right:1rem;cursor: pointer;margin-bottom:0px;margin-top: 7px;font-size: 30px;"
                                            title="Blue Flag">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
                                        <label class="flag-toggle" data-input="red_flag_input" data-color="#ff0000"
                                            style="margin-right:1rem;cursor: pointer;margin-bottom:0px;margin-top: 7px;font-size: 30px;"
                                            title="Red Flag">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
                                        <label class="flag-toggle" data-input="green_flag_input" data-color="#00ff00"
                                            style="cursor: pointer;margin-bottom:0px;margin-top: 7px;font-size: 30px;"
                                            title="Green Flag">
                                            <i class="fa-regular fa-flag"></i>
                                        </label>
                                    </div>

                                </div>

                            </div>
                            <div class="col-md-5" style="line-height: unset;">
                                <div class="form-group"style="margin-bottom:0px;">
                                    <label for="title_above">Add Title Above</label>
                                    <textarea name="title_above" style="height: 60px;" id="title_above" class="form-control" placeholder="">{{ old('title_above') }}</textarea>

                                </div>
                            </div>
                            <div class="col-md-3" style="line-height: unset;">
                                <div class="form-group" style="margin-bottom:0px;">
                                    <label for="background_ref">Background Ref.</label>

                                    <textarea name="background_ref" style="height: 60px;"id="background_ref" class="form-control" placeholder="">{{ old('background_ref') }}</textarea>

                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="background"style="margin-bottom: 0px;">Background</label>
                            <div id="editor1" class="quill-editor"style="min-height:250px;">
                            </div>
                            <input type="hidden" name="background" id="background">
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group mb-3">
                                    <label for="paragraph">Paragraph</label>
                                    <div id="editor2" class="quill-editor"style="min-height:250px;">
                                    </div>
                                    <input type="hidden" name="paragraph" id="paragraph">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="multi-select2_1">Exhibits</label>
                                    <select class="form-control xxxx" id="multi-select2_1" name="para_exhibits[]"
                                        multiple>
                                        @foreach ($docs as $key => $doc)
                                            <option value="{{ $doc->id }}"
                                                data-docslug="{{ $doc->storageFile->path }}">
                                                {{ $doc->reference }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- هنا هنظهر اللينكات -->
                                    <div id="exhibit-links" style="margin-top:10px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group mb-3">
                                    <label for="reply">Reply</label>
                                    <div id="editor3" class="quill-editor"style="min-height:250px;">
                                    </div>
                                    <input type="hidden" name="reply" id="reply">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="multi-select2_2">Exhibits</label>
                                    <select class="form-control xxxxx" id="multi-select2_2"
                                        name="reply_exhibits[]"multiple>
                                        @foreach ($docs as $key => $doc)
                                            <option value="{{ $doc->id }}"
                                                data-docslug="{{ $doc->storageFile->path }}">
                                                {{ $doc->reference }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="exhibit-links2" style="margin-top:10px;"></div>

                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="multi-select2_3">Reply to Paras Nos.</label>
                            <select class="form-control xxx" id="multi-select2_3" name="para_numbers[]"multiple>
                                @foreach ($paragraphs as $paragraph)
                                    <option value="{{ $paragraph->id }}">{{ $paragraph->number }}</option>
                                @endforeach
                            </select>
                            <p>Fill this box only if the replay is for more than one paras</p>
                        </div>
                        <div class="form-group mb-3">
                            <label for="Note">Note</label>
                            <textarea name="notes" rows="5" id="Note" class="form-control" placeholder="Note">{{ old('notes') }}</textarea>
                        </div>
                        <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary"
                            style="margin-top: 10px;">Create</button>
                        <button type="button" class="btn mb-2 btn-outline-danger" style="margin-top: 10px;"
                            onclick="window.location.href='{{ route('project.para-wise-analysis.paragraphs', $para_wise->slug) }}'">Close</button>
                    </form>
                </div>


            </div> <!-- /.col -->

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
    <div class="modal fade" id="insertRefModal" tabindex="-1" role="dialog" aria-labelledby="insertRefModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Insert Reference</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select class="form-control" id="refInput">
                        <option value="" disabled selected> -- Select Document -- </option>

                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="insertRefBtn">Insert</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.dropdown-toggle').dropdown();
            $(".flag-toggle").on("click", function() {
                var inputId = $(this).data("input");
                var color = $(this).data("color");
                var input = $("#" + inputId);
                var icon = $(this).find("i");

                // toggle value (0 → 1 → 0)
                if (input.val() == "0") {
                    input.val("1");
                    icon.removeClass("fa-regular").addClass("fa-solid").css("color", color);
                } else {
                    input.val("0");
                    icon.removeClass("fa-solid").addClass("fa-regular").css("color", "");
                }
            });
            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
            //////////////////////////////////////////////////////////////////////
        });
    </script>
    <script>
        const Size = Quill.import('attributors/style/size');
        Size.whitelist = ['10pt', '11pt', '12pt', '13pt', '14pt', '15pt', '16pt', '17pt', '18pt', '19pt', '20pt',
            '21pt', '22pt', '23pt', '24pt', '25pt', '32pt'
        ];
        Quill.register(Size, true);

        var commonToolbar = [
            [{
                'header': [1, 2, 3, 4, 5, 6, false]
            }],
            [{
                'size': Size.whitelist
            }],
            ['bold', 'italic', 'underline', 'strike'],
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }],
            [{
                'direction': 'rtl'
            }],
            [{
                'color': []
            }, {
                'background': []
            }],
            [{
                'align': []
            }],
            ['image'],
            ['clean']
        ];
        var replyToolbar = JSON.parse(JSON.stringify(commonToolbar));
        replyToolbar[replyToolbar.length - 2].push('footnote');
        var icons = Quill.import('ui/icons');
        icons['footnote'] = '';
        let editors = {};
        let activeEditor = null; // track current editor

        function initQuill(editorId, toolbarOptions, handlers = {}) {
            let el = document.getElementById(editorId);
            if (!el) return;

            let quill = new Quill('#' + editorId, {
                modules: {
                    toolbar: {
                        container: toolbarOptions,
                        handlers: handlers
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

            editors[editorId] = quill;
            return quill;
        }

        // Background editor (editor1)
        initQuill('editor1', commonToolbar, {
            image: function() {
                activeEditor = editors['editor1'];
                $('#insertImageModal').modal('show');
            }
        });

        // Paragraph editor (editor2)
        initQuill('editor2', commonToolbar, {
            image: function() {
                activeEditor = editors['editor2'];
                $('#insertImageModal').modal('show');
            }
        });

        // Reply editor (editor3) مع footnote
        initQuill('editor3', replyToolbar, {
            image: function() {
                activeEditor = editors['editor3'];
                $('#insertImageModal').modal('show');
            },
            footnote: function() {
                activeEditor = editors['editor3'];
                $('#insertRefModal').modal('show');
            }
        });

        $('#insertRefModal').on('show.bs.modal', function() {
            let selectedOptions = $('#multi-select2_2 option:selected'); // كل option متعلم عليه
            let refInput = $('#refInput');

            // فضّي الـ options القديمة
            refInput.empty();

            // ضيف option default
            refInput.append('<option value="" disabled selected> -- Select Document -- </option>');

            // ضيف الجديد
            selectedOptions.each(function() {
                let val = $(this).val();
                let text = $(this).text();


                refInput.append(
                    `<option value="${val}">${text}</option>`
                );
            });
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
        document.getElementById('insertRefBtn').addEventListener('click', function() {
            let refInput = document.getElementById('refInput');
            let selectedOption = refInput.options[refInput.selectedIndex]; // الـ option المختار

            if (selectedOption && activeEditor) {
                let refId = selectedOption.value; // id
                let refText = selectedOption.text; // النص اللي ظاهر في القائمة


                let insertText = `[***${refText}***]`; // تقدر تضيف refSlug أو id كمان لو عايز

                let range = activeEditor.getSelection();
                if (range) {
                    // قبل النص (عادي)
                    activeEditor.insertText(range.index, ",", 'bold', false);

                    // النص نفسه (Bold)
                    activeEditor.insertText(range.index + 1, insertText, 'bold', true);

                    // بعد النص (عادي)
                    activeEditor.insertText(range.index + 1 + insertText.length, ",", 'bold', false);

                } else {
                    const pos = activeEditor.getLength() - 1;

                    activeEditor.insertText(pos, ",", 'bold', false);
                    activeEditor.insertText(pos + 1, insertText, 'bold', true);
                    activeEditor.insertText(pos + 1 + insertText.length, ",", 'bold', false);
                }

                $('#insertRefModal').modal('hide');
                refInput.value = '';
            }
        });
        document.querySelector('#create_paragraph').addEventListener('submit', function() {
            Object.keys(editors).forEach((editorId) => {
                // match editor1 -> impacted_zone, editor2 -> concerned_part, etc.
                let hiddenInputId = editorId.replace('editor', '');

                let mapping = {
                    '1': 'background',
                    '2': 'paragraph',
                    '3': 'reply',
                };

                let hiddenInput = document.getElementById(mapping[hiddenInputId]);
                if (hiddenInput) {
                    hiddenInput.value = editors[editorId].root.innerHTML;
                }
            });
        });
    </script>
@endpush
