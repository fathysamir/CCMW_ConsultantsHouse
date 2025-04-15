<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon"href="{{ asset('dashboard/assets/images/logo.png') }}">
    <title>CCMW - Login</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard/css/simplebar.css') }}">
    <!-- Fonts CSS -->
    <link
        href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="cccss/feather.css')}}">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard/css/daterangepicker.css') }}">
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('dashboard/css/app-light.css') }}" id="lightTheme">
    <link rel="stylesheet" href="{{ asset('dashboard/css/app-dark.css') }}" id="darkTheme" disabled>
    <style>
        body {
            height: 100vh;
            /* تحديد ارتفاع الصفحة بنسبة لحجم الشاشة */
            overflow: hidden;
            /* منع التمرير */
        }

        .fade-effect {
            transition: opacity 0.5s ease-in-out;
        }

        #forgot_password {
            color: #ffffff
        }

        #forgot_password:hover {
            color: #1900fd
        }
    </style>
</head>

<body class="light">
    <div class="wrapper vh-100">
        <div id="background-div" class="row align-items-center h-100"
            style="background-image: url('{{ asset('dashboard/assets/images/bg3.jpg') }}'); background-size: cover; background-repeat: no-repeat; background-position: center;">
            <form class="col-lg-3 col-md-4 col-10 mx-auto text-center"action="{{ route('login') }}"
                method="POST"style="background-color: rgba(128, 128, 128, 0.8); border-radius: 0.4rem">
                @csrf

                
                <div class="form-group mt-3">
                    <img src="{{ asset('dashboard/assets/images/logo.png') }}" style="border-radius: 12px;width:100%;">
                </div>

                {{-- <h1 class="h6 mb-3" style="color:aliceblue;font-size:200%">CCMW Sign in</h1> --}}
                @if ($errors->any())
                    @if ($errors->has('msg'))
                        <p class="alert alert-danger"id="alert" role="alert"
                            style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:brown;border-radius: 20px; color:beige;">
                            {{ $errors->first('msg') }}</p>
                    @endif
                @endif
                <div class="form-group mt-3">
                    <label for="inputEmail" class="sr-only">Email address</label>
                    <input type="email" id="inputEmail" class="form-control form-control-lg" name="email"
                        placeholder="Email address" required autofocus="">
                    @if ($errors->has('email'))
                        <p class="text-error more-info-err" style="color: red;">
                            {{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="sr-only">Password</label>
                    <input type="password" id="inputPassword" name="password" class="form-control form-control-lg"
                        placeholder="Password" required>
                    @if ($errors->has('password'))
                        <p class="text-error more-info-err" style="color: red;">
                            {{ $errors->first('password') }}</p>
                    @endif
                </div>
                <div class="row ml-1 mr-1 d-flex justify-content-between">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="remember"id="customCheck1">
                        <label class="custom-control-label" for="customCheck1" style="color: #fff">Remember me</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="last_project"id="customCheck2">
                        <label class="custom-control-label" for="customCheck2" style="color: #fff">Login to last
                            project</label>
                    </div>

                </div>
                <a id="forgot_password"href="#">Forgot Password</a>

                <button class="btn btn-lg btn-primary btn-block mb-3" type="submit">Login</button>

            </form>
        </div>
    </div>
    <script src="{{ asset('dashboard/js/jquery.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/popper.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/moment.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/simplebar.min.js') }}"></script>
    <script src='{{ asset('dashboard/>js/daterangepicker.js') }}'></script>
    <script src='{{ asset('dashboard/js/jquery.stickOnScroll.js') }}'></script>
    <script src="{{ asset('dashboard/js/tinycolor-min.js') }}"></script>
    <script src="{{ asset('dashboard/js/config.js') }}"></script>
    <script src="{{ asset('dashboard/js/apps.js') }}"></script>
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
    <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(function() {
                $('#alert').fadeOut('fast');
            }, 5000);
        });
    </script>
    <script>
        // Array of background image URLs
        const backgroundImages = [
            "{{ asset('dashboard/assets/images/1.jpg') }}",
            "{{ asset('dashboard/assets/images/2.webp') }}",
            "{{ asset('dashboard/assets/images/3.jpg') }}",
            "{{ asset('dashboard/assets/images/4.jpg') }}",
            "{{ asset('dashboard/assets/images/bg3.jpg') }}",
            "{{ asset('dashboard/assets/images/bg1.jpg') }}",
            "{{ asset('dashboard/assets/images/bg2.jpg') }}",
            "{{ asset('dashboard/assets/images/bg4.jpg') }}",
            "{{ asset('dashboard/assets/images/bg5.jpg') }}",
        ];

        let currentIndex = 0;

        function changeBackground() {
            const backgroundDiv = document.getElementById('background-div');
            const screenWidth = window.innerWidth;
            const screenHeight = window.innerHeight;

            // Add fade out effect
            backgroundDiv.style.opacity = '0';

            // Wait for fade out to complete before changing image
            setTimeout(() => {
                backgroundDiv.style.backgroundImage = `url('${backgroundImages[currentIndex]}')`;
                backgroundDiv.style.backgroundSize = `${screenWidth}px ${screenHeight}px`;

                // Fade in the new image
                backgroundDiv.style.opacity = '1';

                currentIndex = (currentIndex + 1) % backgroundImages.length;
            }, 500);
        }

        // Make sure to add the CSS class to your background div
        document.getElementById('background-div').classList.add('fade-effect');

        // Change the background every 15 seconds
        setInterval(changeBackground, 8000);
    </script>

</body>

</html>
</body>

</html>
