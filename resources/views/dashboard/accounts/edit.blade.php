@extends('dashboard.layout.app')
@section('title', 'Update Account')
@section('content')
    <h2 class="page-title">Update Account</h2>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">



                <div class="col-md-12">
                    <form method="post" action="{{ route('account.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="simpleinputName">Name</label>
                            <input type="text" name="name" required id="simpleinputName" class="form-control"
                                placeholder="Name" value="{{ old('name',$account->name) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-email">Email</label>
                            <input type="email" id="example-email" name="email" class="form-control" required
                                placeholder="Email" value="{{ old('name',$account->email) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-phone">Phone Number</label>
                            <div class="input-group">
                                <div class="input-group-prepend" style="width: 20%;">
                                    <select name="country_code" class="form-control">
                                        <option value="+1" @if($account->country_code == '+1') selected @endif>USA (+1)</option>
                                        <option value="+44"@if($account->country_code == '+44') selected @endif>UK (+44)</option>
                                        <option value="+971"@if($account->country_code == '+971') selected @endif>UAE (+971)</option>
                                        <option value="+91"@if($account->country_code == '+91') selected @endif>India (+91)</option>
                                        <option value="+61"@if($account->country_code == '+61') selected @endif>Australia (+61)</option>
                                        <option value="+93"@if($account->country_code == '+93') selected @endif>Afghanistan (+93)</option>
                                        <option value="+355"@if($account->country_code == '+355') selected @endif>Albania (+355)</option>
                                        <option value="+213"@if($account->country_code == '+213') selected @endif>Algeria (+213)</option>
                                        <option value="+376"@if($account->country_code == '+376') selected @endif>Andorra (+376)</option>
                                        <option value="+244"@if($account->country_code == '+244') selected @endif>Angola (+244)</option>
                                        <option value="+54"@if($account->country_code == '+54') selected @endif>Argentina (+54)</option>
                                        <option value="+374"@if($account->country_code == '+374') selected @endif>Armenia (+374)</option>
                                        <option value="+61"@if($account->country_code == '+61') selected @endif>Australia (+61)</option>
                                        <option value="+43"@if($account->country_code == '+43') selected @endif>Austria (+43)</option>
                                        <option value="+994"@if($account->country_code == '+994') selected @endif>Azerbaijan (+994)</option>
                                        <option value="+973"@if($account->country_code == '+973') selected @endif>Bahrain (+973)</option>
                                        <option value="+880"@if($account->country_code == '+880') selected @endif>Bangladesh (+880)</option>
                                        <option value="+375"@if($account->country_code == '+375') selected @endif>Belarus (+375)</option>
                                        <option value="+32"@if($account->country_code == '+32') selected @endif>Belgium (+32)</option>
                                        <option value="+501"@if($account->country_code == '+501') selected @endif>Belize (+501)</option>
                                        <option value="+229"@if($account->country_code == '+229') selected @endif>Benin (+229)</option>
                                        <option value="+975"@if($account->country_code == '+975') selected @endif>Bhutan (+975)</option>
                                        <option value="+591"@if($account->country_code == '+591') selected @endif>Bolivia (+591)</option>
                                        <option value="+387"@if($account->country_code == '+387') selected @endif>Bosnia and Herzegovina (+387)</option>
                                        <option value="+267"@if($account->country_code == '+267') selected @endif>Botswana (+267)</option>
                                        <option value="+55"@if($account->country_code == '+55') selected @endif>Brazil (+55)</option>
                                        <option value="+673"@if($account->country_code == '+673') selected @endif>Brunei (+673)</option>
                                        <option value="+359"@if($account->country_code == '+359') selected @endif>Bulgaria (+359)</option>
                                        <option value="+226"@if($account->country_code == '+226') selected @endif>Burkina Faso (+226)</option>
                                        <option value="+257"@if($account->country_code == '+257') selected @endif>Burundi (+257)</option>
                                        <option value="+855"@if($account->country_code == '+855') selected @endif>Cambodia (+855)</option>
                                        <option value="+237"@if($account->country_code == '+237') selected @endif>Cameroon (+237)</option>
                                        <option value="+1"@if($account->country_code == '+1') selected @endif>Canada (+1)</option>
                                        <option value="+238"@if($account->country_code == '+238') selected @endif>Cape Verde (+238)</option>
                                        <option value="+236"@if($account->country_code == '+236') selected @endif>Central African Republic (+236)</option>
                                        <option value="+235"@if($account->country_code == '+235') selected @endif>Chad (+235)</option>
                                        <option value="+56"@if($account->country_code == '+56') selected @endif>Chile (+56)</option>
                                        <option value="+86"@if($account->country_code == '+86') selected @endif>China (+86)</option>
                                        <option value="+57"@if($account->country_code == '+57') selected @endif>Colombia (+57)</option>
                                        <option value="+269"@if($account->country_code == '+269') selected @endif>Comoros (+269)</option>
                                        <option value="+243"@if($account->country_code == '+243') selected @endif>Congo (+243)</option>
                                        <option value="+682"@if($account->country_code == '+682') selected @endif>Cook Islands (+682)</option>
                                        <option value="+506"@if($account->country_code == '+506') selected @endif>Costa Rica (+506)</option>
                                        <option value="+385"@if($account->country_code == '+385') selected @endif>Croatia (+385)</option>
                                        <option value="+53"@if($account->country_code == '+53') selected @endif>Cuba (+53)</option>
                                        <option value="+357"@if($account->country_code == '+357') selected @endif>Cyprus (+357)</option>
                                        <option value="+420"@if($account->country_code == '+420') selected @endif>Czech Republic (+420)</option>
                                        <option value="+45"@if($account->country_code == '+45') selected @endif>Denmark (+45)</option>
                                        <option value="+253"@if($account->country_code == '+253') selected @endif>Djibouti (+253)</option>
                                        <option value="+593"@if($account->country_code == '+593') selected @endif>Ecuador (+593)</option>
                                        <option value="+20"@if($account->country_code == '+20') selected @endif>Egypt (+20)</option>
                                        <option value="+503"@if($account->country_code == '+503') selected @endif>El Salvador (+503)</option>
                                        <option value="+240"@if($account->country_code == '+240') selected @endif>Equatorial Guinea (+240)</option>
                                        <option value="+291"@if($account->country_code == '+291') selected @endif>Eritrea (+291)</option>
                                        <option value="+372"@if($account->country_code == '+372') selected @endif>Estonia (+372)</option>
                                        <option value="+251"@if($account->country_code == '+251') selected @endif>Ethiopia (+251)</option>
                                        <option value="+679"@if($account->country_code == '+679') selected @endif>Fiji (+679)</option>
                                        <option value="+358"@if($account->country_code == '+358') selected @endif>Finland (+358)</option>
                                        <option value="+33"@if($account->country_code == '+33') selected @endif>France (+33)</option>
                                        <option value="+241"@if($account->country_code == '+241') selected @endif>Gabon (+241)</option>
                                        <option value="+220"@if($account->country_code == '+220') selected @endif>Gambia (+220)</option>
                                        <option value="+995"@if($account->country_code == '+995') selected @endif>Georgia (+995)</option>
                                        <option value="+49"@if($account->country_code == '+49') selected @endif>Germany (+49)</option>
                                        <option value="+233"@if($account->country_code == '+233') selected @endif>Ghana (+233)</option>
                                        <option value="+30"@if($account->country_code == '+30') selected @endif>Greece (+30)</option>
                                        <option value="+502"@if($account->country_code == '+502') selected @endif>Guatemala (+502)</option>
                                        <option value="+224"@if($account->country_code == '+224') selected @endif>Guinea (+224)</option>
                                        <option value="+592"@if($account->country_code == '+592') selected @endif>Guyana (+592)</option>
                                        <option value="+509"@if($account->country_code == '+509') selected @endif>Haiti (+509)</option>
                                        <option value="+504"@if($account->country_code == '+504') selected @endif>Honduras (+504)</option>
                                        <option value="+36"@if($account->country_code == '+36') selected @endif>Hungary (+36)</option>
                                        <option value="+354"@if($account->country_code == '+354') selected @endif>Iceland (+354)</option>
                                        <option value="+62"@if($account->country_code == '+62') selected @endif>Indonesia (+62)</option>
                                        <option value="+98"@if($account->country_code == '+98') selected @endif>Iran (+98)</option>
                                        <option value="+964"@if($account->country_code == '+964') selected @endif>Iraq (+964)</option>
                                        <option value="+353"@if($account->country_code == '+353') selected @endif>Ireland (+353)</option>
                                        <option value="+39"@if($account->country_code == '+39') selected @endif>Italy (+39)</option>
                                        <option value="+81"@if($account->country_code == '+81') selected @endif>Japan (+81)</option>
                                        <option value="+962"@if($account->country_code == '+962') selected @endif>Jordan (+962)</option>
                                        <option value="+7"@if($account->country_code == '+7') selected @endif>Kazakhstan (+7)</option>
                                        <option value="+254"@if($account->country_code == '+254') selected @endif>Kenya (+254)</option>
                                        <option value="+965"@if($account->country_code == '+965') selected @endif>Kuwait (+965)</option>
                                        <option value="+996"@if($account->country_code == '+996') selected @endif>Kyrgyzstan (+996)</option>
                                        <option value="+856"@if($account->country_code == '+856') selected @endif>Laos (+856)</option>
                                        <option value="+371"@if($account->country_code == '+371') selected @endif>Latvia (+371)</option>
                                        <option value="+961"@if($account->country_code == '+961') selected @endif>Lebanon (+961)</option>
                                        <option value="+266"@if($account->country_code == '+266') selected @endif>Lesotho (+266)</option>
                                        <option value="+231"@if($account->country_code == '+231') selected @endif>Liberia (+231)</option>
                                        <option value="+370"@if($account->country_code == '+370') selected @endif>Lithuania (+370)</option>
                                        <option value="+352"@if($account->country_code == '+352') selected @endif>Luxembourg (+352)</option>
                                        <option value="+389"@if($account->country_code == '+389') selected @endif>Macedonia (+389)</option>
                                        <option value="+261"@if($account->country_code == '+261') selected @endif>Madagascar (+261)</option>
                                        <option value="+265"@if($account->country_code == '+265') selected @endif>Malawi (+265)</option>
                                        <option value="+60"@if($account->country_code == '+60') selected @endif>Malaysia (+60)</option>
                                        <option value="+960"@if($account->country_code == '+960') selected @endif>Maldives (+960)</option>
                                        <option value="+223"@if($account->country_code == '+223') selected @endif>Mali (+223)</option>
                                        <option value="+356"@if($account->country_code == '+356') selected @endif>Malta (+356)</option>
                                        <option value="+692"@if($account->country_code == '+692') selected @endif>Marshall Islands (+692)</option>
                                        <option value="+222"@if($account->country_code == '+222') selected @endif>Mauritania (+222)</option>
                                        <option value="+230"@if($account->country_code == '+230') selected @endif>Mauritius (+230)</option>
                                        <option value="+52"@if($account->country_code == '+52') selected @endif>Mexico (+52)</option>
                                        <option value="+373"@if($account->country_code == '+373') selected @endif>Moldova (+373)</option>
                                        <option value="+377"@if($account->country_code == '+377') selected @endif>Monaco (+377)</option>
                                        <option value="+976"@if($account->country_code == '+976') selected @endif>Mongolia (+976)</option>
                                        <option value="+382"@if($account->country_code == '+382') selected @endif>Montenegro (+382)</option>
                                        <option value="+212"@if($account->country_code == '+212') selected @endif>Morocco (+212)</option>
                                        <option value="+258"@if($account->country_code == '+258') selected @endif>Mozambique (+258)</option>
                                        <option value="+95"@if($account->country_code == '+95') selected @endif>Myanmar (+95)</option>
                                        <option value="+264"@if($account->country_code == '+264') selected @endif>Namibia (+264)</option>
                                        <option value="+977"@if($account->country_code == '+977') selected @endif>Nepal (+977)</option>
                                        <option value="+31"@if($account->country_code == '+31') selected @endif>Netherlands (+31)</option>
                                        <option value="+64"@if($account->country_code == '+64') selected @endif>New Zealand (+64)</option>
                                        <option value="+505"@if($account->country_code == '+505') selected @endif>Nicaragua (+505)</option>
                                        <option value="+227"@if($account->country_code == '+227') selected @endif>Niger (+227)</option>
                                        <option value="+234"@if($account->country_code == '+234') selected @endif>Nigeria (+234)</option>
                                        <option value="+47"@if($account->country_code == '+47') selected @endif>Norway (+47)</option>
                                        <option value="+968"@if($account->country_code == '+968') selected @endif>Oman (+968)</option>
                                        <option value="+92"@if($account->country_code == '+92') selected @endif>Pakistan (+92)</option>
                                        <option value="+507"@if($account->country_code == '+507') selected @endif>Panama (+507)</option>
                                        <option value="+675"@if($account->country_code == '+675') selected @endif>Papua New Guinea (+675)</option>
                                        <option value="+595"@if($account->country_code == '+595') selected @endif>Paraguay (+595)</option>
                                        <option value="+51"@if($account->country_code == '+51') selected @endif>Peru (+51)</option>
                                        <option value="+63"@if($account->country_code == '+63') selected @endif>Philippines (+63)</option>
                                        <option value="+48"@if($account->country_code == '+48') selected @endif>Poland (+48)</option>
                                        <option value="+351"@if($account->country_code == '+351') selected @endif>Portugal (+351)</option>
                                        <option value="+974"@if($account->country_code == '+974') selected @endif>Qatar (+974)</option>
                                        <option value="+40"@if($account->country_code == '+40') selected @endif>Romania (+40)</option>
                                        <option value="+7"@if($account->country_code == '+7') selected @endif>Russia (+7)</option>
                                        <option value="+250"@if($account->country_code == '+250') selected @endif>Rwanda (+250)</option>
                                        <option value="+966"@if($account->country_code == '+966') selected @endif>Saudi Arabia (+966)</option>
                                        <option value="+221"@if($account->country_code == '+221') selected @endif>Senegal (+221)</option>
                                        <option value="+381"@if($account->country_code == '+381') selected @endif>Serbia (+381)</option>
                                        <option value="+248"@if($account->country_code == '+248') selected @endif>Seychelles (+248)</option>
                                        <option value="+232"@if($account->country_code == '+232') selected @endif>Sierra Leone (+232)</option>
                                        <option value="+65"@if($account->country_code == '+65') selected @endif>Singapore (+65)</option>
                                        <option value="+421"@if($account->country_code == '+421') selected @endif>Slovakia (+421)</option>
                                        <option value="+386"@if($account->country_code == '+386') selected @endif>Slovenia (+386)</option>
                                        <option value="+27"@if($account->country_code == '+27') selected @endif>South Africa (+27)</option>
                                        <option value="+82"@if($account->country_code == '+82') selected @endif>South Korea (+82)</option>
                                        <option value="+34"@if($account->country_code == '+34') selected @endif>Spain (+34)</option>
                                        <option value="+94"@if($account->country_code == '+94') selected @endif>Sri Lanka (+94)</option>
                                        <option value="+249"@if($account->country_code == '+249') selected @endif>Sudan (+249)</option>
                                        <option value="+597"@if($account->country_code == '+597') selected @endif>Suriname (+597)</option>
                                        <option value="+268"@if($account->country_code == '+268') selected @endif>Swaziland (+268)</option>
                                        <option value="+46"@if($account->country_code == '+46') selected @endif>Sweden (+46)</option>
                                        <option value="+41"@if($account->country_code == '+41') selected @endif>Switzerland (+41)</option>
                                        <option value="+963"@if($account->country_code == '+963') selected @endif>Syria (+963)</option>
                                        <option value="+886"@if($account->country_code == '+886') selected @endif>Taiwan (+886)</option>
                                        <option value="+992"@if($account->country_code == '+992') selected @endif>Tajikistan (+992)</option>
                                        <option value="+255"@if($account->country_code == '+255') selected @endif>Tanzania (+255)</option>
                                        <option value="+66"@if($account->country_code == '+66') selected @endif>Thailand (+66)</option>
                                        <option value="+228"@if($account->country_code == '+228') selected @endif>Togo (+228)</option>
                                        <option value="+676"@if($account->country_code == '+676') selected @endif>Tonga (+676)</option>
                                        <option value="+216"@if($account->country_code == '+216') selected @endif>Tunisia (+216)</option>
                                        <option value="+90"@if($account->country_code == '+90') selected @endif>Turkey (+90)</option>
                                        <option value="+993"@if($account->country_code == '+993') selected @endif>Turkmenistan (+993)</option>
                                        <option value="+256"@if($account->country_code == '+256') selected @endif>Uganda (+256)</option>
                                        <option value="+380"@if($account->country_code == '+380') selected @endif>Ukraine (+380)</option>
                                        <option value="+971"@if($account->country_code == '+971') selected @endif>United Arab Emirates (+971)</option>
                                        <option value="+44"@if($account->country_code == '+44') selected @endif>United Kingdom (+44)</option>
                                        <option value="+1"@if($account->country_code == '+1') selected @endif>United States (+1)</option>
                                        <option value="+598"@if($account->country_code == '+598') selected @endif>Uruguay (+598)</option>
                                        <option value="+998"@if($account->country_code == '+998') selected @endif>Uzbekistan (+998)</option>
                                        <option value="+58"@if($account->country_code == '+58') selected @endif>Venezuela (+58)</option>
                                        <option value="+84"@if($account->country_code == '+84') selected @endif>Vietnam (+84)</option>
                                        <option value="+967"@if($account->country_code == '+967') selected @endif>Yemen (+967)</option>
                                        <option value="+260"@if($account->country_code == '+260') selected @endif>Zambia (+260)</option>
                                        <option value="+263"@if($account->country_code == '+263') selected @endif>Zimbabwe (+263)</option>
                                    </select>
                                    <!-- Add more country codes as needed -->

                                </div>
                                <input type="number"name="phone" id="example-phone" class="form-control" required
                                    placeholder="Phone Number"value="{{ old('name',$account->phone_no) }}">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="example-security-question">Security Question</label>
                            <input type="text" required name="security_question" id="example-security-question"
                                class="form-control" placeholder="Security Question"value="{{ old('name',$account->security_question) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-security-answer">Security Answer</label>
                            <input type="text" required name="security_answer" id="example-security-answer"
                                class="form-control" placeholder="Security Answer"value="{{ old('name',$account->security_answer) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-recovery-email">Recovery Email</label>
                            <input name="recovery_email" id="example-recovery-email" type="email" class="form-control"
                                required placeholder="Recovery Email"value="{{ old('name',$account->recovery_email) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="example-recovery-phone">Recovery Phone Number</label>
                            <div class="input-group">
                                <div class="input-group-prepend" style="width: 20%;">
                                    
                                    <select name="recovery_country_code" class="form-control">
                                        <option value="+1" @if($account->recovery_country_code == '+1') selected @endif>USA (+1)</option>
                                        <option value="+44"@if($account->recovery_country_code == '+44') selected @endif>UK (+44)</option>
                                        <option value="+971"@if($account->recovery_country_code == '+971') selected @endif>UAE (+971)</option>
                                        <option value="+91"@if($account->recovery_country_code == '+91') selected @endif>India (+91)</option>
                                        <option value="+61"@if($account->recovery_country_code == '+61') selected @endif>Australia (+61)</option>
                                        <option value="+93"@if($account->recovery_country_code == '+93') selected @endif>Afghanistan (+93)</option>
                                        <option value="+355"@if($account->recovery_country_code == '+355') selected @endif>Albania (+355)</option>
                                        <option value="+213"@if($account->recovery_country_code == '+213') selected @endif>Algeria (+213)</option>
                                        <option value="+376"@if($account->recovery_country_code == '+376') selected @endif>Andorra (+376)</option>
                                        <option value="+244"@if($account->recovery_country_code == '+244') selected @endif>Angola (+244)</option>
                                        <option value="+54"@if($account->recovery_country_code == '+54') selected @endif>Argentina (+54)</option>
                                        <option value="+374"@if($account->recovery_country_code == '+374') selected @endif>Armenia (+374)</option>
                                        <option value="+61"@if($account->recovery_country_code == '+61') selected @endif>Australia (+61)</option>
                                        <option value="+43"@if($account->recovery_country_code == '+43') selected @endif>Austria (+43)</option>
                                        <option value="+994"@if($account->recovery_country_code == '+994') selected @endif>Azerbaijan (+994)</option>
                                        <option value="+973"@if($account->recovery_country_code == '+973') selected @endif>Bahrain (+973)</option>
                                        <option value="+880"@if($account->recovery_country_code == '+880') selected @endif>Bangladesh (+880)</option>
                                        <option value="+375"@if($account->recovery_country_code == '+375') selected @endif>Belarus (+375)</option>
                                        <option value="+32"@if($account->recovery_country_code == '+32') selected @endif>Belgium (+32)</option>
                                        <option value="+501"@if($account->recovery_country_code == '+501') selected @endif>Belize (+501)</option>
                                        <option value="+229"@if($account->recovery_country_code == '+229') selected @endif>Benin (+229)</option>
                                        <option value="+975"@if($account->recovery_country_code == '+975') selected @endif>Bhutan (+975)</option>
                                        <option value="+591"@if($account->recovery_country_code == '+591') selected @endif>Bolivia (+591)</option>
                                        <option value="+387"@if($account->recovery_country_code == '+387') selected @endif>Bosnia and Herzegovina (+387)</option>
                                        <option value="+267"@if($account->recovery_country_code == '+267') selected @endif>Botswana (+267)</option>
                                        <option value="+55"@if($account->recovery_country_code == '+55') selected @endif>Brazil (+55)</option>
                                        <option value="+673"@if($account->recovery_country_code == '+673') selected @endif>Brunei (+673)</option>
                                        <option value="+359"@if($account->recovery_country_code == '+359') selected @endif>Bulgaria (+359)</option>
                                        <option value="+226"@if($account->recovery_country_code == '+226') selected @endif>Burkina Faso (+226)</option>
                                        <option value="+257"@if($account->recovery_country_code == '+257') selected @endif>Burundi (+257)</option>
                                        <option value="+855"@if($account->recovery_country_code == '+855') selected @endif>Cambodia (+855)</option>
                                        <option value="+237"@if($account->recovery_country_code == '+237') selected @endif>Cameroon (+237)</option>
                                        <option value="+1"@if($account->recovery_country_code == '+1') selected @endif>Canada (+1)</option>
                                        <option value="+238"@if($account->recovery_country_code == '+238') selected @endif>Cape Verde (+238)</option>
                                        <option value="+236"@if($account->recovery_country_code == '+236') selected @endif>Central African Republic (+236)</option>
                                        <option value="+235"@if($account->recovery_country_code == '+235') selected @endif>Chad (+235)</option>
                                        <option value="+56"@if($account->recovery_country_code == '+56') selected @endif>Chile (+56)</option>
                                        <option value="+86"@if($account->recovery_country_code == '+86') selected @endif>China (+86)</option>
                                        <option value="+57"@if($account->recovery_country_code == '+57') selected @endif>Colombia (+57)</option>
                                        <option value="+269"@if($account->recovery_country_code == '+269') selected @endif>Comoros (+269)</option>
                                        <option value="+243"@if($account->recovery_country_code == '+243') selected @endif>Congo (+243)</option>
                                        <option value="+682"@if($account->recovery_country_code == '+682') selected @endif>Cook Islands (+682)</option>
                                        <option value="+506"@if($account->recovery_country_code == '+506') selected @endif>Costa Rica (+506)</option>
                                        <option value="+385"@if($account->recovery_country_code == '+385') selected @endif>Croatia (+385)</option>
                                        <option value="+53"@if($account->recovery_country_code == '+53') selected @endif>Cuba (+53)</option>
                                        <option value="+357"@if($account->recovery_country_code == '+357') selected @endif>Cyprus (+357)</option>
                                        <option value="+420"@if($account->recovery_country_code == '+420') selected @endif>Czech Republic (+420)</option>
                                        <option value="+45"@if($account->recovery_country_code == '+45') selected @endif>Denmark (+45)</option>
                                        <option value="+253"@if($account->recovery_country_code == '+253') selected @endif>Djibouti (+253)</option>
                                        <option value="+593"@if($account->recovery_country_code == '+593') selected @endif>Ecuador (+593)</option>
                                        <option value="+20"@if($account->recovery_country_code == '+20') selected @endif>Egypt (+20)</option>
                                        <option value="+503"@if($account->recovery_country_code == '+503') selected @endif>El Salvador (+503)</option>
                                        <option value="+240"@if($account->recovery_country_code == '+240') selected @endif>Equatorial Guinea (+240)</option>
                                        <option value="+291"@if($account->recovery_country_code == '+291') selected @endif>Eritrea (+291)</option>
                                        <option value="+372"@if($account->recovery_country_code == '+372') selected @endif>Estonia (+372)</option>
                                        <option value="+251"@if($account->recovery_country_code == '+251') selected @endif>Ethiopia (+251)</option>
                                        <option value="+679"@if($account->recovery_country_code == '+679') selected @endif>Fiji (+679)</option>
                                        <option value="+358"@if($account->recovery_country_code == '+358') selected @endif>Finland (+358)</option>
                                        <option value="+33"@if($account->recovery_country_code == '+33') selected @endif>France (+33)</option>
                                        <option value="+241"@if($account->recovery_country_code == '+241') selected @endif>Gabon (+241)</option>
                                        <option value="+220"@if($account->recovery_country_code == '+220') selected @endif>Gambia (+220)</option>
                                        <option value="+995"@if($account->recovery_country_code == '+995') selected @endif>Georgia (+995)</option>
                                        <option value="+49"@if($account->recovery_country_code == '+49') selected @endif>Germany (+49)</option>
                                        <option value="+233"@if($account->recovery_country_code == '+233') selected @endif>Ghana (+233)</option>
                                        <option value="+30"@if($account->recovery_country_code == '+30') selected @endif>Greece (+30)</option>
                                        <option value="+502"@if($account->recovery_country_code == '+502') selected @endif>Guatemala (+502)</option>
                                        <option value="+224"@if($account->recovery_country_code == '+224') selected @endif>Guinea (+224)</option>
                                        <option value="+592"@if($account->recovery_country_code == '+592') selected @endif>Guyana (+592)</option>
                                        <option value="+509"@if($account->recovery_country_code == '+509') selected @endif>Haiti (+509)</option>
                                        <option value="+504"@if($account->recovery_country_code == '+504') selected @endif>Honduras (+504)</option>
                                        <option value="+36"@if($account->recovery_country_code == '+36') selected @endif>Hungary (+36)</option>
                                        <option value="+354"@if($account->recovery_country_code == '+354') selected @endif>Iceland (+354)</option>
                                        <option value="+62"@if($account->recovery_country_code == '+62') selected @endif>Indonesia (+62)</option>
                                        <option value="+98"@if($account->recovery_country_code == '+98') selected @endif>Iran (+98)</option>
                                        <option value="+964"@if($account->recovery_country_code == '+964') selected @endif>Iraq (+964)</option>
                                        <option value="+353"@if($account->recovery_country_code == '+353') selected @endif>Ireland (+353)</option>
                                        <option value="+39"@if($account->recovery_country_code == '+39') selected @endif>Italy (+39)</option>
                                        <option value="+81"@if($account->recovery_country_code == '+81') selected @endif>Japan (+81)</option>
                                        <option value="+962"@if($account->recovery_country_code == '+962') selected @endif>Jordan (+962)</option>
                                        <option value="+7"@if($account->recovery_country_code == '+7') selected @endif>Kazakhstan (+7)</option>
                                        <option value="+254"@if($account->recovery_country_code == '+254') selected @endif>Kenya (+254)</option>
                                        <option value="+965"@if($account->recovery_country_code == '+965') selected @endif>Kuwait (+965)</option>
                                        <option value="+996"@if($account->recovery_country_code == '+996') selected @endif>Kyrgyzstan (+996)</option>
                                        <option value="+856"@if($account->recovery_country_code == '+856') selected @endif>Laos (+856)</option>
                                        <option value="+371"@if($account->recovery_country_code == '+371') selected @endif>Latvia (+371)</option>
                                        <option value="+961"@if($account->recovery_country_code == '+961') selected @endif>Lebanon (+961)</option>
                                        <option value="+266"@if($account->recovery_country_code == '+266') selected @endif>Lesotho (+266)</option>
                                        <option value="+231"@if($account->recovery_country_code == '+231') selected @endif>Liberia (+231)</option>
                                        <option value="+370"@if($account->recovery_country_code == '+370') selected @endif>Lithuania (+370)</option>
                                        <option value="+352"@if($account->recovery_country_code == '+352') selected @endif>Luxembourg (+352)</option>
                                        <option value="+389"@if($account->recovery_country_code == '+389') selected @endif>Macedonia (+389)</option>
                                        <option value="+261"@if($account->recovery_country_code == '+261') selected @endif>Madagascar (+261)</option>
                                        <option value="+265"@if($account->recovery_country_code == '+265') selected @endif>Malawi (+265)</option>
                                        <option value="+60"@if($account->recovery_country_code == '+60') selected @endif>Malaysia (+60)</option>
                                        <option value="+960"@if($account->recovery_country_code == '+960') selected @endif>Maldives (+960)</option>
                                        <option value="+223"@if($account->recovery_country_code == '+223') selected @endif>Mali (+223)</option>
                                        <option value="+356"@if($account->recovery_country_code == '+356') selected @endif>Malta (+356)</option>
                                        <option value="+692"@if($account->recovery_country_code == '+692') selected @endif>Marshall Islands (+692)</option>
                                        <option value="+222"@if($account->recovery_country_code == '+222') selected @endif>Mauritania (+222)</option>
                                        <option value="+230"@if($account->recovery_country_code == '+230') selected @endif>Mauritius (+230)</option>
                                        <option value="+52"@if($account->recovery_country_code == '+52') selected @endif>Mexico (+52)</option>
                                        <option value="+373"@if($account->recovery_country_code == '+373') selected @endif>Moldova (+373)</option>
                                        <option value="+377"@if($account->recovery_country_code == '+377') selected @endif>Monaco (+377)</option>
                                        <option value="+976"@if($account->recovery_country_code == '+976') selected @endif>Mongolia (+976)</option>
                                        <option value="+382"@if($account->recovery_country_code == '+382') selected @endif>Montenegro (+382)</option>
                                        <option value="+212"@if($account->recovery_country_code == '+212') selected @endif>Morocco (+212)</option>
                                        <option value="+258"@if($account->recovery_country_code == '+258') selected @endif>Mozambique (+258)</option>
                                        <option value="+95"@if($account->recovery_country_code == '+95') selected @endif>Myanmar (+95)</option>
                                        <option value="+264"@if($account->recovery_country_code == '+264') selected @endif>Namibia (+264)</option>
                                        <option value="+977"@if($account->recovery_country_code == '+977') selected @endif>Nepal (+977)</option>
                                        <option value="+31"@if($account->recovery_country_code == '+31') selected @endif>Netherlands (+31)</option>
                                        <option value="+64"@if($account->recovery_country_code == '+64') selected @endif>New Zealand (+64)</option>
                                        <option value="+505"@if($account->recovery_country_code == '+505') selected @endif>Nicaragua (+505)</option>
                                        <option value="+227"@if($account->recovery_country_code == '+227') selected @endif>Niger (+227)</option>
                                        <option value="+234"@if($account->recovery_country_code == '+234') selected @endif>Nigeria (+234)</option>
                                        <option value="+47"@if($account->recovery_country_code == '+47') selected @endif>Norway (+47)</option>
                                        <option value="+968"@if($account->recovery_country_code == '+968') selected @endif>Oman (+968)</option>
                                        <option value="+92"@if($account->recovery_country_code == '+92') selected @endif>Pakistan (+92)</option>
                                        <option value="+507"@if($account->recovery_country_code == '+507') selected @endif>Panama (+507)</option>
                                        <option value="+675"@if($account->recovery_country_code == '+675') selected @endif>Papua New Guinea (+675)</option>
                                        <option value="+595"@if($account->recovery_country_code == '+595') selected @endif>Paraguay (+595)</option>
                                        <option value="+51"@if($account->recovery_country_code == '+51') selected @endif>Peru (+51)</option>
                                        <option value="+63"@if($account->recovery_country_code == '+63') selected @endif>Philippines (+63)</option>
                                        <option value="+48"@if($account->recovery_country_code == '+48') selected @endif>Poland (+48)</option>
                                        <option value="+351"@if($account->recovery_country_code == '+351') selected @endif>Portugal (+351)</option>
                                        <option value="+974"@if($account->recovery_country_code == '+974') selected @endif>Qatar (+974)</option>
                                        <option value="+40"@if($account->recovery_country_code == '+40') selected @endif>Romania (+40)</option>
                                        <option value="+7"@if($account->recovery_country_code == '+7') selected @endif>Russia (+7)</option>
                                        <option value="+250"@if($account->recovery_country_code == '+250') selected @endif>Rwanda (+250)</option>
                                        <option value="+966"@if($account->recovery_country_code == '+966') selected @endif>Saudi Arabia (+966)</option>
                                        <option value="+221"@if($account->recovery_country_code == '+221') selected @endif>Senegal (+221)</option>
                                        <option value="+381"@if($account->recovery_country_code == '+381') selected @endif>Serbia (+381)</option>
                                        <option value="+248"@if($account->recovery_country_code == '+248') selected @endif>Seychelles (+248)</option>
                                        <option value="+232"@if($account->recovery_country_code == '+232') selected @endif>Sierra Leone (+232)</option>
                                        <option value="+65"@if($account->recovery_country_code == '+65') selected @endif>Singapore (+65)</option>
                                        <option value="+421"@if($account->recovery_country_code == '+421') selected @endif>Slovakia (+421)</option>
                                        <option value="+386"@if($account->recovery_country_code == '+386') selected @endif>Slovenia (+386)</option>
                                        <option value="+27"@if($account->recovery_country_code == '+27') selected @endif>South Africa (+27)</option>
                                        <option value="+82"@if($account->recovery_country_code == '+82') selected @endif>South Korea (+82)</option>
                                        <option value="+34"@if($account->recovery_country_code == '+34') selected @endif>Spain (+34)</option>
                                        <option value="+94"@if($account->recovery_country_code == '+94') selected @endif>Sri Lanka (+94)</option>
                                        <option value="+249"@if($account->recovery_country_code == '+249') selected @endif>Sudan (+249)</option>
                                        <option value="+597"@if($account->recovery_country_code == '+597') selected @endif>Suriname (+597)</option>
                                        <option value="+268"@if($account->recovery_country_code == '+268') selected @endif>Swaziland (+268)</option>
                                        <option value="+46"@if($account->recovery_country_code == '+46') selected @endif>Sweden (+46)</option>
                                        <option value="+41"@if($account->recovery_country_code == '+41') selected @endif>Switzerland (+41)</option>
                                        <option value="+963"@if($account->recovery_country_code == '+963') selected @endif>Syria (+963)</option>
                                        <option value="+886"@if($account->recovery_country_code == '+886') selected @endif>Taiwan (+886)</option>
                                        <option value="+992"@if($account->recovery_country_code == '+992') selected @endif>Tajikistan (+992)</option>
                                        <option value="+255"@if($account->recovery_country_code == '+255') selected @endif>Tanzania (+255)</option>
                                        <option value="+66"@if($account->recovery_country_code == '+66') selected @endif>Thailand (+66)</option>
                                        <option value="+228"@if($account->recovery_country_code == '+228') selected @endif>Togo (+228)</option>
                                        <option value="+676"@if($account->recovery_country_code == '+676') selected @endif>Tonga (+676)</option>
                                        <option value="+216"@if($account->recovery_country_code == '+216') selected @endif>Tunisia (+216)</option>
                                        <option value="+90"@if($account->recovery_country_code == '+90') selected @endif>Turkey (+90)</option>
                                        <option value="+993"@if($account->recovery_country_code == '+993') selected @endif>Turkmenistan (+993)</option>
                                        <option value="+256"@if($account->recovery_country_code == '+256') selected @endif>Uganda (+256)</option>
                                        <option value="+380"@if($account->recovery_country_code == '+380') selected @endif>Ukraine (+380)</option>
                                        <option value="+971"@if($account->recovery_country_code == '+971') selected @endif>United Arab Emirates (+971)</option>
                                        <option value="+44"@if($account->recovery_country_code == '+44') selected @endif>United Kingdom (+44)</option>
                                        <option value="+1"@if($account->recovery_country_code == '+1') selected @endif>United States (+1)</option>
                                        <option value="+598"@if($account->recovery_country_code == '+598') selected @endif>Uruguay (+598)</option>
                                        <option value="+998"@if($account->recovery_country_code == '+998') selected @endif>Uzbekistan (+998)</option>
                                        <option value="+58"@if($account->recovery_country_code == '+58') selected @endif>Venezuela (+58)</option>
                                        <option value="+84"@if($account->recovery_country_code == '+84') selected @endif>Vietnam (+84)</option>
                                        <option value="+967"@if($account->recovery_country_code == '+967') selected @endif>Yemen (+967)</option>
                                        <option value="+260"@if($account->recovery_country_code == '+260') selected @endif>Zambia (+260)</option>
                                        <option value="+263"@if($account->recovery_country_code == '+263') selected @endif>Zimbabwe (+263)</option>
                                    </select>
                                    <!-- Add more country codes as needed -->

                                </div>
                                <input type="number"name="recovery_phone" id="example-recovery-phone"
                                    class="form-control" required placeholder="Recovery Phone Number"value="{{ old('name',$account->recovery_phone_no) }}">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="customFile">Account Logo</label>
                            <div class="custom-file">
                                <input name="logo"type="file" class="custom-file-input" id="customFile"onchange="previewImage(event)"">
                                <label class="custom-file-label" for="customFile">Choose Image</label>
                            </div>
                            <div class="mt-3">
                                <img id="imagePreview" src="{{ getFirstMediaUrl($account,$account->logoCollection) }}" alt="Image Preview"
                                    class="img-thumbnail"
                                    style="@if (getFirstMediaUrl($account,$account->logoCollection)) display: block; @else display: none; @endif max-width: 200px; height: auto;">
                            </div>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="active"id="customCheck1" @if($account->active == '1') checked @endif>
                            <label class="custom-control-label" for="customCheck1">Active</label>
                        </div>
                        <button type="submit" class="btn mb-2 btn-outline-primary"id="btn-outline-primary"
                            style="margin-top: 10px;">Update</button>
                    </form>
                </div> <!-- /.col -->

            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
    function previewImage(event) {
        var input = event.target;
        var reader = new FileReader();

        reader.onload = function() {
            var img = document.getElementById('imagePreview');
            img.src = reader.result;
            img.style.display = 'block'; // Show the image
        };

        if (input.files && input.files[0]) {
            reader.readAsDataURL(input.files[0]); // Read the uploaded image
        }
    }
</script>
@endpush
