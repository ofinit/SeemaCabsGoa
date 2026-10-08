@extends('layouts.main')

@section('title', 'Account Ads')
@section('breadcrumb-item', 'Advertisements')

@section('breadcrumb-item-active', 'Account Ads')

@section('css')

<!-- GLightBox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<!-- Popup Animation -->
<link href="{{ URL::asset('build/css/plugins/animate.min.css') }}" rel="stylesheet" type="text/css">

<!-- File Upload CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dropzone.min.css') }}">

<!-- Date Range CSS -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datepicker-bs5.min.css') }}">

<!-- Time Picker -->
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/flatpickr.min.css') }}">

@endsection

@section('css-bottom')
<link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Box Ads Upload</h5>
            </div>
            <div class="card-body">
                <div class="select-advertiser-section">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label" for="boxAdvertisers">Select Advertisers</label>
                                <select data-trigger class="form-select" name="boxAdvertisers" id="boxAdvertisers">
                                    <option value="">Select</option>
                                    <option>John Doe</option>
                                    <option>Jane Smith</option>
                                    <option>Alice Johnson</option>
                                    <option>Robert Brown</option>
                                    <option>Emily White</option>
                                    <option>Chris Green</option>
                                    <option>Laura Black</option>
                                    <option>Michael King</option>
                                    <option>Susan Harris</option>
                                    <option>Kevin Wright</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="file-upload-section">
                    <label class="form-label mb-2">Upload Banner Image</label>
                    <form action="/build/json/file-upload.php" class="dropzone">
                        <div class="fallback">
                            <input name="file" type="file" accept="images/*">
                        </div>
                    </form>
                </div>
                <div class="url-section mt-3">
                    <label class="form-label mb-2" for="adsUrl">Banner URL / WhatsApp Link / Telegram Link / Social
                        Media Link</label>
                    <input class="form-control" type="url" name="adsUrl" id="adsUrl" placeholder="Enter your url">
                </div>

                <hr class="my-5">

                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Audience</h5>
                        <p>Define who you want to see your ads.</p>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="selectGender">Select Gender</label>
                            <select data-trigger class="form-select" name="selectGender" id="selectGender">
                                <option value="">Select Gender</option>
                                <option>All</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                            <h6 class="mt-2 text-primary">Reach up to 150 Users</h6>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="selectCountry">Select Country</label>
                            <select data-trigger class="form-select" name="selectCountry" id="selectCountry">
                                <option value="">Select Country</option>
                                <option value="AF">All</option>
                                <option value="AF">Afghanistan</option>
                                <option value="AL">Albania</option>
                                <option value="DZ">Algeria</option>
                                <option value="AS">American Samoa</option>
                                <option value="AD">Andorra</option>
                                <option value="AO">Angola</option>
                                <option value="AI">Anguilla</option>
                                <option value="AQ">Antarctica</option>
                                <option value="AG">Antigua and Barbuda</option>
                                <option value="AR">Argentina</option>
                                <option value="AM">Armenia</option>
                                <option value="AW">Aruba</option>
                                <option value="AU">Australia</option>
                                <option value="AT">Austria</option>
                                <option value="AZ">Azerbaijan</option>
                                <option value="BS">Bahamas</option>
                                <option value="BH">Bahrain</option>
                                <option value="BD">Bangladesh</option>
                                <option value="BB">Barbados</option>
                                <option value="BY">Belarus</option>
                                <option value="BE">Belgium</option>
                                <option value="BZ">Belize</option>
                                <option value="BJ">Benin</option>
                                <option value="BM">Bermuda</option>
                                <option value="BT">Bhutan</option>
                                <option value="BO">Bolivia</option>
                                <option value="BA">Bosnia and Herzegovina</option>
                                <option value="BW">Botswana</option>
                                <option value="BR">Brazil</option>
                                <option value="BN">Brunei Darussalam</option>
                                <option value="BG">Bulgaria</option>
                                <option value="BF">Burkina Faso</option>
                                <option value="BI">Burundi</option>
                                <option value="KH">Cambodia</option>
                                <option value="CM">Cameroon</option>
                                <option value="CA">Canada</option>
                                <option value="CV">Cape Verde</option>
                                <option value="KY">Cayman Islands</option>
                                <option value="CF">Central African Republic</option>
                                <option value="TD">Chad</option>
                                <option value="CL">Chile</option>
                                <option value="CN">China</option>
                                <option value="CO">Colombia</option>
                                <option value="KM">Comoros</option>
                                <option value="CG">Congo</option>
                                <option value="CD">Congo, The Democratic Republic of the</option>
                                <option value="CR">Costa Rica</option>
                                <option value="CI">Côte d'Ivoire</option>
                                <option value="HR">Croatia</option>
                                <option value="CU">Cuba</option>
                                <option value="CY">Cyprus</option>
                                <option value="CZ">Czech Republic</option>
                                <option value="DK">Denmark</option>
                                <option value="DJ">Djibouti</option>
                                <option value="DM">Dominica</option>
                                <option value="DO">Dominican Republic</option>
                                <option value="EC">Ecuador</option>
                                <option value="EG">Egypt</option>
                                <option value="SV">El Salvador</option>
                                <option value="GQ">Equatorial Guinea</option>
                                <option value="ER">Eritrea</option>
                                <option value="EE">Estonia</option>
                                <option value="SZ">Eswatini</option>
                                <option value="ET">Ethiopia</option>
                                <option value="FJ">Fiji</option>
                                <option value="FI">Finland</option>
                                <option value="FR">France</option>
                                <option value="GA">Gabon</option>
                                <option value="GM">Gambia</option>
                                <option value="GE">Georgia</option>
                                <option value="DE">Germany</option>
                                <option value="GH">Ghana</option>
                                <option value="GR">Greece</option>
                                <option value="GD">Grenada</option>
                                <option value="GT">Guatemala</option>
                                <option value="GN">Guinea</option>
                                <option value="GW">Guinea-Bissau</option>
                                <option value="GY">Guyana</option>
                                <option value="HT">Haiti</option>
                                <option value="HN">Honduras</option>
                                <option value="HU">Hungary</option>
                                <option value="IS">Iceland</option>
                                <option value="IN">India</option>
                                <option value="ID">Indonesia</option>
                                <option value="IR">Iran</option>
                                <option value="IQ">Iraq</option>
                                <option value="IE">Ireland</option>
                                <option value="IL">Israel</option>
                                <option value="IT">Italy</option>
                                <option value="JM">Jamaica</option>
                                <option value="JP">Japan</option>
                                <option value="JO">Jordan</option>
                                <option value="KZ">Kazakhstan</option>
                                <option value="KE">Kenya</option>
                                <option value="KI">Kiribati</option>
                                <option value="KP">Korea (North)</option>
                                <option value="KR">Korea (South)</option>
                                <option value="KW">Kuwait</option>
                                <option value="KG">Kyrgyzstan</option>
                                <option value="LA">Laos</option>
                                <option value="LV">Latvia</option>
                                <option value="LB">Lebanon</option>
                                <option value="LS">Lesotho</option>
                                <option value="LR">Liberia</option>
                                <option value="LY">Libya</option>
                                <option value="LI">Liechtenstein</option>
                                <option value="LT">Lithuania</option>
                                <option value="LU">Luxembourg</option>
                                <option value="MG">Madagascar</option>
                                <option value="MW">Malawi</option>
                                <option value="MY">Malaysia</option>
                                <option value="MV">Maldives</option>
                                <option value="ML">Mali</option>
                                <option value="MT">Malta</option>
                                <option value="MH">Marshall Islands</option>
                                <option value="MR">Mauritania</option>
                                <option value="MU">Mauritius</option>
                                <option value="MX">Mexico</option>
                                <option value="FM">Micronesia</option>
                                <option value="MD">Moldova</option>
                                <option value="MC">Monaco</option>
                                <option value="MN">Mongolia</option>
                                <option value="ME">Montenegro</option>
                                <option value="MA">Morocco</option>
                                <option value="MZ">Mozambique</option>
                                <option value="MM">Myanmar</option>
                                <option value="NA">Namibia</option>
                                <option value="NR">Nauru</option>
                                <option value="NP">Nepal</option>
                                <option value="NL">Netherlands</option>
                                <option value="NZ">New Zealand</option>
                                <option value="NI">Nicaragua</option>
                                <option value="NE">Niger</option>
                                <option value="NG">Nigeria</option>
                                <option value="NO">Norway</option>
                                <option value="OM">Oman</option>
                                <option value="PK">Pakistan</option>
                                <option value="PW">Palau</option>
                                <option value="PS">Palestine</option>
                                <option value="PA">Panama</option>
                                <option value="PG">Papua New Guinea</option>
                                <option value="PY">Paraguay</option>
                                <option value="PE">Peru</option>
                                <option value="PH">Philippines</option>
                                <option value="PL">Poland</option>
                                <option value="PT">Portugal</option>
                                <option value="QA">Qatar</option>
                                <option value="RO">Romania</option>
                                <option value="RU">Russia</option>
                                <option value="RW">Rwanda</option>
                                <option value="WS">Samoa</option>
                                <option value="SM">San Marino</option>
                                <option value="ST">Sao Tome and Principe</option>
                                <option value="SA">Saudi Arabia</option>
                                <option value="SN">Senegal</option>
                                <option value="RS">Serbia</option>
                                <option value="SC">Seychelles</option>
                                <option value="SL">Sierra Leone</option>
                                <option value="SG">Singapore</option>
                                <option value="SK">Slovakia</option>
                                <option value="SI">Slovenia</option>
                                <option value="SB">Solomon Islands</option>
                                <option value="SO">Somalia</option>
                                <option value="ZA">South Africa</option>
                                <option value="ES">Spain</option>
                                <option value="LK">Sri Lanka</option>
                                <option value="SD">Sudan</option>
                                <option value="SR">Suriname</option>
                                <option value="SE">Sweden</option>
                                <option value="CH">Switzerland</option>
                                <option value="SY">Syria</option>
                                <option value="TW">Taiwan</option>
                                <option value="TJ">Tajikistan</option>
                                <option value="TZ">Tanzania</option>
                                <option value="TH">Thailand</option>
                                <option value="TG">Togo</option>
                                <option value="TO">Tonga</option>
                                <option value="TT">Trinidad and Tobago</option>
                                <option value="TN">Tunisia</option>
                                <option value="TR">Turkey</option>
                                <option value="TM">Turkmenistan</option>
                                <option value="TV">Tuvalu</option>
                                <option value="UG">Uganda</option>
                                <option value="UA">Ukraine</option>
                                <option value="AE">United Arab Emirates</option>
                                <option value="GB">United Kingdom</option>
                                <option value="US">United States</option>
                                <option value="UY">Uruguay</option>
                                <option value="UZ">Uzbekistan</option>
                                <option value="VU">Vanuatu</option>
                                <option value="VE">Venezuela</option>
                                <option value="VN">Vietnam</option>
                                <option value="YE">Yemen</option>
                                <option value="ZM">Zambia</option>
                                <option value="ZW">Zimbabwe</option>
                            </select>
                            <h6 class="mt-2 text-primary">Reach up to 150 Users</h6>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="selectState">Select State</label>
                            <select data-trigger class="form-select" name="selectState" id="selectState">
                                <option value="">Select State</option>
                                <option value="AF">All</option>
                                <option value="AP">Andhra Pradesh</option>
                                <option value="AR">Arunachal Pradesh</option>
                                <option value="AS">Assam</option>
                                <option value="BR">Bihar</option>
                                <option value="CT">Chhattisgarh</option>
                                <option value="GA">Goa</option>
                                <option value="GJ">Gujarat</option>
                                <option value="HR">Haryana</option>
                                <option value="HP">Himachal Pradesh</option>
                                <option value="JH">Jharkhand</option>
                                <option value="KA">Karnataka</option>
                                <option value="KL">Kerala</option>
                                <option value="MP">Madhya Pradesh</option>
                                <option value="MH">Maharashtra</option>
                                <option value="MN">Manipur</option>
                                <option value="ML">Meghalaya</option>
                                <option value="MZ">Mizoram</option>
                                <option value="NL">Nagaland</option>
                                <option value="OR">Odisha</option>
                                <option value="PB">Punjab</option>
                                <option value="RJ">Rajasthan</option>
                                <option value="SK">Sikkim</option>
                                <option value="TN">Tamil Nadu</option>
                                <option value="TG">Telangana</option>
                                <option value="TR">Tripura</option>
                                <option value="UP">Uttar Pradesh</option>
                                <option value="UT">Uttarakhand</option>
                                <option value="WB">West Bengal</option>
                                <option value="AN">Andaman and Nicobar Islands</option>
                                <option value="CH">Chandigarh</option>
                                <option value="DH">Dadra and Nagar Haveli and Daman and Diu</option>
                                <option value="DL">Delhi</option>
                                <option value="JK">Jammu and Kashmir</option>
                                <option value="LA">Ladakh</option>
                                <option value="LD">Lakshadweep</option>
                                <option value="PY">Puducherry</option>
                            </select>
                            <h6 class="mt-2 text-primary">Reach up to 150 Users</h6>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="selectLiveLocation">Select Live Location</label>
                            <select data-trigger class="form-select" name="selectLiveLocation" id="selectLiveLocation">
                                <option value="">Select Live Location</option>
                                <option value="AF">All</option>
                                <option value="AP">Andhra Pradesh</option>
                                <option value="AR">Arunachal Pradesh</option>
                                <option value="AS">Assam</option>
                                <option value="BR">Bihar</option>
                                <option value="CT">Chhattisgarh</option>
                                <option value="GA">Goa</option>
                                <option value="GJ">Gujarat</option>
                                <option value="HR">Haryana</option>
                                <option value="HP">Himachal Pradesh</option>
                                <option value="JH">Jharkhand</option>
                                <option value="KA">Karnataka</option>
                                <option value="KL">Kerala</option>
                                <option value="MP">Madhya Pradesh</option>
                                <option value="MH">Maharashtra</option>
                                <option value="MN">Manipur</option>
                                <option value="ML">Meghalaya</option>
                                <option value="MZ">Mizoram</option>
                                <option value="NL">Nagaland</option>
                                <option value="OR">Odisha</option>
                                <option value="PB">Punjab</option>
                                <option value="RJ">Rajasthan</option>
                                <option value="SK">Sikkim</option>
                                <option value="TN">Tamil Nadu</option>
                                <option value="TG">Telangana</option>
                                <option value="TR">Tripura</option>
                                <option value="UP">Uttar Pradesh</option>
                                <option value="UT">Uttarakhand</option>
                                <option value="WB">West Bengal</option>
                                <option value="AN">Andaman and Nicobar Islands</option>
                                <option value="CH">Chandigarh</option>
                                <option value="DH">Dadra and Nagar Haveli and Daman and Diu</option>
                                <option value="DL">Delhi</option>
                                <option value="JK">Jammu and Kashmir</option>
                                <option value="LA">Ladakh</option>
                                <option value="LD">Lakshadweep</option>
                                <option value="PY">Puducherry</option>
                            </select>
                            <h6 class="mt-2 text-primary">150 Users have enabaled on live location</h6>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Budget & Schedule</h5>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="selectBudget">Budget</label>
                            <select data-trigger class="form-select" name="selectBudget" id="selectBudget">
                                <option value="">Select Budget</option>
                                <option>Daily Budget</option>
                                <option>Monthly Budget</option>
                            </select>
                        </div>
                    </div> -->
                    <!-- <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label" for="selectBudget">Per Day Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="text" class="form-control" placeholder="Rate" aria-label="Amount (to the nearest dollar)">
                                <span class="input-group-text">INR</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <h5 class="form-label">Schedule</h5>
                    </div> -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label" for="selectBudget">Start Date</label>
                        <input type="text" class="form-control" id="boxAdStartDate" placeholder="Select date">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label" for="selectBudget">Time</label>
                        <div class="input-group timepicker">
                            <input class="form-control" id="boxAdStartTime" placeholder="Select time" type="text">
                            <span class="input-group-text">
                                <i class="feather icon-clock"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label" for="selectBudget">End Date</label>
                        <input type="text" class="form-control" id="boxAdEndDate" placeholder="Select date">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label" for="selectBudget">Time</label>
                        <div class="input-group timepicker">
                            <input class="form-control" id="boxAdEndTime" placeholder="Select time" type="text">
                            <span class="input-group-text">
                                <i class="feather icon-clock"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-12 mt-5">
                        <h6>Total Amount</h6>
                        <p>Rs. 1700</p>
                    </div>
                </div>
                <div class="text-end m-t-20">
                    <button class="btn btn-primary">Submit Ad</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>View Box Ads Details</h5>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="boxAdDetails" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Advertiser</th>
                                <th>Ad Image</th>
                                <th>Banner URL</th>
                                <th>Budget</th>
                                <th>Budget Amount</th>
                                <th>Start Date & Time</th>
                                <th>End Date & Time</th>
                                <th>Gender</th>
                                <th>Country</th>
                                <th>State</th>
                                <th>Live Location</th>
                                <th>No. of Clicks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel1.com"
                                        target="_blank">https://www.examplehotel1.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel2.com"
                                        target="_blank">https://www.examplehotel2.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel3.com"
                                        target="_blank">https://www.examplehotel3.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel4.com"
                                        target="_blank">https://www.examplehotel4.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel5.com"
                                        target="_blank">https://www.examplehotel5.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel6.com"
                                        target="_blank">https://www.examplehotel6.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel7.com"
                                        target="_blank">https://www.examplehotel7.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel8.com"
                                        target="_blank">https://www.examplehotel8.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel9.com"
                                        target="_blank">https://www.examplehotel9.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>
                                    <div class="ad-photo">
                                        <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox"
                                            data-glightbox="type: image">
                                            <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}"
                                                alt="image" />
                                        </a>
                                    </div>
                                </td>
                                <td><a href="https://www.examplehotel10.com"
                                        target="_blank">https://www.examplehotel10.com</a></td>
                                <td>Daily Budget</td>
                                <td>Rs.55</td>
                                <td>10-12-2024 12:00 AM</td>
                                <td>31-12-2024 12:00 PM</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                                <td>Goa</td>
                                <td>345</td>
                                <td>
                                    <a href="#!" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="modal"
                                        data-bs-target="#advertiserDetails"><i class="feather icon-eye"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-success me-1"><i
                                            class="feather icon-edit"></i></a>
                                    <a href="#!" class="btn btn-sm btn-light-danger sa-bs-error-ico"><i
                                            class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Model -->
