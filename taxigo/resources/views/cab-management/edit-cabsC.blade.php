@extends('layouts.main')

@section('title', 'Edit Cab Details')
@section('breadcrumb-item', 'Cab Management')

@section('breadcrumb-item-active', 'Edit Cab Details')

@section('css')

<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/dropzone.min.css') }}">

@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5>Edit Cab Details</h5>
      </div>
      <div class="card-body">
        <form>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="cabStand">Cab Zone</label>
                <select class="form-control"
                  name="choices-multiple-remove-button"
                  id="choices-multiple-remove-button"
                  multiple>
                  <option value="panaji" hidden>Select cab zone</option>
                  <option value="panaji">Panaji</option>
                  <option value="calangute">Calangute</option>
                  <option value="baga">Baga</option>
                  <option value="anjuna">Anjuna</option>
                  <option value="vagator">Vagator</option>
                  <option value="colva">Colva</option>
                  <option value="candolim">Candolim</option>
                  <option value="chapora">Chapora</option>
                  <option value="dona-paula">Dona Paula</option>
                  <option value="arambol">Arambol</option>
                  <option value="agonda">Agonda</option>
                  <option value="palolem">Palolem</option>
                  <option value="morjim">Morjim</option>
                  <option value="ashwem">Ashwem</option>
                  <option value="mandrem">Mandrem</option>
                  <option value="miramar">Miramar</option>
                  <option value="betalbatim">Betalbatim</option>
                  <option value="sinquerim">Sinquerim</option>
                  <option value="bogmalo">Bogmalo</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="cabName">Cab Number</label>
                <input type="text" class="form-control" id="cabName" placeholder="Enter cab number">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="cabType">Cab Type</label>
                <select class="form-select" id="cabType">
                  <option hidden>Select cab type</option>
                  <option>Hatchback</option>
                  <option>Sedan</option>
                  <option>SUV</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="cabModel">Cab Model</label>
                <select class="form-select" id="cabModel">
                  <option hidden>Select cab model</option>
                  <option>Baleno, Swift or similar</option>
                  <option>Dzire, Etios or similar</option>
                  <option>Xylo, Ertiga or similar</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="cabModel">Model Name</label>
                <select class="form-select" id="cabModel">
                  <option hidden>Select cab model name</option>
                  <option>Baleno</option>
                  <option>Swift</option>
                  <option>Dzire</option>
                  <option>Etios</option>
                  <option>Xylo</option>
                  <option>Ertiga</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="cabModel">Color</label>
                <select class="form-select" id="cabModel">
                  <option hidden>Select cab color</option>
                  <option>White</option>
                  <option>Black</option>
                  <option>Gray</option>
                  <option>Navy Blue</option>
                  <option>Blue</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="seatNumber">No. Of Seats</label>
                <select class="form-select" id="seatNumber">
                  <option hidden>Select no. of seats</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="fuelType">Fuel Type</label>
                <select class="form-select" id="fuelType">
                  <option hidden>Select fuel type</option>
                  <option>CNG</option>
                  <option>Petrol</option>
                  <option>Diesel</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5>Price Details <span class="cancellation-notice">( Cancellation: Free till 1 hours of departure )</span></h5>
      </div>
      <div class="card-body">
        <form>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="baseFare">Base Fare</label>
                <select class="form-select" id="baseFare">
                  <option hidden>Select base fare</option>
                  <option>Rs. 30</option>
                  <option>Rs. 35</option>
                  <option>Rs. 40</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="kmNumber">No. of Kms Included</label>
                <select class="form-select" id="kmNumber">
                  <option>40 kms</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="addKm">Additional KM Charges</label>
                <select class="form-select" id="addKm">
                  <option hidden>Select additional km charges</option>
                  <option>Rs. 20</option>
                  <option>Rs. 25</option>
                  <option>Rs. 30</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="waitingCharge">Waiting Charges ( After 30 mins )</label>
                <select class="form-select" id="waitingCharge">
                  <option>Rs. 100/30 mins</option>
                </select>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5>Cab Documents</h5>
      </div>
      <div class="card-body">
        <form>
          <div class="row">
            <div class="col-12">
              <label class="form-label" for="registerCertificate">Registration Certificate</label>
            </div>
            <div class="col-md-6">
              <label class="card-upload-box" for="register-front">
                <p>(Frontside of Registration Certificate)</p>
                <p>Drag & Drop file here or click to browse</p>
              </label>
              <input type="file" class="d-none" id="register-front" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="card-upload-box" for="register-back">
                <p>(Backside of Registration Certificate)</p>
                <p>Drag & Drop file here or click to browse</p>
              </label>
              <input type="file" class="d-none" id="register-back" accept="image/*">
            </div>
            <!-- <div class="col-md-6">
              <div class="my-3">
                <label class="form-label" for="travelPermit">Travel Permit</label>
                <input type="file" class="form-control" id="travelPermit" accept="image/*">
              </div>
            </div> -->
            <div class="col-12">
              <div class="my-3">
                <label class="form-label" for="insurance">Insurance</label>
                <input type="file" class="form-control" id="insurance" accept="image/*">
              </div>
            </div>
            <!-- <div class="col-md-6">
              <div class="my-3">
                <label class="form-label" for="taxCertificate">Tax</label>
                <input type="file" class="form-control" id="taxCertificate" accept="image/*">
              </div>
            </div>
            <div class="col-md-6">
              <div class="my-3">
                <label class="form-label" for="fitnessCertificate">Fitness Certificate</label>
                <input type="file" class="form-control" id="fitnessCertificate" accept="image/*">
              </div>
            </div> -->
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5>Driver Details</h5>
        <button id="addNewDriver" class="btn btn-sm btn-light-secondary fw-bold"> <i data-feather="plus"></i> Add New Driver</button>
      </div>
      <div class="card-body">
        <form>
          <div class="row">
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="driverName">Driver Name</label>
                <input type="text" class="form-control" id="driverName" placeholder="Enter Driver name">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="driverNumber">Driver Mobile</label>
                <input type="number" class="form-control" id="driverNumber" placeholder="Enter driver number">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="bankName">Bank Name</label>
                <input type="text" class="form-control" id="bankName" placeholder="Enter bank name">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="branchName">Branch Name</label>
                <input type="text" class="form-control" id="branchName" placeholder="Enter branch name">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="accountName">Account Holder Name</label>
                <input type="text" class="form-control" id="accountName" placeholder="Enter account holder name">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="accountNumber">Account Number</label>
                <input type="number" class="form-control" id="accountNumber" placeholder="Enter account number">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="ifscCode">IFSC Code</label>
                <input type="text" class="form-control" id="ifscCode" placeholder="Enter IFSC code">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="upiNumber">UPI ID</label>
                <input type="text" class="form-control" id="upiNumber" placeholder="Enter upi id">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="licenseNumber">Driving License Number</label>
                <input type="number" class="form-control" id="licenseNumber" placeholder="Enter driving license number" required>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="aadharNumber">Aadhar Card Number</label>
                <input type="number" class="form-control" id="aadharNumber" placeholder="Enter aadhar card number">
              </div>
            </div>
            <div class="col-12">
              <div class="my-3">
                <h5>Driving License</h5>
              </div>
            </div>
            <div class="col-md-6">
              <label class="card-upload-box" for="license-front">
                <p>(Frontside of Driving License)</p>
                <p>Drag & Drop file here or click to browse</p>
              </label>
              <input type="file" class="d-none" id="license-front" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="card-upload-box" for="license-back">
                <p>(Backside of Driving License)</p>
                <p>Drag & Drop file here or click to browse</p>
              </label>
              <input type="file" class="d-none" id="license-back" accept="image/*">
            </div>

            <div class="col-md-6">
              <div class="my-3">
                <label class="form-label" for="aadhar-front">Frontside of Aadhar Card</label>
                <input type="file" class="form-control" id="aadhar-front" accept="image/*">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mt-3 mb-5">
                <label class="form-label" for="aadhar-back">Backside of Aadhar Card</label>
                <input type="file" class="form-control" id="aadhar-back" accept="image/*">
              </div>
            </div>
            <hr>
            <div class="col-12">
              <div class="d-flex justify-content-start align-items-center">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="group4" value="" id="driver-assigned">
                  <label class="form-label mb-0" for="driver-assigned">
                    <h5 class="mb-0">Assign Driver to Cab </h5>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5>Driver Details</h5>
        <button class="btn btn-sm btn-light-danger"> <i data-feather="minus"></i> Remove Driver</button>
      </div>
      <div class="card-body">
        <form>
          <div class="row">
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="driverName">Driver Name</label>
                <input type="text" class="form-control" id="driverName" placeholder="Enter Driver name">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="driverNumber">Driver Mobile</label>
                <input type="number" class="form-control" id="driverNumber" placeholder="Enter driver number">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="bankName">Bank Name</label>
                <input type="text" class="form-control" id="bankName" placeholder="Enter bank name">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="branchName">Branch Name</label>
                <input type="text" class="form-control" id="branchName" placeholder="Enter branch name">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="accountName">Account Holder Name</label>
                <input type="text" class="form-control" id="accountName" placeholder="Enter account holder name">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="accountNumber">Account Number</label>
                <input type="number" class="form-control" id="accountNumber" placeholder="Enter account number">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="ifscCode">IFSC Code</label>
                <input type="text" class="form-control" id="ifscCode" placeholder="Enter IFSC code">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="upiNumber">UPI ID</label>
                <input type="text" class="form-control" id="upiNumber" placeholder="Enter upi id">
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="licenseNumber">Driving License Number</label>
                <input type="number" class="form-control" id="licenseNumber" placeholder="Enter driving license number" required>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="aadharNumber">Aadhar Card Number</label>
                <input type="number" class="form-control" id="aadharNumber" placeholder="Enter aadhar card number">
              </div>
            </div>
            <div class="col-12">
              <div class="my-3">
                <h5>Driving License</h5>
              </div>
            </div>
            <div class="col-md-6">
              <label class="card-upload-box" for="license-front">
                <p>(Frontside of Driving License)</p>
                <p>Drag & Drop file here or click to browse</p>
              </label>
              <input type="file" class="d-none" id="license-front" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="card-upload-box" for="license-back">
                <p>(Backside of Driving License)</p>
                <p>Drag & Drop file here or click to browse</p>
              </label>
              <input type="file" class="d-none" id="license-back" accept="image/*">
            </div>

            <div class="col-md-6">
              <div class="my-3">
                <label class="form-label" for="aadhar-front">Frontside of Aadhar Card</label>
                <input type="file" class="form-control" id="aadhar-front" accept="image/*">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mt-3 mb-5">
                <label class="form-label" for="aadhar-back">Backside of Aadhar Card</label>
                <input type="file" class="form-control" id="aadhar-back" accept="image/*">
              </div>
            </div>
            <hr>
            <div class="col-12">
              <div class="d-flex justify-content-start align-items-center">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="group4" value="" id="driver-assigned">
                  <label class="form-label mb-0" for="driver-assigned">
                    <h5 class="mb-0">Assign Driver to Cab </h5>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card">
      <div class="card-body text-end">
        <a href="{{asset('/cab-management/view-cabs')}}" type="submit" class="btn btn-primary">Submit</a>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- [Page Specific JS] start -->
<script src="{{ URL::asset('build/js/plugins/dropzone-amd-module.min.js') }}"></script>

<!-- Dropdown with Search -->
<script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>

<script>
  // Select Multiple JS
  document.addEventListener('DOMContentLoaded', function() {
    var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
      removeItemButton: true
    });
  });
</script>
<!-- [Page Specific JS] end -->
@endsection