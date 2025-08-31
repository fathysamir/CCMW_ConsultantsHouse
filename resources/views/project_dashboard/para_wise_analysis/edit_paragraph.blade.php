@extends('project_dashboard.layout.app')
@section('title', 'Admin Home - Edit Paragraph')
@section('content')
    <link rel="stylesheet" href="{{ asset('dashboard/css/dataTables.bootstrap4.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">


    <div class="row align-items-center my-4"
        style="margin-top: 0px !important; justify-content: center;margin-bottom: 0px !important;">
        <div class="col">
            <h4 class="h4 mb-0 page-title">
                <a href="{{ route('project.para-wise-analysis') }}">Para-wise Analysis</a>
                <span class="fe fe-24 fe-chevrons-right" style="position: relative; top: 3px;"></span>
                <a href="{{ route('project.para-wise-analysis.paragraphs', $para_wise->slug) }}">{{ $para_wise->title }}</a>
                <span class="fe fe-24 fe-chevrons-right" style="position: relative; top: 3px;"></span>
                Edit Paragraph
            </h4>
        </div>


        <div class="col-auto">
            {{-- Previous --}}
            @if ($previous)
                <label
                    onclick="window.location.href='{{ route('project.para-wise-analysis.edit_paragraph', $previous->slug) }}'"
                    style="cursor:pointer;margin-right:-15px;" title="Previous Paragraph">
                    <span class="fe fe-24 fe-navigation-2" style="font-size:30px;"></span>
                </label>
            @else
                <label style="color:#ccc;margin-right:-15px;" title="No Previous Paragraph">
                    <span class="fe fe-24 fe-navigation-2" style="font-size:30px;"></span>
                </label>
            @endif

            {{-- Next --}}
            @if ($next)
                <label
                    onclick="window.location.href='{{ route('project.para-wise-analysis.edit_paragraph', $next->slug) }}'"
                    style="cursor:pointer;" title="Next Paragraph">
                    <span class="fe fe-24 fe-navigation-2"
                        style="font-size:30px;transform: rotate(180deg);display:inline-block;"></span>
                </label>
            @else
                <label style="color:#ccc;" title="No Next Paragraph">
                    <span class="fe fe-24 fe-navigation-2"
                        style="font-size:30px;transform: rotate(180deg);display:inline-block;"></span>
                </label>
            @endif
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
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="update_paragraph" method="post"
                        action="{{ route('project.para-wise-analysis.update_paragraph', $paragraph->slug) }}"
                        enctype="multipart/form-data">
                        @csrf


                        <input hidden name="para_wise_slug" value="{{ $para_wise->slug }}">
                        <input hidden name="red_flag" id="red_flag_input" value="{{ $paragraph->red_flag }}">
                        <input hidden name="blue_flag" id="blue_flag_input" value="{{ $paragraph->blue_flag }}">
                        <input hidden name="green_flag" id="green_flag_input" value="{{ $paragraph->green_flag }}">

                        <div class="row">
                            <div class="col-md-3" style="line-height: unset;">
                                <div class="form-group "style="margin-bottom:0px;">
                                    <label for="number">Para.No. <span style="color: red">*</span></label>
                                    <input type="number" name="number" required id="number" class="form-control"
                                        value="{{ old('number', $paragraph->number) }}" step="0.001">
                                </div>
                            </div>
                            <div class="col-md-1" style="line-height: unset;padding-left: 0px;">
                                <div class="form-group "style="margin-bottom:0px;">
                                    <label></label>
                                    <div style="display: flex">
                                        <label class="flag-toggle" data-input="blue_flag_input" data-color="#0000ff"
                                            title="Blue Flag"
                                            style="margin-right:1rem;cursor:pointer;font-size:30px;margin-top:7px;">
                                            <i class="{{ $paragraph->blue_flag ? 'fa-solid' : 'fa-regular' }} fa-flag"
                                                style="color: {{ $paragraph->blue_flag ? '#0000ff' : '' }}"></i>
                                        </label>
                                        <label class="flag-toggle" data-input="red_flag_input" data-color="#ff0000"
                                            title="Red Flag"
                                            style="margin-right:1rem;cursor:pointer;font-size:30px;margin-top:7px;">
                                            <i class="{{ $paragraph->red_flag ? 'fa-solid' : 'fa-regular' }} fa-flag"
                                                style="color: {{ $paragraph->red_flag ? '#ff0000' : '' }}"></i>
                                        </label>
                                        <label class="flag-toggle" data-input="green_flag_input" data-color="#00ff00"
                                            title="Green Flag" style="cursor:pointer;font-size:30px;margin-top:7px;">
                                            <i class="{{ $paragraph->green_flag ? 'fa-solid' : 'fa-regular' }} fa-flag"
                                                style="color: {{ $paragraph->green_flag ? '#00ff00' : '' }}"></i>
                                        </label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5" style="line-height: unset;">
                                <div class="form-group"style="margin-bottom:0px;">
                                    <label for="title_above">Add Title Above</label>

                                    <textarea name="title_above" style="height: 60px;"id="title_above" class="form-control" placeholder="">{{ old('title_above', $paragraph->title_above) }}</textarea>

                                </div>
                            </div>
                            <div class="col-md-3" style="line-height: unset;">
                                <div class="form-group" style="margin-bottom:0px;">
                                    <label for="background_ref">Background Ref.</label>

                                    <textarea name="background_ref"style="height: 60px;" id="background_ref" class="form-control" placeholder="">{{ old('background_ref', $paragraph->background_ref) }}</textarea>

                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="background" style="margin-bottom: 0px;">Background</label>
                            <div id="editor1" class="quill-editor" style="min-height:250px;">{!! $paragraph->background !!}
                            </div>
                            <input type="hidden" name="background" id="background">
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group mb-3">
                                    <label for="paragraph">Paragraph</label>
                                    <div id="editor2" class="quill-editor" style="min-height:250px;">
                                        {!! $paragraph->paragraph !!}</div>
                                    <input type="hidden" name="paragraph" id="paragraph">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="multi-select2_1">Exhibits</label>
                                    <select class="form-control xxxx" id="multi-select2_1" name="para_exhibits[]"
                                        multiple>
                                        @foreach ($docs as $doc)
                                            <option value="{{ $doc->id }}"
                                                data-docslug="{{ $doc->storageFile->path }}"
                                                {{ in_array($doc->id, array_map('intval', explode(',', $paragraph->para_exhibits))) ? 'selected' : '' }}>
                                                {{ $doc->reference }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div id="exhibit-links" style="margin-top:10px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group mb-3">
                                    <label for="reply">Reply</label>
                                    <div id="editor3" class="quill-editor" style="min-height:250px;">
                                        {!! $paragraph->reply !!}</div>
                                    <input type="hidden" name="reply" id="reply">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="multi-select2_2">Exhibits</label>
                                    <select class="form-control xxxxx" id="multi-select2_2" name="reply_exhibits[]"
                                        multiple>
                                        @foreach ($docs as $doc)
                                            <option value="{{ $doc->id }}"
                                                data-docslug="{{ $doc->storageFile->path }}"
                                                {{ in_array($doc->id, array_map('intval', explode(',', $paragraph->reply_exhibits))) ? 'selected' : '' }}>
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
                            <select class="form-control xxx" id="multi-select2_3" name="para_numbers[]" multiple>
                                @foreach ($paragraphs as $para)
                                    <option value="{{ $para->id }}"
                                        {{ in_array($para->id, array_map('intval', explode(',', $paragraph->para_numbers))) ? 'selected' : '' }}>
                                        {{ $para->number }}
                                    </option>
                                @endforeach
                            </select>
                            <p>Fill this box only if the reply is for more than one paras</p>
                        </div>

                        <div class="form-group mb-3">
                            <label for="Note">Note</label>
                            <textarea name="notes" rows="5" id="Note" class="form-control">{{ old('notes', $paragraph->notes) }}</textarea>
                        </div>
                        <input type="hidden" name="action" id="formAction" value="save">
                        <button type="submit" class="btn mb-2 btn-outline-primary"
                            onclick="document.getElementById('formAction').value='save'">Save</button>
                        <button type="submit" class="btn mb-2 btn-outline-primary"
                            onclick="document.getElementById('formAction').value='update'">Update</button>
                        <button type="button" class="btn mb-2 btn-outline-danger"id="btn-outline-primary"
                            onclick="window.location.href='{{ route('project.para-wise-analysis.paragraphs', $para_wise->slug) }}'">Close</button>
                    </form>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



    <script>
        $(document).ready(function() {
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

        document.querySelector('#update_paragraph').addEventListener('submit', function() {
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
    <script>
        function renderLinks(selectId, linksDivId) {
            let selectedOptions = document.querySelectorAll(`#${selectId} option:checked`);
            let linksDiv = document.getElementById(linksDivId);
            linksDiv.innerHTML = ''; // clear old links

            selectedOptions.forEach(opt => {
                let slug = opt.dataset.docslug;
                let ref = opt.textContent.trim();
                let url = `/${slug}`; // هنا حط الروت المناسب عندك

                let a = document.createElement('a');
                a.href = url;
                a.target = '_blank';
                a.textContent = ref;
                a.style.display = 'block';
                linksDiv.appendChild(a);
            });
        }




        // شغّلها مرة عند تحميل الصفحة علشان تعرض اللينكات المختارة بالفعل
        renderLinks('multi-select2_1', 'exhibit-links');
        renderLinks('multi-select2_2', 'exhibit-links2');
    </script>
@endpush
