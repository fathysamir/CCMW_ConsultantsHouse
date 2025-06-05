<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon"href="{{ asset('dashboard/assets/images/logo.png') }}">
    <title>CCMW - @yield('title', 'Admin Home')</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard/css/simplebar.css') }}">
    <!-- Fonts CSS -->
    <link
        href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard/css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/dropzone.css') }}'">
    <link rel="stylesheet" href="{{ asset('dashboard/css/uppy.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/jquery.steps.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/jquery.timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/quill.snow.css') }}">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard/css/daterangepicker.css') }}">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard/css/app-light.css') }}" id="lightTheme">
    <link rel="stylesheet" href="{{ asset('dashboard/css/app-dark.css') }}" id="darkTheme" disabled>
    <style>
        .sidebar-resize-handle {
            position: absolute;
            top: 0;
            right: -5px;
            width: 10px;
            height: 100%;
            cursor: ew-resize;
            background: transparent;
            z-index: 1000;
        }

        .sidebar-resize-handle:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        /* Prevent text selection while resizing */
        .resizing {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        /* Ensure sidebar has relative positioning */
        .sidebar-left {

            /* Minimum width */
            max-width: 800px !important;
            /* Maximum width */
            transition: none !important;
            /* Disable transitions while dragging */
        }

        /* Ensure content adjusts properly */
        .wrapper {

            overflow: hidden;
        }

        .main-content {

            min-width: 0;
            /* Prevents content from breaking layout */
        }

        .main-content {
            flex: 1;
            min-width: 0;
            /* Prevents content from breaking layout */
            transition: width 0.1s ease;
            /* Smooth transition for width changes */
        }

        .l-link:hover {
            color: #1b68ff !important;
        }

        @keyframes tilt-shaking {
            0% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(7deg);
            }

            50% {
                transform: rotate(0deg);
            }

            75% {
                transform: rotate(-7deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .vibrate-hover:hover {
            /* animation: vibrate-up-down-left-right 1s ease; */
            animation: tilt-shaking 0.1s;


        }

        .link_kkkkk:hover {
            background: #495057 !important;
        }
    </style>
    <style>
        body.collapsed.hover #logo {
            width: 130px !important;
            border: 6px solid #495057 !important;
        }

        body.collapsed:not(.hover) #logo {
            width: 50px !important;
            border: 3px solid #495057 !important;
        }
    </style>
    <style>
        /* تظهر القيمة في قائمة الحجم */
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value]::before {
            content: attr(data-value);
        }

        /* تظهر القيمة في الزر نفسه */
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value]::before {
            content: attr(data-value);
        }

        /* في حالة لا يوجد data-value (الحجم العادي الافتراضي) */
        .ql-snow .ql-picker.ql-size .ql-picker-item:not([data-value])::before,
        .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before {
            content: 'Normal';
        }
    </style>
</head>