<div class="modal fade" id="advertiserDetails" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">View Advertiser </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- <div class="row gy-1">
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Advertiser Name</h6>
                        <p class="cab-model-location">Panaji</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Budget</h6>
                        <p class="cab-model-location">Daily Budget</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Budget Amount</h6>
                        <p class="cab-model-location">Rs.55</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Start Date & Time</h6>
                        <p class="cab-model-location">10-12-2024 12:00 AM</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">End Date & Time</h6>
                        <p class="cab-model-location">31-12-2024 12:00 PM</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Gender</h6>
                        <p class="cab-model-location">Male</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Country</h6>
                        <p class="cab-model-location">India</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">State</h6>
                        <p class="cab-model-location">Goa</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Live Location</h6>
                        <p class="cab-model-location">Goa</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">No. of Clicks</h6>
                        <p class="cab-model-location">320</p>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="mb-1">Banner URL</h6>
                        <a href="#" class="cab-model-location">https://www.examplehotel1.com</a>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-1">Uploaded Image</h6>
                        <div class="document-container">
                            <div class="main-photo img-box">
                                <a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" class="glightbox" data-title="Insurance" data-description='<div><a href="{{ URL::asset('build/images/documents/dummy.jpg') }}" download class="btn btn-primary download-btn">Download</a></div>'>
                                    <img src="{{ URL::asset('build/images/documents/dummy.jpg') }}" alt="image" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="dt-responsive">
                    <table id="adViewDetails" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>Advertisers</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Gender</th>
                                <th>Country</th>
                                <th>State</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                            <tr>
                                <td>Company Name</td>
                                <td>Warner Doe</td>
                                <td>user.kjm@gmail.com</td>
                                <td>+91 98765 43210</td>
                                <td>Male</td>
                                <td>India</td>
                                <td>Goa</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Type Here -- Model Footer -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<!-- SweetAlert JS -->
