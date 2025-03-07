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
            color:#1b68ff !important;
        }
    </style>
</head>

<body class="vertical  light  ">
    <div class="wrapper">
        @include('project_dashboard.layout.header')
        @include('project_dashboard.layout.side_menu')

        <main role="main" class="main-content" style="padding-top: 0px;" >
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
    <script>
        $('.select2').select2({
            theme: 'bootstrap4',
        });
        $('.select2-multi').select2({
            multiple: true,
            theme: 'bootstrap4',
            tags: true
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
            var toolbarOptions = [
                [{
                    'font': []
                }],
                [{
                    'header': [1, 2, 3, 4, 5, 6, false]
                }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{
                        'header': 1
                    },
                    {
                        'header': 2
                    }
                ],
                [{
                        'list': 'ordered'
                    },
                    {
                        'list': 'bullet'
                    }
                ],
                [{
                        'script': 'sub'
                    },
                    {
                        'script': 'super'
                    }
                ],
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
                ['clean'] // remove formatting button
            ];
            var quill = new Quill(editor, {
                modules: {
                    toolbar: toolbarOptions
                },
                theme: 'snow'
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
    <script>
        var uptarg = document.getElementById('drag-drop-area');
        if (uptarg) {
            var uppy = Uppy.Core().use(Uppy.Dashboard, {
                inline: true,
                target: uptarg,
                proudlyDisplayPoweredByUppy: false,
                theme: 'dark',
                width: 770,
                height: 210,
                plugins: ['Webcam']
            }).use(Uppy.Tus, {
                endpoint: 'https://master.tus.io/files/'
            });
            uppy.on('complete', (result) => {
                console.log('Upload complete! We’ve uploaded these files:', result.successful)
            });
        }
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
        function toggleWidth() {
            const logo = document.getElementById('logo');
            const currentWidth = parseInt(logo.getAttribute('width'));

            if (currentWidth === 100) {
                logo.setAttribute('width', '50');
            //     const tableHeader = document.querySelector('.table-container thead');
            //     const tablebody = document.querySelector('.table-container tbody');
            // if (tableHeader) {
            //     tableHeader.style.width = '100%';
            //     tablebody.style.width = '100%'; // Set the header width to 100%
            // } // Decrease width
            } else {
            //     const tableHeader = document.querySelector('.table-container thead');
            //     const tablebody = document.querySelector('.table-container tbody');
            // if (tableHeader) {
            //     tableHeader.style.width = ''; // Set the header width to 100%
            //     tablebody.style.width = ''; 
            // } // 
                logo.setAttribute('width', '100'); // Increase width
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
