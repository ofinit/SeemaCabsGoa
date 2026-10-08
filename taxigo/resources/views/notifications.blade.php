@extends('layouts.main')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('build/css/plugins/style.css') }}">
@endsection

@section('content')

<!-- [ Main Content ] start -->
<div class="row">
  <div class="col-12">
    <div class="card table-card">
      <div class="card-header">
        <h5>Notifications</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover" id="pc-dt-simple">
            <tbody>
              <tr>
                <td>
                  <div class="avtar avtar-l">
                    <img src="{{ URL::asset('build/images/logo-dark.svg') }}" alt="user-image" class="avtar avtar-l object-fit-contain border" />
                  </div>
                </td>
                <td>
                  {Date Time}
                </td>
                <td>
                  <h6 class="mb-0">🎉 New Booking</h6>
                </td>
                <td>
                  <p class="mb-0">From: {Location} To: {Location} Pickup Date: {Date} Pickup Time: {Time} Customer: {Name} {Mobile Number}</p>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="avtar avtar-l">
                    <img src="{{ URL::asset('build/images/logo-dark.svg') }}" alt="user-image" class="avtar avtar-l object-fit-contain border" />
                  </div>
                </td>
                <td>
                  {Date Time}
                </td>
                <td>
                  <h6 class="mb-0">🚨 Ride Cancelled</h6>
                </td>
                <td>
                  <p class="mb-0 text-wrap">Customer {Name} {Mobile Number} cancelled Ride From: {Location} To: {Location} at {Date Time} with {Cab Number} {Driver Name} {Mobile Number}</p>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="avtar avtar-l">
                    <img src="{{ URL::asset('build/images/logo-dark.svg') }}" alt="user-image" class="avtar avtar-l object-fit-contain border" />
                  </div>
                </td>
                <td>
                  {Date Time}
                </td>
                <td>
                  <h6 class="mb-0">🚕 Ride Completed</h6>
                </td>
                <td>
                  <p class="mb-0 text-wrap">Customer {Name} {Mobile Number} ride completed From: {Location} To: {Location} at {Date Time} with {Cab Number} {Driver Name} {Mobile Number}</p>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="avtar avtar-l">
                    <img src="{{ URL::asset('build/images/logo-dark.svg') }}" alt="user-image" class="avtar avtar-l object-fit-contain border" />
                  </div>
                </td>
                <td>
                  {Date Time}
                </td>
                <td>
                  <h6 class="mb-0">🚨 SOS Alert</h6>
                </td>
                <td>
                  <p class="mb-0 text-wrap"><span class="text-danger fw-bolder">EMERGENCY!</span> Customer {Name} {Mobile Number} triggered SOS at {Date Time} Trip Details: From: {Location} To: {Location} Cab Details: {Cab Number} {Driver Name} {Mobile Number}</p>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="avtar avtar-l">
                    <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}" alt="user-image" class="avtar avtar-l object-fit-contain border" />
                  </div>
                </td>
                <td>
                  {Date Time}
                </td>
                <td>
                  <h6 class="mb-0"><img src="{{ URL::asset('build/icons/other-icons/ad-icon.png') }}" alt=""> Review New Ad!</h6>
                </td>
                <td>
                  <p class="mb-0 text-wrap">Advertiser Details: {Name} {Mobile Number} Start: {Date} {Time} End: {Date} {Time}</p>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="avtar avtar-l">
                    <img src="{{ URL::asset('build/images/ad-images/box-ad-1.jpg') }}" alt="user-image" class="avtar avtar-l object-fit-contain border" />
                  </div>
                </td>
                <td>
                  {Date Time}
                </td>
                <td>
                  <h6 class="mb-0"><img src="{{ URL::asset('build/icons/other-icons/ad-icon.png') }}" alt=""> Ad Expired!</h6>
                </td>
                <td>
                  <p class="mb-0 text-wrap">Customer Ad Has Expired. Renew it! Advertiser Details: {Name} {Mobile Number}</p>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="avtar avtar-l">
                    <img src="{{ URL::asset('build/images/documents/taxi-driver.png') }}" alt="user-image" class="avtar avtar-l object-fit-cover border" />
                  </div>
                </td>
                <td>
                  {Date Time}
                </td>
                <td>
                  <h6 class="mb-0">🚕 Driver Reached Location</h6>
                </td>
                <td>
                  <p class="mb-0 text-wrap">Customer: {Name} {Mobile Number} Cab Details: {Cab Number} {Driver Name} {Mobile Number}</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('scripts')
<script type="module">
  import {
    DataTable
  } from "/build/js/plugins/module.js"
  window.dt = new DataTable("#pc-dt-simple");
</script>
@endsection