<script src="{{ URL::asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>

<!-- DataTable JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

<!-- GLightBox -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<!-- File Upload JS -->
<script src="{{ URL::asset('build/js/plugins/dropzone-amd-module.min.js') }}"></script>

<!-- Date Range JS -->
<script src="{{ URL::asset('build/js/plugins/datepicker-full.min.js') }}"></script>

<!-- Time Picker -->
<script src="{{ URL::asset('build/js/plugins/flatpickr.min.js') }}"></script>

<!-- Dropdown with Search -->
<script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>

<script>
    var table1 = $('#boxAdDetails').DataTable();
    var table2 = $('#adViewDetails').DataTable();

    // GLightBox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        width: "90vw",
        height: "90vh"
    });

    // Delete Button Sweet Alert
    document.querySelectorAll('.sa-bs-error-ico').forEach(function (element) {
        element.addEventListener('click', function () {
            Swal.fire({
                icon: 'error',
                title: 'Are You Sure!',
                showCancelButton: true,
                confirmButtonText: 'Yes, Proceed',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            })
        });
    });

    // Select with Search
    document.addEventListener('DOMContentLoaded', function () {
        var element1 = document.querySelector('#boxAdvertisers');

        new Choices(element1, {
            searchPlaceholderValue: 'Search Advertisers'
        });
    });

    // Select Date
    const boxAdStartDate = new Datepicker(document.querySelector('#boxAdStartDate'), {
        buttonClass: 'btn'
    });

    const boxAdEndDate = new Datepicker(document.querySelector('#boxAdEndDate'), {
        buttonClass: 'btn'
    });

    // Select Time
    document.querySelector('#boxAdStartTime').flatpickr({
        enableTime: true,
        noCalendar: true
    });

    document.querySelector('#boxAdEndTime').flatpickr({
        enableTime: true,
        noCalendar: true
    });
</script>
<!-- [Page Specific JS] end -->
@endsection