<body class="vertical  light  @if ($sideBarTheme == '0') collapsed @endif">
    <div class="wrapper">
        @include('project_dashboard.layout.header')
        @include('project_dashboard.layout.side_menu')

        <main role="main" class="main-content" style="padding-top: 0px;">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12">
                        @yield('content')
                    </div> <!-- .col-12 -->
                </div> <!-- .row -->
            </div> <!-- .container-fluid -->

        </main> <!-- main -->
    </div> <!-- .wrapper -->
    <script src="{{ asset('dashboard/js/jquery.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/popper.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/moment.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/simplebar.min.js') }}"></script>
    <script src='{{ asset('dashboard/js/daterangepicker.js') }}'></script>
    <script src='{{ asset('dashboard/js/jquery.stickOnScroll.js') }}'></script>
    <script src="{{ asset('dashboard/js/tinycolor-min.js') }}"></script>
    <script src="{{ asset('dashboard/js/config.js') }}"></script>
    <script src="{{ asset('dashboard/js/d3.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/topojson.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/datamaps.all.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/datamaps-zoomto.js') }}"></script>
    <script src="{{ asset('dashboard/js/datamaps.custom.js') }}"></script>
    <script src="{{ asset('dashboard/js/Chart.min.js') }}"></script>
    <script>
        /* defind global options */
        Chart.defaults.global.defaultFontFamily = base.defaultFontFamily;
        Chart.defaults.global.defaultFontColor = colors.mutedColor;
    </script>
    <script src="{{ asset('dashboard/js/gauge.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/apexcharts.custom.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/select2.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/jquery.timepicker.js') }}"></script>
    <script src="{{ asset('dashboard/js/dropzone.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/uppy.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/quill.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>

    <script>
        $('.select2').select2({
            theme: 'bootstrap4',
        });
        $('.select2-multi').select2({
            multiple: true,
            theme: 'bootstrap4',
            tags: true
        });
        $('.xxx').select2({
            multiple: true,
            theme: 'bootstrap4',
            tags: false
        });
        $('.drgpicker').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            showDropdowns: true,
            locale: {
                format: 'MM/DD/YYYY'
            }
        });
        $('.time-input').timepicker({
            'scrollDefault': 'now',
            'zindex': '9999' /* fix modal open */
        });
        /** date range picker */
        if ($('.datetimes').length) {
            $('.datetimes').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });
        }
        var start = moment().subtract(29, 'days');
        var end = moment();

        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')]
            }
        }, cb);
        cb(start, end);
        $('.input-placeholder').mask("00/00/0000", {
            placeholder: "__/__/____"
        });
        $('.input-zip').mask('00000-000', {
            placeholder: "____-___"
        });
        $('.input-money').mask("#.##0,00", {
            reverse: true
        });
        $('.input-phoneus').mask('(000) 000-0000');
        $('.input-mixed').mask('AAA 000-S0S');
        $('.input-ip').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
            translation: {
                'Z': {
                    pattern: /[0-9]/,
                    optional: true
                }
            },
            placeholder: "___.___.___.___"
        });
        // editor
        var editor = document.getElementById('editor');
        if (editor) {
            const Size = Quill.import('attributors/style/size');
            Size.whitelist = ['10pt', '11pt', '12pt', '13pt', '14pt', '15pt', '16pt', '17pt','18pt', '19pt','20pt', '21pt','22pt','23pt','24pt','25pt', '32pt'];
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
            var quill = new Quill('#editor', {
                modules: {
                    toolbar: {
                        container: toolbarOptions,
                        handlers: {
                            image: function() {
                                // Show custom image modal
                                $('#insertImageModal').modal('show');
                            }
                        }
                    },
                    imageResize: { // Move imageResize outside of toolbar
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
            document.querySelector('.close').addEventListener('click', function() {

                $('#insertImageModal').modal('hide');
            });

            document.getElementById('insertImageBtn').addEventListener('click', async function() {
                var imageUrl = document.getElementById('imageUrlInput').value;
                var altText = document.getElementById('imageAltInput').value;
                var fileInput = document.getElementById('uploadImageInput');

                if (fileInput.files.length > 0) {
                    // If user selected an image file, upload it
                    var file = fileInput.files[0];
                    var formData = new FormData();
                    formData.append('image', file);

                    try {
                        let response = await fetch('/project/upload-editor-image', { // Laravel route
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
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

                // Insert Image into Quill Editor with Alt Text
                if (imageUrl) {
                    var range = quill.getSelection(); // Get cursor position

                    if (!range) {
                        range = {
                            index: quill.getLength()
                        }; // If null, insert at the end
                    }

                    var imgTag = `<img src="${imageUrl}" alt="${altText}">`;
                    quill.clipboard.dangerouslyPasteHTML(range.index, imgTag);
                } else {
                    alert('Please provide an image URL or upload a file.');
                }

                // Close modal
                $('#insertImageModal').modal('hide');

                // Clear inputs
                document.getElementById('imageUrlInput').value = '';
                document.getElementById('uploadImageInput').value = '';
                document.getElementById('imageAltInput').value = '';
            });
            // var quill = new Quill('#editor', {
            //     modules: {
            //         toolbar: {
            //             container: toolbarOptions,
            //             handlers: {
            //                 image: function() {
            //                     var input = document.createElement('input');
            //                     input.setAttribute('type', 'file');
            //                     input.setAttribute('accept', 'image/*');
            //                     input.click();

            //                     input.onchange = async function() {
            //                         var file = input.files[0];
            //                         if (file) {
            //                             var formData = new FormData();
            //                             formData.append('image', file);

            //                             try {
            //                                 let response = await fetch(
            //                                 '/project/upload-editor-image', { // <-- Change this URL to your backend route
            //                                     method: 'POST',
            //                                     body: formData,
            //                                     headers: {
            //                                         'X-CSRF-TOKEN': document.querySelector(
            //                                             'meta[name="csrf-token"]').content
            //                                     }
            //                                 });

            //                                 let result = await response.json();
            //                                 if (result.success) {
            //                                     var range = quill.getSelection();
            //                                     quill.insertEmbed(range.index, 'image', '/' + result
            //                                         .file.path);
            //                                 } else {
            //                                     alert('Image upload failed');
            //                                 }
            //                             } catch (error) {
            //                                 console.error('Upload error:', error);
            //                             }
            //                         }
            //                     };
            //                 }
            //             }
            //         }
            //     },
            //     theme: 'snow'
            // });

            document.querySelector('#formNarrative').addEventListener('submit', function() {
                document.querySelector('#narrative').value = quill.root.innerHTML;
            });
        }
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>

    <script src="{{ asset('dashboard/js/apps.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar-left');
            if (!sidebar) return;

            // Create and append resize handle
            const resizeHandle = document.createElement('div');
            resizeHandle.className = 'sidebar-resize-handle';
            sidebar.appendChild(resizeHandle);

            let isResizing = false;
            let lastDownX = 0;
            let originalWidth = 0;

            resizeHandle.addEventListener('mousedown', function(e) {
                isResizing = true;
                lastDownX = e.clientX;
                originalWidth = sidebar.offsetWidth;
                document.body.classList.add('resizing');

                // Disable any existing transitions temporarily
                sidebar.style.transition = 'none';
            });

            document.addEventListener('mousemove', function(e) {
                if (!isResizing) return;

                const delta = e.clientX - lastDownX;
                const newWidth = originalWidth + delta;

                // Apply min/max constraints
                if (newWidth <= 600) {
                    sidebar.style.width = newWidth + 'px';

                    // Ensure simplebar updates its height
                    const simplebar = sidebar.querySelector('[data-simplebar]');
                    if (simplebar && simplebar.SimpleBar) {
                        simplebar.SimpleBar.recalculate();
                    }
                }
            });

            document.addEventListener('mouseup', function() {
                if (!isResizing) return;

                isResizing = false;
                document.body.classList.remove('resizing');

                // Store the final width
                originalWidth = sidebar.offsetWidth;

                // Re-enable transitions
                sidebar.style.transition = '';
            });

            // Prevent text selection while dragging
            resizeHandle.addEventListener('dragstart', function(e) {
                e.preventDefault();
            });
        });
    </script>
    <script>
        function toggle2() {
            const icon = document.getElementById('modeIcon');
            const currentSrc = icon.getAttribute('src');

            // استخرج اسم الصورة فقط (moon.png أو sun.png)
            const fileName = currentSrc.split('/').pop();

            if (fileName === 'moon.png') {
                icon.setAttribute('src', '{{ asset('/dashboard/assets/selected_images/sun2.png') }}');
            } else {
                icon.setAttribute('src', '{{ asset('/dashboard/assets/selected_images/moon.png') }}');
            }
        }


        function toggleWidth() {
            const logo = document.getElementById('logo');
            const currentWidth = parseInt(logo.getAttribute('width'));

            if (currentWidth === 130) {
                logo.setAttribute('width', '50');
                logo.style.border = '3px solid #495057';
                $.ajax({
                    url: '/change-sideBarTheme', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        // CSRF token
                        sideBarTheme: '0',

                    },
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    success: function(response) {

                    },
                    error: function() {

                    }
                });
            } else {
                logo.setAttribute('width', '130'); // Increase width
                logo.style.border = '6px solid #495057';
                $.ajax({
                    url: '/change-sideBarTheme', // Adjust the route to your API endpoint
                    type: 'POST',
                    data: {
                        // _token: $('input[name="_token"]').val(), // CSRF token
                        sideBarTheme: '1',

                    },
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    success: function(response) {

                    },
                    error: function() {

                    }
                });
            }


        }
    </script>
    @stack('scripts')
    <!-- Global site tag (gtag.js) - Google Analytics -->
    {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag()
      {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', 'UA-56159088-1');
    </script> --}}
</body>

</html>
