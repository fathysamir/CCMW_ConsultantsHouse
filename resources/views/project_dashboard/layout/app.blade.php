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
    <style>
        .profile-container2 {
            text-align: center;
        }

        .profile-pic2 {
            width: 100%;

            height: 180px;

            margin-bottom: 20px;
        }

        .profile-pic2 img {
            width: 180px;
            height: 100%;

            border-radius: 50%;

            border: 3px solid #ddd;
        }

        .btn2 {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-change2 {
            background: #e5f4ff;
            color: #007bff;
        }

        .btn-change2:hover {
            background: #d0ebff;
        }

        .btn-remove2 {
            background: #ffeaea;
            color: #d9534f;
        }

        .btn-remove2:hover {
            background: #ffcccc;
        }

        .fileProfileImage {
            display: none;
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
                        <div class="modal fade" id="report1Modal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Contractual report</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="report1Form">
                                            @csrf
                                            <div class="form-group">
                                                <label for="cont_tag">Contractual Tags</label>
                                                <select class="form-control"id="cont_tag" name="cont_tag">
                                                    @foreach ($contract_tags as $tag)
                                                        <option value="{{ $tag->id }}">{{ $tag->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            </div>
                                            @php
                                                $project = \App\Models\Project::findOrFail(
                                                    auth()->user()->current_project_id,
                                                );
                                                $stake_holders = $project->stakeHolders;
                                            @endphp
                                            <div class="form-group">
                                                <label for="sender">Sender</label>

                                                <select class="form-control"id="sender" name="sender">
                                                    @foreach ($stake_holders as $stake_holder)
                                                        <option value="{{ $stake_holder->id }}">
                                                            {{ $stake_holder->narrative }} -
                                                            {{ $stake_holder->role }}</option>
                                                    @endforeach

                                                </select>

                                            </div>
                                            <div class="form-group">
                                                <label for="receiver">Receiver</label>
                                                <select class="form-control"id="receiver" name="receiver">
                                                    @foreach ($stake_holders as $stake_holder)
                                                        <option value="{{ $stake_holder->id }}">
                                                            {{ $stake_holder->narrative }} -
                                                            {{ $stake_holder->role }}</option>
                                                    @endforeach

                                                </select>

                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="option1" name="show_data" value="option1"
                                                    class="custom-control-input" checked>
                                                <label class="custom-control-label" for="option1">Show in New
                                                    Window</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="option2" name="show_data"
                                                    class="custom-control-input" value="option2">
                                                <label class="custom-control-label" for="option2">Export to
                                                    Excel</label>
                                            </div>

                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="report1">Show</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="userProfileInfoModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Profile Information</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="userProfileInfoForm">
                                            @csrf
                                            <div class="form-group">
                                                <div class="profile-container2">
                                                    <div class="profile-pic2" id="profilePic">
                                                        <img @if (getFirstMediaUrl(auth()->user(), auth()->user()->avatarCollection) != null) src="{{ getFirstMediaUrl(auth()->user(), auth()->user()->avatarCollection, true) }}" @else src="{{ asset('dashboard/assets/avatars/user_avatar.png') }}" @endif
                                                            alt="Profile" id="profileImage">
                                                    </div>

                                                    <label for="fileInput" class="btn2 btn-change2">Change</label>
                                                    <button class="btn2 btn-remove2"
                                                        onclick="removeImage()">Remove</button>

                                                    <input type="file" id="fileInput" name="fileProfileImage"
                                                        class="fileProfileImage" accept="image/*">
                                                    <input hidden name="removed" value='' id="removed">
                                                </div>
                                                <span class="text-danger error-text fileProfileImage_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input class="form-control" id="name" type="text"
                                                    name="name" required value="{{ auth()->user()->name }}">
                                                <span class="text-danger error-text name_error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input class="form-control" type="email" disabled
                                                    value="{{ auth()->user()->email }}">

                                            </div>
                                            @php
                                                $country_code = auth()->user()->country_code;
                                                $phone = auth()->user()->phone;
                                                if (!empty($phone) && !empty($country_code)) {
                                                    // Remove country code
                                                    $without_code = str_replace($country_code, '', $phone);
                                                } else {
                                                    // If phone or country_code is null, set it as null (or keep original)
                                                    $without_code = $phone ?? null;
                                                }
                                            @endphp
                                            <div class="form-group">
                                                <label>Phone Number</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <select name="country_code" class="form-control">
                                                            <option value="+1"
                                                                {{ $country_code == '+1' ? 'selected' : '' }}>USA
                                                                (+1)</option>
                                                            <option value="+44"
                                                                {{ $country_code == '+44' ? 'selected' : '' }}>UK
                                                                (+44)</option>
                                                            <option value="+971"
                                                                {{ $country_code == '+971' ? 'selected' : '' }}>UAE
                                                                (+971)</option>
                                                            <option value="+91"
                                                                {{ $country_code == '+91' ? 'selected' : '' }}>India
                                                                (+91)</option>
                                                            <option value="+61"
                                                                {{ $country_code == '+61' ? 'selected' : '' }}>
                                                                Australia (+61)</option>
                                                            <option value="+93"
                                                                {{ $country_code == '+93' ? 'selected' : '' }}>
                                                                Afghanistan (+93)</option>
                                                            <option value="+355"
                                                                {{ $country_code == '+355' ? 'selected' : '' }}>
                                                                Albania (+355)</option>
                                                            <option value="+213"
                                                                {{ $country_code == '+213' ? 'selected' : '' }}>
                                                                Algeria (+213)</option>
                                                            <option value="+376"
                                                                {{ $country_code == '+376' ? 'selected' : '' }}>
                                                                Andorra (+376)</option>
                                                            <option value="+244"
                                                                {{ $country_code == '+244' ? 'selected' : '' }}>
                                                                Angola (+244)</option>
                                                            <option value="+54"
                                                                {{ $country_code == '+54' ? 'selected' : '' }}>
                                                                Argentina (+54)</option>
                                                            <option value="+374"
                                                                {{ $country_code == '+374' ? 'selected' : '' }}>
                                                                Armenia (+374)</option>
                                                            <option value="+61"
                                                                {{ $country_code == '+61' ? 'selected' : '' }}>
                                                                Australia (+61)</option>
                                                            <option value="+43"
                                                                {{ $country_code == '+43' ? 'selected' : '' }}>
                                                                Austria (+43)</option>
                                                            <option value="+994"
                                                                {{ $country_code == '+994' ? 'selected' : '' }}>
                                                                Azerbaijan (+994)</option>
                                                            <option value="+973"
                                                                {{ $country_code == '+973' ? 'selected' : '' }}>
                                                                Bahrain (+973)</option>
                                                            <option value="+880"
                                                                {{ $country_code == '+880' ? 'selected' : '' }}>
                                                                Bangladesh (+880)</option>
                                                            <option value="+375"
                                                                {{ $country_code == '+375' ? 'selected' : '' }}>
                                                                Belarus (+375)</option>
                                                            <option value="+32"
                                                                {{ $country_code == '+32' ? 'selected' : '' }}>
                                                                Belgium (+32)</option>
                                                            <option value="+501"
                                                                {{ $country_code == '+501' ? 'selected' : '' }}>
                                                                Belize (+501)</option>
                                                            <option value="+229"
                                                                {{ $country_code == '+229' ? 'selected' : '' }}>
                                                                Benin (+229)</option>
                                                            <option value="+975"
                                                                {{ $country_code == '+975' ? 'selected' : '' }}>
                                                                Bhutan (+975)</option>
                                                            <option value="+591"
                                                                {{ $country_code == '+591' ? 'selected' : '' }}>
                                                                Bolivia (+591)</option>
                                                            <option value="+387"
                                                                {{ $country_code == '+387' ? 'selected' : '' }}>
                                                                Bosnia and Herzegovina (+387)</option>
                                                            <option value="+267"
                                                                {{ $country_code == '+267' ? 'selected' : '' }}>
                                                                Botswana (+267)</option>
                                                            <option value="+55"
                                                                {{ $country_code == '+55' ? 'selected' : '' }}>
                                                                Brazil (+55)</option>
                                                            <option value="+673"
                                                                {{ $country_code == '+673' ? 'selected' : '' }}>
                                                                Brunei (+673)</option>
                                                            <option value="+359"
                                                                {{ $country_code == '+359' ? 'selected' : '' }}>
                                                                Bulgaria (+359)</option>
                                                            <option value="+226"
                                                                {{ $country_code == '+226' ? 'selected' : '' }}>
                                                                Burkina Faso (+226)</option>
                                                            <option value="+257"
                                                                {{ $country_code == '+257' ? 'selected' : '' }}>
                                                                Burundi (+257)</option>
                                                            <option value="+855"
                                                                {{ $country_code == '+855' ? 'selected' : '' }}>
                                                                Cambodia (+855)</option>
                                                            <option value="+237"
                                                                {{ $country_code == '+237' ? 'selected' : '' }}>
                                                                Cameroon (+237)</option>
                                                            <option value="+1"
                                                                {{ $country_code == '+1' ? 'selected' : '' }}>Canada
                                                                (+1)</option>
                                                            <option value="+238"
                                                                {{ $country_code == '+238' ? 'selected' : '' }}>Cape
                                                                Verde (+238)</option>
                                                            <option value="+236"
                                                                {{ $country_code == '+236' ? 'selected' : '' }}>
                                                                Central African Republic (+236)</option>
                                                            <option value="+235"
                                                                {{ $country_code == '+235' ? 'selected' : '' }}>Chad
                                                                (+235)</option>
                                                            <option value="+56"
                                                                {{ $country_code == '+56' ? 'selected' : '' }}>Chile
                                                                (+56)</option>
                                                            <option value="+86"
                                                                {{ $country_code == '+86' ? 'selected' : '' }}>China
                                                                (+86)</option>
                                                            <option value="+57"
                                                                {{ $country_code == '+57' ? 'selected' : '' }}>
                                                                Colombia (+57)</option>
                                                            <option value="+269"
                                                                {{ $country_code == '+269' ? 'selected' : '' }}>
                                                                Comoros (+269)</option>
                                                            <option value="+243"
                                                                {{ $country_code == '+243' ? 'selected' : '' }}>
                                                                Congo (+243)</option>
                                                            <option value="+682"
                                                                {{ $country_code == '+682' ? 'selected' : '' }}>Cook
                                                                Islands (+682)</option>
                                                            <option value="+506"
                                                                {{ $country_code == '+506' ? 'selected' : '' }}>
                                                                Costa Rica (+506)</option>
                                                            <option value="+385"
                                                                {{ $country_code == '+385' ? 'selected' : '' }}>
                                                                Croatia (+385)</option>
                                                            <option value="+53"
                                                                {{ $country_code == '+53' ? 'selected' : '' }}>Cuba
                                                                (+53)</option>
                                                            <option value="+357"
                                                                {{ $country_code == '+357' ? 'selected' : '' }}>
                                                                Cyprus (+357)</option>
                                                            <option value="+420"
                                                                {{ $country_code == '+420' ? 'selected' : '' }}>
                                                                Czech Republic (+420)</option>
                                                            <option value="+45"
                                                                {{ $country_code == '+45' ? 'selected' : '' }}>
                                                                Denmark (+45)</option>
                                                            <option value="+253"
                                                                {{ $country_code == '+253' ? 'selected' : '' }}>
                                                                Djibouti (+253)</option>
                                                            <option value="+593"
                                                                {{ $country_code == '+593' ? 'selected' : '' }}>
                                                                Ecuador (+593)</option>
                                                            <option
                                                                value="+20"{{ $country_code == '+20' || $country_code == null ? 'selected' : '' }}>
                                                                Egypt
                                                                (+20)</option>
                                                            <option value="+503"
                                                                {{ $country_code == '+503' ? 'selected' : '' }}>El
                                                                Salvador (+503)</option>
                                                            <option value="+240"
                                                                {{ $country_code == '+240' ? 'selected' : '' }}>
                                                                Equatorial Guinea (+240)</option>
                                                            <option value="+291"
                                                                {{ $country_code == '+291' ? 'selected' : '' }}>
                                                                Eritrea (+291)</option>
                                                            <option value="+372"
                                                                {{ $country_code == '+372' ? 'selected' : '' }}>
                                                                Estonia (+372)</option>
                                                            <option value="+251"
                                                                {{ $country_code == '+251' ? 'selected' : '' }}>
                                                                Ethiopia (+251)</option>
                                                            <option value="+679"
                                                                {{ $country_code == '+679' ? 'selected' : '' }}>Fiji
                                                                (+679)</option>
                                                            <option value="+358"
                                                                {{ $country_code == '+358' ? 'selected' : '' }}>
                                                                Finland (+358)</option>
                                                            <option value="+33"
                                                                {{ $country_code == '+33' ? 'selected' : '' }}>
                                                                France (+33)</option>
                                                            <option value="+241"
                                                                {{ $country_code == '+241' ? 'selected' : '' }}>
                                                                Gabon (+241)</option>
                                                            <option value="+220"
                                                                {{ $country_code == '+220' ? 'selected' : '' }}>
                                                                Gambia (+220)</option>
                                                            <option value="+995"
                                                                {{ $country_code == '+995' ? 'selected' : '' }}>
                                                                Georgia (+995)</option>
                                                            <option value="+49"
                                                                {{ $country_code == '+49' ? 'selected' : '' }}>
                                                                Germany (+49)</option>
                                                            <option value="+233"
                                                                {{ $country_code == '+233' ? 'selected' : '' }}>
                                                                Ghana (+233)</option>
                                                            <option value="+30"
                                                                {{ $country_code == '+30' ? 'selected' : '' }}>
                                                                Greece (+30)</option>
                                                            <option value="+502"
                                                                {{ $country_code == '+502' ? 'selected' : '' }}>
                                                                Guatemala (+502)</option>
                                                            <option value="+224"
                                                                {{ $country_code == '+224' ? 'selected' : '' }}>
                                                                Guinea (+224)</option>
                                                            <option value="+592"
                                                                {{ $country_code == '+592' ? 'selected' : '' }}>
                                                                Guyana (+592)</option>
                                                            <option
                                                                value="+509"{{ $country_code == '+509' ? 'selected' : '' }}>
                                                                Haiti
                                                                (+509)</option>
                                                            <option
                                                                value="+504"{{ $country_code == '+504' ? 'selected' : '' }}>
                                                                Honduras (+504)</option>
                                                            <option
                                                                value="+36"{{ $country_code == '+36' ? 'selected' : '' }}>
                                                                Hungary (+36)</option>
                                                            <option
                                                                value="+354"{{ $country_code == '+354' ? 'selected' : '' }}>
                                                                Iceland (+354)</option>
                                                            <option
                                                                value="+62"{{ $country_code == '+62' ? 'selected' : '' }}>
                                                                Indonesia (+62)</option>
                                                            <option
                                                                value="+98"{{ $country_code == '+98' ? 'selected' : '' }}>
                                                                Iran
                                                                (+98)</option>
                                                            <option
                                                                value="+964"{{ $country_code == '+964' ? 'selected' : '' }}>
                                                                Iraq
                                                                (+964)</option>
                                                            <option
                                                                value="+353"{{ $country_code == '+353' ? 'selected' : '' }}>
                                                                Ireland (+353)</option>
                                                            <option
                                                                value="+39"{{ $country_code == '+39' ? 'selected' : '' }}>
                                                                Italy
                                                                (+39)</option>
                                                            <option
                                                                value="+81"{{ $country_code == '+81' ? 'selected' : '' }}>
                                                                Japan
                                                                (+81)</option>
                                                            <option
                                                                value="+962"{{ $country_code == '+962' ? 'selected' : '' }}>
                                                                Jordan (+962)</option>
                                                            <option
                                                                value="+7"{{ $country_code == '+7' ? 'selected' : '' }}>
                                                                Kazakhstan (+7)</option>
                                                            <option
                                                                value="+254"{{ $country_code == '+254' ? 'selected' : '' }}>
                                                                Kenya (+254)</option>
                                                            <option
                                                                value="+965"{{ $country_code == '+965' ? 'selected' : '' }}>
                                                                Kuwait (+965)</option>
                                                            <option
                                                                value="+996"{{ $country_code == '+996' ? 'selected' : '' }}>
                                                                Kyrgyzstan (+996)</option>
                                                            <option
                                                                value="+856"{{ $country_code == '+856' ? 'selected' : '' }}>
                                                                Laos
                                                                (+856)</option>
                                                            <option
                                                                value="+371"{{ $country_code == '+371' ? 'selected' : '' }}>
                                                                Latvia (+371)</option>
                                                            <option
                                                                value="+961"{{ $country_code == '+961' ? 'selected' : '' }}>
                                                                Lebanon (+961)</option>
                                                            <option
                                                                value="+266"{{ $country_code == '+266' ? 'selected' : '' }}>
                                                                Lesotho (+266)</option>
                                                            <option
                                                                value="+231"{{ $country_code == '+231' ? 'selected' : '' }}>
                                                                Liberia (+231)</option>
                                                            <option
                                                                value="+370"{{ $country_code == '+370' ? 'selected' : '' }}>
                                                                Lithuania (+370)</option>
                                                            <option
                                                                value="+352"{{ $country_code == '+352' ? 'selected' : '' }}>
                                                                Luxembourg (+352)</option>
                                                            <option
                                                                value="+389"{{ $country_code == '+389' ? 'selected' : '' }}>
                                                                Macedonia (+389)</option>
                                                            <option
                                                                value="+261"{{ $country_code == '+261' ? 'selected' : '' }}>
                                                                Madagascar (+261)</option>
                                                            <option
                                                                value="+265"{{ $country_code == '+265' ? 'selected' : '' }}>
                                                                Malawi (+265)</option>
                                                            <option
                                                                value="+60"{{ $country_code == '+60' ? 'selected' : '' }}>
                                                                Malaysia (+60)</option>
                                                            <option
                                                                value="+960"{{ $country_code == '+960' ? 'selected' : '' }}>
                                                                Maldives (+960)</option>
                                                            <option
                                                                value="+223"{{ $country_code == '+223' ? 'selected' : '' }}>
                                                                Mali (+223)</option>
                                                            <option
                                                                value="+356"{{ $country_code == '+356' ? 'selected' : '' }}>
                                                                Malta (+356)</option>
                                                            <option
                                                                value="+692"{{ $country_code == '+692' ? 'selected' : '' }}>
                                                                Marshall Islands (+692)</option>
                                                            <option
                                                                value="+222"{{ $country_code == '+222' ? 'selected' : '' }}>
                                                                Mauritania (+222)</option>
                                                            <option
                                                                value="+230"{{ $country_code == '+230' ? 'selected' : '' }}>
                                                                Mauritius (+230)</option>
                                                            <option
                                                                value="+52"{{ $country_code == '+52' ? 'selected' : '' }}>
                                                                Mexico (+52)</option>
                                                            <option
                                                                value="+373"{{ $country_code == '+373' ? 'selected' : '' }}>
                                                                Moldova (+373)</option>
                                                            <option
                                                                value="+377"{{ $country_code == '+377' ? 'selected' : '' }}>
                                                                Monaco (+377)</option>
                                                            <option
                                                                value="+976"{{ $country_code == '+976' ? 'selected' : '' }}>
                                                                Mongolia (+976)</option>
                                                            <option
                                                                value="+382"{{ $country_code == '+382' ? 'selected' : '' }}>
                                                                Montenegro (+382)</option>
                                                            <option
                                                                value="+212"{{ $country_code == '+212' ? 'selected' : '' }}>
                                                                Morocco (+212)</option>
                                                            <option
                                                                value="+258"{{ $country_code == '+258' ? 'selected' : '' }}>
                                                                Mozambique (+258)</option>
                                                            <option
                                                                value="+95"{{ $country_code == '+95' ? 'selected' : '' }}>
                                                                Myanmar (+95)</option>
                                                            <option
                                                                value="+264"{{ $country_code == '+264' ? 'selected' : '' }}>
                                                                Namibia (+264)</option>
                                                            <option
                                                                value="+977"{{ $country_code == '+977' ? 'selected' : '' }}>
                                                                Nepal (+977)</option>
                                                            <option
                                                                value="+31"{{ $country_code == '+31' ? 'selected' : '' }}>
                                                                Netherlands (+31)</option>
                                                            <option
                                                                value="+64"{{ $country_code == '+64' ? 'selected' : '' }}>
                                                                New
                                                                Zealand (+64)</option>
                                                            <option
                                                                value="+505"{{ $country_code == '+505' ? 'selected' : '' }}>
                                                                Nicaragua (+505)</option>
                                                            <option
                                                                value="+227"{{ $country_code == '+227' ? 'selected' : '' }}>
                                                                Niger (+227)</option>
                                                            <option
                                                                value="+234"{{ $country_code == '+234' ? 'selected' : '' }}>
                                                                Nigeria (+234)</option>
                                                            <option
                                                                value="+47"{{ $country_code == '+47' ? 'selected' : '' }}>
                                                                Norway (+47)</option>
                                                            <option
                                                                value="+968"{{ $country_code == '+968' ? 'selected' : '' }}>
                                                                Oman (+968)</option>
                                                            <option
                                                                value="+92"{{ $country_code == '+92' ? 'selected' : '' }}>
                                                                Pakistan (+92)</option>
                                                            <option
                                                                value="+507"{{ $country_code == '+507' ? 'selected' : '' }}>
                                                                Panama (+507)</option>
                                                            <option
                                                                value="+675"{{ $country_code == '+675' ? 'selected' : '' }}>
                                                                Papua New Guinea (+675)</option>
                                                            <option
                                                                value="+595"{{ $country_code == '+595' ? 'selected' : '' }}>
                                                                Paraguay (+595)</option>
                                                            <option
                                                                value="+51"{{ $country_code == '+51' ? 'selected' : '' }}>
                                                                Peru
                                                                (+51)</option>
                                                            <option
                                                                value="+63"{{ $country_code == '+63' ? 'selected' : '' }}>
                                                                Philippines (+63)</option>
                                                            <option
                                                                value="+48"{{ $country_code == '+48' ? 'selected' : '' }}>
                                                                Poland (+48)</option>
                                                            <option
                                                                value="+351"{{ $country_code == '+351' ? 'selected' : '' }}>
                                                                Portugal (+351)</option>
                                                            <option
                                                                value="+974"{{ $country_code == '+974' ? 'selected' : '' }}>
                                                                Qatar (+974)</option>
                                                            <option
                                                                value="+40"{{ $country_code == '+40' ? 'selected' : '' }}>
                                                                Romania (+40)</option>
                                                            <option
                                                                value="+7"{{ $country_code == '+7' ? 'selected' : '' }}>
                                                                Russia (+7)</option>
                                                            <option
                                                                value="+250"{{ $country_code == '+250' ? 'selected' : '' }}>
                                                                Rwanda (+250)</option>
                                                            <option
                                                                value="+966"{{ $country_code == '+966' ? 'selected' : '' }}>
                                                                Saudi Arabia (+966)</option>
                                                            <option
                                                                value="+221"{{ $country_code == '+221' ? 'selected' : '' }}>
                                                                Senegal (+221)</option>
                                                            <option
                                                                value="+381"{{ $country_code == '+381' ? 'selected' : '' }}>
                                                                Serbia (+381)</option>
                                                            <option
                                                                value="+248"{{ $country_code == '+248' ? 'selected' : '' }}>
                                                                Seychelles (+248)</option>
                                                            <option
                                                                value="+232"{{ $country_code == '+232' ? 'selected' : '' }}>
                                                                Sierra Leone (+232)</option>
                                                            <option
                                                                value="+65"{{ $country_code == '+65' ? 'selected' : '' }}>
                                                                Singapore (+65)</option>
                                                            <option
                                                                value="+421"{{ $country_code == '+421' ? 'selected' : '' }}>
                                                                Slovakia (+421)</option>
                                                            <option
                                                                value="+386"{{ $country_code == '+386' ? 'selected' : '' }}>
                                                                Slovenia (+386)</option>
                                                            <option
                                                                value="+27"{{ $country_code == '+27' ? 'selected' : '' }}>
                                                                South Africa (+27)</option>
                                                            <option
                                                                value="+82"{{ $country_code == '+82' ? 'selected' : '' }}>
                                                                South Korea (+82)</option>
                                                            <option
                                                                value="+34"{{ $country_code == '+34' ? 'selected' : '' }}>
                                                                Spain (+34)</option>
                                                            <option
                                                                value="+94"{{ $country_code == '+94' ? 'selected' : '' }}>
                                                                Sri
                                                                Lanka (+94)</option>
                                                            <option
                                                                value="+249"{{ $country_code == '+249' ? 'selected' : '' }}>
                                                                Sudan (+249)</option>
                                                            <option
                                                                value="+597"{{ $country_code == '+597' ? 'selected' : '' }}>
                                                                Suriname (+597)</option>
                                                            <option
                                                                value="+268"{{ $country_code == '+268' ? 'selected' : '' }}>
                                                                Swaziland (+268)</option>
                                                            <option
                                                                value="+46"{{ $country_code == '+46' ? 'selected' : '' }}>
                                                                Sweden (+46)</option>
                                                            <option
                                                                value="+41"{{ $country_code == '+41' ? 'selected' : '' }}>
                                                                Switzerland (+41)</option>
                                                            <option
                                                                value="+963"{{ $country_code == '+963' ? 'selected' : '' }}>
                                                                Syria (+963)</option>
                                                            <option
                                                                value="+886"{{ $country_code == '+886' ? 'selected' : '' }}>
                                                                Taiwan (+886)</option>
                                                            <option
                                                                value="+992"{{ $country_code == '+992' ? 'selected' : '' }}>
                                                                Tajikistan (+992)</option>
                                                            <option
                                                                value="+255"{{ $country_code == '+255' ? 'selected' : '' }}>
                                                                Tanzania (+255)</option>
                                                            <option
                                                                value="+66"{{ $country_code == '+66' ? 'selected' : '' }}>
                                                                Thailand (+66)</option>
                                                            <option
                                                                value="+228"{{ $country_code == '+228' ? 'selected' : '' }}>
                                                                Togo (+228)</option>
                                                            <option
                                                                value="+676"{{ $country_code == '+676' ? 'selected' : '' }}>
                                                                Tonga (+676)</option>
                                                            <option
                                                                value="+216"{{ $country_code == '+216' ? 'selected' : '' }}>
                                                                Tunisia (+216)</option>
                                                            <option
                                                                value="+90"{{ $country_code == '+90' ? 'selected' : '' }}>
                                                                Turkey (+90)</option>
                                                            <option
                                                                value="+993"{{ $country_code == '+993' ? 'selected' : '' }}>
                                                                Turkmenistan (+993)</option>
                                                            <option
                                                                value="+256"{{ $country_code == '+256' ? 'selected' : '' }}>
                                                                Uganda (+256)</option>
                                                            <option
                                                                value="+380"{{ $country_code == '+380' ? 'selected' : '' }}>
                                                                Ukraine (+380)</option>
                                                            <option
                                                                value="+971"{{ $country_code == '+971' ? 'selected' : '' }}>
                                                                United Arab Emirates (+971)</option>
                                                            <option
                                                                value="+44"{{ $country_code == '+44' ? 'selected' : '' }}>
                                                                United Kingdom (+44)</option>
                                                            <option
                                                                value="+1"{{ $country_code == '+1' ? 'selected' : '' }}>
                                                                United States (+1)</option>
                                                            <option
                                                                value="+598"{{ $country_code == '+598' ? 'selected' : '' }}>
                                                                Uruguay (+598)</option>
                                                            <option
                                                                value="+998"{{ $country_code == '+998' ? 'selected' : '' }}>
                                                                Uzbekistan (+998)</option>
                                                            <option
                                                                value="+58"{{ $country_code == '+58' ? 'selected' : '' }}>
                                                                Venezuela (+58)</option>
                                                            <option
                                                                value="+84"{{ $country_code == '+84' ? 'selected' : '' }}>
                                                                Vietnam (+84)</option>
                                                            <option
                                                                value="+967"{{ $country_code == '+967' ? 'selected' : '' }}>
                                                                Yemen (+967)</option>
                                                            <option
                                                                value="+260"{{ $country_code == '+260' ? 'selected' : '' }}>
                                                                Zambia (+260)</option>
                                                            <option
                                                                value="+263"{{ $country_code == '+263' ? 'selected' : '' }}>
                                                                Zimbabwe (+263)</option>
                                                        </select>
                                                    </div>
                                                    <input type="number" name="phone" class="form-control"
                                                        placeholder="Enter Phone Number"
                                                        value="{{ $without_code }}">
                                                </div>
                                                <span class="text-danger error-text phone_error"></span>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn mb-2 btn-danger"
                                            id="changePasswordButton">Change Password</button>
                                        <button type="button" class="btn btn-primary"
                                            id="saveUserProfileInfo">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="changePasswordModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Change Password</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="changePasswordForm">
                                            @csrf

                                            <div class="form-group">
                                                <label>Old Password</label>
                                                <input class="form-control" id="old_password" type="password"
                                                    name="old_password" required>
                                                <span class="text-danger error-text old_password_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>New Password</label>
                                                <input class="form-control" type="password" name="new_password"
                                                    id="new_password" required>
                                                <span class="text-danger error-text new_password_error"></span>
                                            </div>

                                            <div class="form-group">
                                                <label>Confirm New Password</label>
                                                <input class="form-control" type="password"
                                                    name="new_password_confirmation" id="new_password_confirmation"
                                                    required>
                                                <span
                                                    class="text-danger error-text new_password_confirmation_error"></span>
                                            </div>

                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary"
                                            id="saveChangePassword">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        $('.xxxx').select2({
            multiple: true,
            theme: 'bootstrap4',
            tags: false
        }).on('change', function() {
            let selected = $(this).find(':selected');
            let container = $('#exhibit-links');
            container.empty(); // clear old links

            selected.each(function() {
                let id = $(this).val();
                let slug = $(this).data('docslug');
                let text = $(this).text();

                // adjust href depending on your route
                let link = `<a href="/${slug}" target="_blank" class="d-block mb-1">
                        ${text}
                    </a>`;
                container.append(link);
            });
        });
        $('.xxxxx').select2({
            multiple: true,
            theme: 'bootstrap4',
            tags: false
        }).on('change', function() {
            let selected = $(this).find(':selected');
            let container = $('#exhibit-links2');
            container.empty(); // clear old links

            selected.each(function() {
                let id = $(this).val();
                let slug = $(this).data('docslug');
                let text = $(this).text();

                // adjust href depending on your route
                let link = `<a href="/${slug}" target="_blank" class="d-block mb-1">
                        ${text}
                    </a>`;
                container.append(link);
            });
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


    <script>
        $(document).ready(function() {



            let bootstrapScriptId = "bootstrap-5-bundle";
            $('#UpdateUserProfileInfoLink').on('click', function() {

                if (!document.getElementById(bootstrapScriptId)) {
                    let script = document.createElement("script");
                    script.src =
                        "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js";
                    script.id = bootstrapScriptId;
                    document.body.appendChild(script);

                    script.onload = function() {
                        $('#userProfileInfoModal').modal('show'); // show after Bootstrap loaded
                    };
                } else {
                    $('#userProfileInfoModal').modal('show');
                }
            });

            $('#saveUserProfileInfo').on('click', function(e) {
                e.preventDefault();

                let formData = new FormData($('#userProfileInfoForm')[0]);

                $.ajax({
                    url: "{{ route('user.updateProfile') }}", // define route in web.php
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.error-text').text('');
                            alert("Profile updated successfully!");
                            $('#userProfileInfoModal').modal('hide');
                            location.reload(); // refresh to show new data (optional)
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('.error-text').text(''); // clear old errors
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                // find span with class="key_error"
                                $('.' + key + '_error').text(value[0]);
                            });
                        } else {
                            alert("Something went wrong!");
                        }
                    }
                });
            });
            $('#userProfileInfoModal').on('hidden.bs.modal', function() {
                let script = document.getElementById(bootstrapScriptId);
                if (script) {
                    script.remove();
                }
            });

            $('#report1Link').on('click', function() {
                if (!document.getElementById(bootstrapScriptId)) {
                    let script = document.createElement("script");
                    script.src =
                        "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js";
                    script.id = bootstrapScriptId;
                    document.body.appendChild(script);

                    script.onload = function() {
                        $('#report1Modal').modal('show'); // show after Bootstrap loaded
                    };
                } else {
                    $('#report1Modal').modal('show');
                }

            });
            $('#report1').on('click', function() {
                let form = $('#report1Form');
                let data = form.serialize(); // serialize all inputs

                let showOption = $('input[name="show_data"]:checked').val();

                if (showOption === 'option1') {
                    let url = "{{ route('project.all_documents.index') }}?" + data;
                    window.open(url, '_blank');
                } else if (showOption === 'option2') {
                    $.ajax({
                        url: "{{ route('project.all_documents.index') }}",
                        type: "GET",
                        data: data + "&export=excel",
                        success: function(response) {
                            
                                // open the file in new tab
                                
                                window.open(response.download_url, '_blank');

                                // OR download directly:
                                // window.location.href = response.url;

                                // OR show the link in modal:
                                // $('#excelLink').attr('href', response.url).show();
                           
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert("Failed to export Excel.");
                        }
                    });
                }
            });
            $('#report1Modal').on('hidden.bs.modal', function() {
                let script = document.getElementById(bootstrapScriptId);
                if (script) {
                    script.remove();
                }
            });

            $('#changePasswordModal').on('hidden.bs.modal', function() {
                let script = document.getElementById(bootstrapScriptId);
                if (script) {
                    script.remove();
                }
            });
            $('#changePasswordButton').on('click', function() {

                $('#userProfileInfoModal').modal('hide');
                if (!document.getElementById(bootstrapScriptId)) {
                    let script = document.createElement("script");
                    script.src =
                        "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js";
                    script.id = bootstrapScriptId;
                    document.body.appendChild(script);

                    script.onload = function() {
                        $('#changePasswordModal').modal('show'); // show after Bootstrap loaded
                    };
                } else {
                    $('#changePasswordModal').modal('show');
                }
            });

            $('#saveChangePassword').on('click', function(e) {
                e.preventDefault();

                let form = $('#changePasswordForm')[0];
                let formData = new FormData(form);

                $.ajax({
                    url: "{{ route('user.changePassword') }}", // define this route
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function(response) {
                        $('.error-text').text(''); // clear errors
                        if (response.success) {
                            alert("Password changed successfully!");
                            $('#changePasswordModal').modal('hide');
                            $('#changePasswordForm')[0].reset();
                        } else {
                            alert(response.message || "Something went wrong!");
                        }
                    },
                    error: function(xhr) {
                        $('.error-text').text(""); // clear old errors

                        if (xhr.status === 422) {
                            let res = xhr.responseJSON;

                            if (res.errors) {
                                // Laravel validation errors
                                $.each(res.errors, function(key, value) {
                                    $('.' + key + '_error').text(value[0]);
                                });
                            } else if (res.message) {
                                // Custom error (old password incorrect)
                                $('.old_password_error').text(res.message);
                            }
                        }
                    }
                });
            });
        });
    </script>
    <script>
        const fileInput = document.getElementById('fileInput');
        const profileImage = document.getElementById('profileImage');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImage.src = e.target.result;
                }
                reader.readAsDataURL(file);
                document.getElementById('removed').value = '';
            }
        });

        function removeImage() {
            profileImage.src = "{{ asset('dashboard/assets/avatars/user_avatar.png') }}";
            fileInput.value = "";
            document.getElementById('removed').value = 'on';
        }
    </script>
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
