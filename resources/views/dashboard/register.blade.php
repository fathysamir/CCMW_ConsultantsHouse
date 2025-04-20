<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="author" content="">
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
    </style>
</head>

<body class="light">
    <div class="wrapper vh-100">
        <div id="background-div" class="row align-items-center h-100"
            style="background-image: url('{{ asset('dashboard/assets/images/bg3.jpg') }}'); background-size: cover; background-repeat: no-repeat; background-position: center;">
            <form class="col-lg-3 col-md-4 col-10 mx-auto text-center"action="{{ route('sign-up') }}" method="POST"
                style="background-color: rgba(128, 128, 128, 0.7); border-radius: 0.4rem">
                @csrf
                <div class="form-group mt-3">
                    <img src="{{ asset('dashboard/assets/images/logo.png') }}" style="border-radius: 12px;width:100%;">
                </div>
                @if ($errors->any())
                    @if ($errors->has('msg'))
                        <p class="alert alert-danger"id="alert" role="alert"
                            style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:brown;border-radius: 20px; color:beige;">
                            {{ $errors->first('msg') }}</p>
                    @endif
                    @foreach ($errors->all() as $error)
                    <p class="alert alert-danger"id="alert" role="alert"
                            style="padding-top:5px;padding-bottom:5px; padding-left: 10px; background-color:brown;border-radius: 20px; color:beige;">
                            {{ $error }}</p>
                        
                    @endforeach
                @endif
                <div class="form-group mt-3">
                    <label for="inputName" class="sr-only">Name</label>
                    <input type="text" id="inputEmail" class="form-control form-control-lg" name="name"
                        placeholder="Name" required autofocus="">
                    @if ($errors->has('name'))
                        <p class="text-error more-info-err" style="color: red;">
                            {{ $errors->first('name') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Email address</label>
                    <input type="email" id="inputEmail" class="form-control form-control-lg" name="email"
                        placeholder="Email address" value="{{ $email }}" required autofocus="">
                    @if ($errors->has('email'))
                        <p class="text-error more-info-err" style="color: red;">
                            {{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label for="inputPhone" class="sr-only">Phone Number</label>
                    <div class="input-group">
                        <div class="input-group-prepend " style="width: 24%;margin-right:1%;">
                            <select name="country_code" class="form-control form-control-lg">
                                <option value="+1">USA (+1)</option>
                                <option value="+44">UK (+44)</option>
                                <option value="+971">UAE (+971)</option>
                                <option value="+91">India (+91)</option>
                                <option value="+61">Australia (+61)</option>
                                <option value="+93">Afghanistan (+93)</option>
                                <option value="+355">Albania (+355)</option>
                                <option value="+213">Algeria (+213)</option>
                                <option value="+376">Andorra (+376)</option>
                                <option value="+244">Angola (+244)</option>
                                <option value="+54">Argentina (+54)</option>
                                <option value="+374">Armenia (+374)</option>
                                <option value="+61">Australia (+61)</option>
                                <option value="+43">Austria (+43)</option>
                                <option value="+994">Azerbaijan (+994)</option>
                                <option value="+973">Bahrain (+973)</option>
                                <option value="+880">Bangladesh (+880)</option>
                                <option value="+375">Belarus (+375)</option>
                                <option value="+32">Belgium (+32)</option>
                                <option value="+501">Belize (+501)</option>
                                <option value="+229">Benin (+229)</option>
                                <option value="+975">Bhutan (+975)</option>
                                <option value="+591">Bolivia (+591)</option>
                                <option value="+387">Bosnia and Herzegovina (+387)</option>
                                <option value="+267">Botswana (+267)</option>
                                <option value="+55">Brazil (+55)</option>
                                <option value="+673">Brunei (+673)</option>
                                <option value="+359">Bulgaria (+359)</option>
                                <option value="+226">Burkina Faso (+226)</option>
                                <option value="+257">Burundi (+257)</option>
                                <option value="+855">Cambodia (+855)</option>
                                <option value="+237">Cameroon (+237)</option>
                                <option value="+1">Canada (+1)</option>
                                <option value="+238">Cape Verde (+238)</option>
                                <option value="+236">Central African Republic (+236)</option>
                                <option value="+235">Chad (+235)</option>
                                <option value="+56">Chile (+56)</option>
                                <option value="+86">China (+86)</option>
                                <option value="+57">Colombia (+57)</option>
                                <option value="+269">Comoros (+269)</option>
                                <option value="+243">Congo (+243)</option>
                                <option value="+682">Cook Islands (+682)</option>
                                <option value="+506">Costa Rica (+506)</option>
                                <option value="+385">Croatia (+385)</option>
                                <option value="+53">Cuba (+53)</option>
                                <option value="+357">Cyprus (+357)</option>
                                <option value="+420">Czech Republic (+420)</option>
                                <option value="+45">Denmark (+45)</option>
                                <option value="+253">Djibouti (+253)</option>
                                <option value="+593">Ecuador (+593)</option>
                                <option value="+20">Egypt (+20)</option>
                                <option value="+503">El Salvador (+503)</option>
                                <option value="+240">Equatorial Guinea (+240)</option>
                                <option value="+291">Eritrea (+291)</option>
                                <option value="+372">Estonia (+372)</option>
                                <option value="+251">Ethiopia (+251)</option>
                                <option value="+679">Fiji (+679)</option>
                                <option value="+358">Finland (+358)</option>
                                <option value="+33">France (+33)</option>
                                <option value="+241">Gabon (+241)</option>
                                <option value="+220">Gambia (+220)</option>
                                <option value="+995">Georgia (+995)</option>
                                <option value="+49">Germany (+49)</option>
                                <option value="+233">Ghana (+233)</option>
                                <option value="+30">Greece (+30)</option>
                                <option value="+502">Guatemala (+502)</option>
                                <option value="+224">Guinea (+224)</option>
                                <option value="+592">Guyana (+592)</option>
                                <option value="+509">Haiti (+509)</option>
                                <option value="+504">Honduras (+504)</option>
                                <option value="+36">Hungary (+36)</option>
                                <option value="+354">Iceland (+354)</option>
                                <option value="+62">Indonesia (+62)</option>
                                <option value="+98">Iran (+98)</option>
                                <option value="+964">Iraq (+964)</option>
                                <option value="+353">Ireland (+353)</option>
                                <option value="+39">Italy (+39)</option>
                                <option value="+81">Japan (+81)</option>
                                <option value="+962">Jordan (+962)</option>
                                <option value="+7">Kazakhstan (+7)</option>
                                <option value="+254">Kenya (+254)</option>
                                <option value="+965">Kuwait (+965)</option>
                                <option value="+996">Kyrgyzstan (+996)</option>
                                <option value="+856">Laos (+856)</option>
                                <option value="+371">Latvia (+371)</option>
                                <option value="+961">Lebanon (+961)</option>
                                <option value="+266">Lesotho (+266)</option>
                                <option value="+231">Liberia (+231)</option>
                                <option value="+370">Lithuania (+370)</option>
                                <option value="+352">Luxembourg (+352)</option>
                                <option value="+389">Macedonia (+389)</option>
                                <option value="+261">Madagascar (+261)</option>
                                <option value="+265">Malawi (+265)</option>
                                <option value="+60">Malaysia (+60)</option>
                                <option value="+960">Maldives (+960)</option>
                                <option value="+223">Mali (+223)</option>
                                <option value="+356">Malta (+356)</option>
                                <option value="+692">Marshall Islands (+692)</option>
                                <option value="+222">Mauritania (+222)</option>
                                <option value="+230">Mauritius (+230)</option>
                                <option value="+52">Mexico (+52)</option>
                                <option value="+373">Moldova (+373)</option>
                                <option value="+377">Monaco (+377)</option>
                                <option value="+976">Mongolia (+976)</option>
                                <option value="+382">Montenegro (+382)</option>
                                <option value="+212">Morocco (+212)</option>
                                <option value="+258">Mozambique (+258)</option>
                                <option value="+95">Myanmar (+95)</option>
                                <option value="+264">Namibia (+264)</option>
                                <option value="+977">Nepal (+977)</option>
                                <option value="+31">Netherlands (+31)</option>
                                <option value="+64">New Zealand (+64)</option>
                                <option value="+505">Nicaragua (+505)</option>
                                <option value="+227">Niger (+227)</option>
                                <option value="+234">Nigeria (+234)</option>
                                <option value="+47">Norway (+47)</option>
                                <option value="+968">Oman (+968)</option>
                                <option value="+92">Pakistan (+92)</option>
                                <option value="+507">Panama (+507)</option>
                                <option value="+675">Papua New Guinea (+675)</option>
                                <option value="+595">Paraguay (+595)</option>
                                <option value="+51">Peru (+51)</option>
                                <option value="+63">Philippines (+63)</option>
                                <option value="+48">Poland (+48)</option>
                                <option value="+351">Portugal (+351)</option>
                                <option value="+974">Qatar (+974)</option>
                                <option value="+40">Romania (+40)</option>
                                <option value="+7">Russia (+7)</option>
                                <option value="+250">Rwanda (+250)</option>
                                <option value="+966">Saudi Arabia (+966)</option>
                                <option value="+221">Senegal (+221)</option>
                                <option value="+381">Serbia (+381)</option>
                                <option value="+248">Seychelles (+248)</option>
                                <option value="+232">Sierra Leone (+232)</option>
                                <option value="+65">Singapore (+65)</option>
                                <option value="+421">Slovakia (+421)</option>
                                <option value="+386">Slovenia (+386)</option>
                                <option value="+27">South Africa (+27)</option>
                                <option value="+82">South Korea (+82)</option>
                                <option value="+34">Spain (+34)</option>
                                <option value="+94">Sri Lanka (+94)</option>
                                <option value="+249">Sudan (+249)</option>
                                <option value="+597">Suriname (+597)</option>
                                <option value="+268">Swaziland (+268)</option>
                                <option value="+46">Sweden (+46)</option>
                                <option value="+41">Switzerland (+41)</option>
                                <option value="+963">Syria (+963)</option>
                                <option value="+886">Taiwan (+886)</option>
                                <option value="+992">Tajikistan (+992)</option>
                                <option value="+255">Tanzania (+255)</option>
                                <option value="+66">Thailand (+66)</option>
                                <option value="+228">Togo (+228)</option>
                                <option value="+676">Tonga (+676)</option>
                                <option value="+216">Tunisia (+216)</option>
                                <option value="+90">Turkey (+90)</option>
                                <option value="+993">Turkmenistan (+993)</option>
                                <option value="+256">Uganda (+256)</option>
                                <option value="+380">Ukraine (+380)</option>
                                <option value="+971"selected>United Arab Emirates (+971)</option>
                                <option value="+44">United Kingdom (+44)</option>
                                <option value="+1">United States (+1)</option>
                                <option value="+598">Uruguay (+598)</option>
                                <option value="+998">Uzbekistan (+998)</option>
                                <option value="+58">Venezuela (+58)</option>
                                <option value="+84">Vietnam (+84)</option>
                                <option value="+967">Yemen (+967)</option>
                                <option value="+260">Zambia (+260)</option>
                                <option value="+263">Zimbabwe (+263)</option>
                            </select>
                            <!-- Add more country codes as needed -->

                        </div>
                        <input type="number"id="inputPhone" name="phone" class="form-control form-control-lg"
                            placeholder="Phone Number" value="{{ old('phone') }}"
                            style="border-top-left-radius: 0.3rem;border-bottom-left-radius: 0.3rem;">
                    </div>
                    @if ($errors->has('phone'))
                        <p class="text-error more-info-err" style="color: red;">{{ $errors->first('phone') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="sr-only">Password</label>
                    <input pattern="[A-Za-z0-9]{50}" title="Password must be exactly 50 digits" type="password"
                        id="inputPassword" name="password" class="form-control form-control-lg"
                        placeholder="Password" required>
                    @if ($errors->has('password'))
                        <p class="text-error more-info-err" style="color: red;">
                            {{ $errors->first('password') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label for="inputConfirmPassword" class="sr-only">Confirm Password</label>
                    <input pattern="[A-Za-z0-9]{50}" type="password" id="inputConfirmPassword"
                        name="password_confirmation" class="form-control form-control-lg"
                        placeholder="Confirm Password" required>
                    @if ($errors->has('password_confirmation'))
                        <p class="text-error more-info-err" style="color: red;">
                            {{ $errors->first('password_confirmation') }}</p>
                    @endif
                </div>

                <button class="btn btn-lg btn-primary btn-block mb-3" type="submit">Sign Up</button>

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
