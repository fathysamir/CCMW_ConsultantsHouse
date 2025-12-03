@extends('dashboard.layout.app')
@section('title', 'Admin Home - Edit Window Narrative Setting')
@section('content')
    <style>
        .date {
            background-color: #fff !important;
        }

        #editor1427 .ql-editor {
            max-height: 80px;
            overflow-y: auto;
        }

        #editor1428 .ql-editor {
            max-height: 80px;
            overflow-y: auto;
        }

        .ql-snow .ql-picker.ql-size .ql-picker-label::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item::before {
            content: attr(data-value) !important;
            text-transform: none !important;
        }

        /* ensure toolbar function button is positioned relative (so its dropdown is absolute inside it) */
        .ql-toolbar .ql-func,
        .ql-toolbar .ql-default {
            width: auto !important;
            padding: 0 6px;
            font-size: 14px;
            font-weight: bold;
            color: #6c757d;
            position: relative;
        }

        /* Function label */
        .ql-toolbar .ql-func::before {
            content: "Function";
        }

        /* Set Default label */
        .ql-toolbar .ql-default::before {
            content: "Set-Default";
        }


        /* dropdown now positioned relative to button */
        .func-dropdown {
            position: absolute;
            top: 100%;
            /* immediately below the button, like other pickers */
            right: 0;
            /* align to the right edge of the button */
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: none;
            z-index: 9999;
            min-width: 140px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .func-dropdown div {
            padding: 8px 12px;
            cursor: pointer;
            white-space: nowrap;
        }

        .func-dropdown div:hover {
            background: #f3f3f3;
        }

        .sub-dropdown {
            position: absolute;
            top: 0;
            left: 100%;
            /* opens on the right */
            margin-left: 5px;
            min-width: 140px;
            display: none;
            background: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            z-index: 10000;
        }
    </style>
    <h2 class="page-title">Update Window Narrative Description</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form id="ggg" method="post"
                        action="{{ route('accounts.window-narrative-settings.update', $setting->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6" style="padding-right:0px !important;">
                                <div class="form-group mb-3">
                                    <label for="para_id">Para ID</label>
                                    <input type="text" disabled id="para_id" class="form-control" placeholder="Para ID"
                                        value="{{ $setting->para_id }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="location">Location</label>
                                    <input type="text" disabled id="location" class="form-control"
                                        placeholder="Location" value="{{ $setting->location }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="Scenario_Description">Scenario Description</label>
                            <input type="text" disabled id="Scenario_Description" class="form-control"
                                placeholder="Scenario Description" value="{{ $setting->description }}">
                        </div>

                        <div class="form-group mb-3">
                            <label for="editor1427">Paragraph</label>

                            <div style="position: relative;">
                                <div id="editor1427" class="quill-editor" style="min-height:80px;">
                                    {!! $setting->paragraph !!}
                                </div>

                                <!-- dropdown الخاص بال Functions -->
                                <div id="funcDropdown" class="func-dropdown"></div>
                            </div>

                            <input type="hidden" name="paragraph" id="paragraph">
                        </div>
                        <div class="form-group mb-3">
                            <label for="editor1428">Default</label>
                            <div id="editor1428" class="quill-editor" style="min-height:80px;">
                                {!! $setting->paragraph_default !!}</div>
                        </div>
                        <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary"
                            style="margin-top: 10px;">Save</button>
                    </form>

                    <div style="background-color: #f27a7a;padding: 5px 10px;border-radius: 0.25rem;">
                        <p style="color: #fff"><label><b>fnWNo() :</b> This function return Window No.</label></P>
                        <p style="color: #fff"><label><b>fnPrevWNo() :</b> This function return Previous Window No.</label>
                        </p>
                        <p style="color: #fff"><label><b>fnDrivAct() :</b> This function return .</label>
                        </p>
                        <p style="color: #fff"><label><b>fnListOfDEs() :</b> This function return .</label>
                        </p>
                        <p style="color: #fff;margin-bottom: 0px;"><label style="margin-bottom: 0px;"><b>fnCompDate() :</b>
                                This function return last date of program.</label></p>
                    </div>
                </div> <!-- /.col -->

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // register sizes once
        const Size = Quill.import('attributors/style/size');
        Size.whitelist = ['10pt', '11pt', '12pt', '13pt', '14pt', '15pt', '16pt', '17pt', '18pt', '19pt', '20pt', '21pt',
            '22pt', '23pt', '24pt', '25pt', '32pt'
        ];
        Quill.register(Size, true);

        var toolbarOptions = [
            [{
                'size': Size.whitelist
            }], // size first
            [{
                'header': [1, 2, 3, 4, 5, 6, false]
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
            ['clean']
        ];

        // ---------- FIRST editor (with Function) ----------
        var editorDiv1 = document.getElementById('editor1427');
        if (editorDiv1) {

            const FUNCTIONS_LIST = {
                "fnWNo()": null,
                "fnPrevWNo()": null,
                "fnCompDate": ["BAS", "IMP", "UPD", "BUT"],
                "fnDrivAct": ["BAS", "IMP", "UPD", "BUT"],
                "fnListOfDEs()": null,
                "fnCulpable()":null,
                "fnExcusable()":null,
                "fnCompensable()":null,
                "fnCompensableTransfer":['WNo','PrevWNo']
            };

            const dropdown = document.getElementById('funcDropdown');

            // create submenu element (but don't let innerHTML overwrite it)
            const subDropdown = document.createElement('div');
            subDropdown.className = "sub-dropdown";
            subDropdown.style.display = "none";
            // keep subDropdown out of innerHTML manipulations: we'll append later

            // build main items as elements (more robust than innerHTML)
            dropdown.innerHTML = ''; // clear any existing content
            Object.keys(FUNCTIONS_LIST).forEach(fn => {
                const div = document.createElement('div');
                div.className = 'main-fn';
                div.setAttribute('data-func', fn);
                div.textContent = fn;
                dropdown.appendChild(div);
            });

            // append submenu container after main items
            dropdown.appendChild(subDropdown);

            var toolbarForEditor1 = JSON.parse(JSON.stringify(toolbarOptions));
            toolbarForEditor1[toolbarForEditor1.length - 1].push('func');
            toolbarForEditor1[toolbarForEditor1.length - 1].push('default');

            var icons = Quill.import('ui/icons');
            icons['func'] = '';
            icons['default'] = '';

            var secondQuill = new Quill('#editor1428', {});
            window.secondQuill = secondQuill;

            var quill1 = new Quill('#editor1427', {
                modules: {
                    toolbar: {
                        container: toolbarForEditor1,
                        handlers: {
                            func: function() {
                                const toolbarEl = editorDiv1.previousElementSibling;
                                const btn = toolbarEl ? toolbarEl.querySelector('.ql-func') : null;

                                if (!btn) return;

                                if (!btn.contains(dropdown)) {
                                    btn.appendChild(dropdown);
                                }

                                // toggle main dropdown
                                dropdown.style.display =
                                    dropdown.style.display === 'block' ? 'none' : 'block';

                                // hide submenu when opening main menu
                                subDropdown.style.display = "none";
                            },
                            default: function() {
                                const firstEditor = quill1;
                                const secondEditor = window.secondQuill;
                                if (!secondEditor) return;

                                const secondContent = secondEditor.root.innerHTML;
                                firstEditor.root.innerHTML = secondContent;
                            }
                        }
                    }
                },
                theme: 'snow'
            });

            // stop clicks inside dropdown from bubbling to document (which closes menus)
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();

                // main function clicked?
                const main = e.target.closest('.main-fn');
                if (main) {
                    const fn = main.dataset.func;
                    const params = FUNCTIONS_LIST[fn];

                    // no parameters → insert directly
                    if (!params) {
                        insertToEditor(fn);
                        dropdown.style.display = "none";
                        subDropdown.style.display = "none";
                        return;
                    }

                    // has parameters → populate submenu and show it
                    subDropdown.innerHTML = ''; // clear old items
                    params.forEach(p => {
                        const si = document.createElement('div');
                        si.className = 'sub-item';
                        si.setAttribute('data-parent', fn);
                        si.setAttribute('data-param', p);
                        si.textContent = p;
                        subDropdown.appendChild(si);
                    });

                    // show submenu (toggle)
                    subDropdown.style.display = (subDropdown.style.display === 'block') ? 'none' : 'block';
                    return;
                }

                // submenu item clicked?
                const sub = e.target.closest('.sub-item');
                if (sub) {
                    const parent = sub.dataset.parent;
                    const param = sub.dataset.param;
                    if (parent && param) {
                        const finalFn = `${parent}(${param})`;
                        insertToEditor(finalFn);
                    }

                    dropdown.style.display = "none";
                    subDropdown.style.display = "none";
                    return;
                }
            });

            function insertToEditor(text) {
                quill1.focus();
                const range = quill1.getSelection(true);
                const index = (range && typeof range.index === 'number') ? range.index : quill1.getLength();

                quill1.insertText(index, text, {
                    bold: true
                });
                quill1.setSelection(index + text.length, 0);
                quill1.format('bold', false);
            }

            // click outside closes all menus (document-level)
            document.addEventListener('click', function(e) {
                const toolbarEl = editorDiv1.previousElementSibling;
                const insideButton = toolbarEl && toolbarEl.querySelector('.ql-func') &&
                    toolbarEl.querySelector('.ql-func').contains(e.target);

                if (!insideButton && !dropdown.contains(e.target)) {
                    dropdown.style.display = "none";
                    subDropdown.style.display = "none";
                }
            });

            document.querySelector('#ggg').addEventListener('submit', function() {
                document.querySelector('#paragraph').value = quill1.root.innerHTML;
            });

        }




        // ---------- SECOND editor (NO Function button) ----------
        var editorDiv2 = document.getElementById('editor1428');
        if (editorDiv2) {
            // use the base toolbarOptions (no 'func' added)
            var quill2 = new Quill('#editor1428', {
                modules: {
                    toolbar: {
                        container: toolbarOptions
                    }
                },
                theme: 'snow'
            });
            quill2.enable(false);
            // same size label fix for second editor
            setTimeout(() => {
                document.querySelectorAll('#editor1428 + .ql-toolbar .ql-size .ql-picker-item').forEach(item => {
                    const v = item.getAttribute('data-value');
                    if (v) item.setAttribute('data-value', v);
                });
                document.querySelectorAll('#editor1428 + .ql-toolbar .ql-size .ql-picker-label').forEach(label => {
                    const v = label.getAttribute('data-value');
                    if (v) label.setAttribute('data-value', v);
                });
            }, 200);
        }
    </script>
    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('#errorAlert').fadeOut();
                $('#successAlert').fadeOut();
            }, 4000); // 4 seconds
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
@endpush
