@extends('layouts.main')

@section('title', 'Cab Rates')

@section('css')
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <style>
        .parsley-errors-list {
            list-style: none;
            padding-left: 0;
            margin: 0;
            color: red;
            font-size: 14px;
        }

        .parsley-errors-list li {
            display: inline;
        }

        .tabs {
            display: flex;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .tab {
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-bottom: none;
            background-color: #f1f1f1;
            margin-right: 5px;
        }

        .tab.active {
            background-color: white;
            border-top: 2px solid #007bff;
        }

        .tab-content {
            border: 1px solid #ccc;
            padding: 20px;
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .save-btn,
        .add-btn,
        .remove-btn {
            padding: 5px 10px;
            margin-top: 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .save-btn {
            background-color: #007bff;
            color: white;
        }

        .add-btn {
            background-color: #28a745;
            color: white;
        }

        .remove-btn {
            background-color: #dc3545;
            color: white;
        }
    </style>
    <!-- DataTables CSS -->
@endsection

@section('css-bottom')
    <link rel="stylesheet" href="{{ URL::asset('build/css/uikit.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Cab Rates</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Cab Rates</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @include('layouts.message')
        </div>
        @php
            $airport = getAirportList();
            $typeList = getCabType();
        @endphp
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ @$priceDetails ? 'Update' : 'Add' }} Cab Rates</h5>
                </div>
                <div class="card-body">
                    <div class="tabs">
                        <div class="tab" onclick="showTab(0)">Airport Pick Up</div>
                        <div class="tab" onclick="showTab(1)">Airport Drop</div>
                        <div class="tab active" onclick="showTab(2)">In-City Rides</div>
                    </div>

                    <div class="tab-content " id="pickup-tab">
                        <div class="row">
                            <div class="col-sm-3 mb-3">
                                <label for="">From</label>
                                <select class="form-control" name="search_from" id="search_from" required
                                    data-parsley-required-message="Please select airport.">
                                    <option value="">Select Airport</option>
                                    @if ($airport != null && count($airport) > 0)
                                        @foreach ($airport as $key => $list)
                                            <option value="{{ $key + 1 }}">
                                                {{ $list ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-sm-3 mb-3">
                                <label for="">To</label>
                                <select class="form-control" name="search_to" id="search_to" required
                                    data-parsley-required-message="Please select city.">
                                    <option value="">Select City Name</option>
                                    @if (isset($city) && count($city) > 0)
                                        @foreach ($city as $list)
                                            <option value="{{ $list->id ?? '' }}">
                                                {{ $list->name ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for=""></label>
                                <div class="mt-1">
                                    <input type="hidden" id="tab" value="0">
                                    <button type="button" class="btn btn-primary search-airpot-pickup-btn">Search</button>
                                </div>
                            </div>
                        </div>
                        <form class="airportPickupPercentage" id="airport-pickup-percentage">
                            @csrf
                            <div class="row">
                                <div class="col-sm-3 mb-3">
                                    <label for="">Airport Pickup %</label>
                                    <input type="number" class="form-control" name="airport_pickup_percentage" required
                                        value="{{isset($airportPickupPercentage->value) ? $airportPickupPercentage->value : ''}}"
                                        data-parsley-required-message="Please enter percentage"
                                        data-parsley-type="number"
                                        data-parsley-min="-100"
                                        data-parsley-min-message="Percentage must be greater than or equal to -100">
                                </div>
                                <div class="col-sm-3 mb-3">
                                    <label for=""></label>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary save-airport-pickup-percentage-btn">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form id="formTabOne" method="post">
                            @csrf
                            <div class="mb-3 text-end">
                                <button type="submit" class="save-btn">Save</button>
                            </div>
                            <div class="row">
                                <input type="hidden" name="tab" value="{{ App\Enums\Type::AIRPORT_PICKUP }}"
                                    id="">
                                <div id="pickupContainer">
                                    @include('settings.partials.airport_pickup', [
                                        'airPortPickup' => $airPortPickup,
                                        'city' => $city,
                                        'airport' => $airport,
                                        'typeList' => $typeList,
                                    ])
                                </div>
                         <div class="mb-3 text-start">
                                <button type="submit" class="save-btn">Save</button>
                            </div>
                                @if (isset($airPortPickupCount) && $airPortPickupCount > 9)
                                    <div id="loadMoreWrapper1" style="text-align: center; padding: 20px;">
                                        <button id="loadMoreBtn1" class="btn btn-primary loadMoreBtn" data-tab="0"
                                            type="button">Load
                                            More</button>
                                        <div id="loader1" style="display: none; margin-top: 10px;">Loading more...</div>
                                    </div>
                                @endif

                            </div>
                            <div class=" TabOneInput"></div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary addNewTab1 btn-sm"><i
                                            class="feather icon-plus"></i></button>
                                </div>
                            </div>
                            {{-- <button type="submit" class="save-btn">Save</button> --}}
                        </form>
                    </div>

                    <div class="tab-content " id="drop-tab">
                        <div class="row">
                            <div class="col-sm-3 mb-3">
                                <label for="">From</label>
                                <select class="form-control" name="drop_city_from" id="drop_city_from" required
                                    data-parsley-required-message="Please select city.">
                                    <option value="">Select City Name</option>
                                    @if (isset($city) && count($city) > 0)
                                        @foreach ($city as $list)
                                            <option value="{{ $list->id ?? '' }}">
                                                {{ $list->name ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="">To</label>
                                <select class="form-control" name="drop_city_to" id="drop_city_to" required
                                    data-parsley-required-message="Please select airport.">
                                    <option value="">Select Airport</option>
                                    @if ($airport != null && count($airport) > 0)
                                        @foreach ($airport as $key => $list)
                                            <option value="{{ $key + 1 }}">
                                                {{ $list ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for=""></label>
                                <div class="mt-1">
                                    <input type="hidden" id="tab" value="1">
                                    <button type="button" class="btn btn-primary search-airport-drop-btn">Search</button>
                                </div>
                            </div>
                        </div>
                        <form class="formTabTwo" id="formTabTwo" method="post">
                            @csrf
                            <div class="mb-3 text-end">
                                <button type="submit" class="save-btn">Save</button>
                            </div>
                            <div class="row">
                                <input type="hidden" name="tab" value="{{ App\Enums\Type::AIRPORT_DROP }}"
                                    id="">
                                <div id="dropContainer">
                                    @include('settings.partials.airport_drop', [
                                        'airPortDrop' => $airPortDrop,
                                        'city' => $city,
                                        'airport' => $airport,
                                        'typeList' => $typeList,
                                    ])
                                </div>
                                 <div class="mb-3 text-start">
                                <button type="submit" class="save-btn">Save</button>
                            </div>
                                @if (isset($airPortDropCount) && $airPortDropCount > 9)
                                    <div id="loadMoreWrapper2" style="text-align: center; padding: 20px;">
                                        <button id="loadMoreBtn2" class="btn btn-primary loadMoreBtn" data-tab="1"
                                            type="button">Load
                                            More</button>
                                        <div id="loader2" style="display: none; margin-top: 10px;">Loading more...</div>
                                    </div>
                                @endif
                            </div>
                            <div class=" TabTwoInput"></div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary addNewTab2 btn-sm"><i
                                            class="feather icon-plus"></i></button>
                                </div>
                            </div>
                            {{-- <button type="submit" class="save-btn">Save</button> --}}
                        </form>
                    </div>
                    <div class="tab-content active" id="city-tab">
                    <div class="row">
                            <div class="col-sm-3 mb-3">
                                <label for="">From</label>
                                <select class="form-control" name="city_ride_from" id="city_ride_from" required
                                    data-parsley-required-message="Please select city.">
                                    <option value="">Select City Name</option>
                                    @if (isset($city) && count($city) > 0)
                                        @foreach ($city as $list)
                                            <option value="{{ $list->id ?? '' }}">
                                                {{ $list->name ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="">To</label>
                                <select class="form-control" name="city_ride_to" id="city_ride_to" required
                                    data-parsley-required-message="Please select airport.">
                                    <option value="">Select City Name</option>
                                    @if (isset($city) && count($city) > 0)
                                        @foreach ($city as $list)
                                            <option value="{{ $list->id ?? '' }}">
                                                {{ $list->name ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for=""></label>
                                <div class="mt-1">
                                    <input type="hidden" id="tab" value="2">
                                    <button type="button" class="btn btn-primary search-city-ride-btn">Search</button>
                                </div>
                            </div>
                        </div>
                        <form class="formTabThree" id="formTabThree" method="post">
                            @csrf
                            <div class="mb-3 text-end">
                                <button type="submit" class="save-btn">Save</button>
                            </div>
                            <div class="row">
                                <input type="hidden" name="tab" value="{{ App\Enums\Type::CITY_RIDES }}"
                                    id="">
                                <div id="cityRideContainer">
                                    @include('settings.partials.city_ride_rows', [
                                        'cityRide' => $cityRide,
                                        'city' => $city,
                                    ])
                                </div>
                                @if (isset($cityRideCount) && $cityRideCount > 9)
                                    <div id="loadMoreWrapper3" style="text-align: center; padding: 20px;">
                                        <button id="loadMoreBtn3" class="btn btn-primary loadMoreBtn" data-tab="2"
                                            type="button">Load
                                            More</button>
                                        <div id="loader3" style="display: none; margin-top: 10px;">Loading more...</div>
                                    </div>
                                @endif
                            </div>
                            <div class=" TabThreeInput"></div>
                             <div class="mb-3 text-start">
                                <button type="submit" class="save-btn">Save</button>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary addNewTab3 btn-sm"><i
                                            class="feather icon-plus"></i></button>
                                </div>
                            </div>
                            {{-- <button type="submit" class="save-btn">Save</button> --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div id="ajaxLoader"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
     background: rgba(255,255,255,0.6); z-index: 9999; text-align: center;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <div class="spinner-border text-primary" role="status"></div>
            <div style="margin-top: 10px;">Processing...</div>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(function() {

            $('#formTabOne').parsley();
            $('.formTabTwo').parsley();
            $('.formTabThree').parsley();
            $('#airport-pickup-percentage').parsley();
            $('.cabRateTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.setting.cabRate.list') }}",
                    type: "POST",
                    data: function(data) {
                        data.search = $('input[type="search"]').val();
                        data._token = "{{ csrf_token() }}";
                    }
                },
                order: [
                    [1, 'DESC']
                ],
                pageLength: 10,
                searching: true,
                columns: [{
                    data: 'tab',
                    name: 'tab',
                    render: function(data, type, row) {
                        var html = '';
                        if (row.tab == 1) {
                            html = 'AIRPORT PICKUP';
                        } else if (row.tab == 2) {
                            html = 'AIRPORT DROP';
                        } else if (row.tab == 3) {
                            html = 'CITY RIDES';
                        }
                        return html;
                    }
                }, {
                    data: 'default',
                    name: 'default',
                    render: function(data, type, row) {
                        var city_name = '';
                        if (row.tab == 1 || row.tab == 2) {
                            if (row.from == 1) {
                                city_name = 'Dabolim Goa Airport (GOI)';
                            }
                            if (row.from == 2) {
                                city_name = 'Manohar International Airport (GOX)';
                            }
                        } else {
                            city_name = row.city_from ? row.city_from.name : '';
                        }
                        return city_name;
                    }
                }, {
                    data: 'to',
                    name: 'to',
                    render: function(data, type, row) {
                        return row.city_to ? row.city_to.name : '';
                    }
                }, {
                    data: 'cab_id',
                    name: 'cab_id',
                    render: function(data, type, row) {
                        $type = 'Hatchback';
                        if (row.cab_id == 2) {
                            $type = 'Sedan';
                        } else if (row.cab_id == 3) {
                            $type = 'SUV';
                        }
                        return $type;
                    }
                }, {
                    data: 'base_fare',
                    name: 'base_fare',
                    render: function(data, type, row) {
                        return row.base_fare ? getCurrencySign() + row.base_fare : '--';
                    }
                }, {
                    data: 'id',
                    name: 'id',
                    render: function(data, type, row) {
                        return "fdfdf";
                    }
                }]
            });
            // preview front_side_aadhar
            $('#front_side_aadhar').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#imagePreviewFrontSide');
                $('#imagePreviewFrontSide').addClass('imagePreview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };

                    reader.readAsDataURL(file);
                }
            });

            // preview back_side_aadhar
            $('#back_side_aadhar').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#imagePreviewBackSide');
                $('#imagePreviewBackSide').addClass('imagePreview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };

                    reader.readAsDataURL(file);
                }
            });

            // preview company_license
            $('#company_license').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#imagePreviewCompanyLicense');
                $('#imagePreviewCompanyLicense').addClass('imagePreview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };

                    reader.readAsDataURL(file);
                }
            });

            // preview aggrement
            $('#aggrement').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#imagePreviewAggrement');
                $('#imagePreviewAggrement').addClass('imagePreview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        imagePreview.show();
                    };

                    reader.readAsDataURL(file);
                }
            });
            // delete
            $(document).on('click', '.deleteBaseFare', function(e) {
                e.preventDefault();
                var url = $(this).data('url');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't delete this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "GET",
                            success: function(response) {
                                Swal.fire("Success!", "Base fare has been deleted.",
                                    "success");
                                location.reload();
                            },
                            error: function() {
                                Swal.fire("Error!", "Something went wrong.", "error");
                            }
                        });
                    }
                });
            });


        });
    </script>
    <script>
        var tabId = 0;

        function showTab(index) {
            const tabs = document.querySelectorAll(".tab");
            const contents = document.querySelectorAll(".tab-content");
            tabId = index;
            tabs.forEach(tab => tab.classList.remove("active"));
            contents.forEach(content => content.classList.remove("active"));

            tabs[index].classList.add("active");
            contents[index].classList.add("active");
        }

        function addEntry(tabId, defaultFrom) {
            const container = document.querySelector(`#${tabId} .entry-container`);
            const row = document.createElement('div');
            row.className = 'row';

            const isCityTab = tabId === 'city-tab';

            row.innerHTML = `
                <div class="col-sm-3 mb-3">
            <select class="form-control"><option>${defaultFrom}</option></select>
            </div>
            <div class="col-sm-3 mb-3">
            <select class="form-control"><option>City Name</option></select>
            </div>
            <div class="col-sm-3 mb-3">
            <select class="form-control">
            <option>Hatchback</option>
            <option>Sedan</option>
            <option>SUV</option>
            </select>
             </div>
            <div class="col-sm-2 mb-3">
            <input type="text" class="form-control" placeholder="Base Fare">
             </div>
              </div>
            <div class="col-sm-1">
            <button class="remove-btn" onclick="removeEntry(this)"><i class="feather icon-trash-2"></i></button></div>
        `;

            container.appendChild(row);
        }

        function removeEntry(button) {
            button.parentElement.remove();
        }

        window.onload = () => {
            for (let i = 0; i < 3; i++) addEntry('pickup-tab', 'Dabolim Goa Airport (GOI)');
            for (let i = 0; i < 3; i++) addEntry('drop-tab', 'Manohar International Airport (GOX)');
            for (let i = 0; i < 3; i++) addEntry('city-tab', 'City Name');
        };

        $(function() {
            var count = 1;
            $(document).on('click', '.addNewTab1', function(e) {
                e.preventDefault();

                var html = ` <div class="row add_${count}">
                    <div class="col-sm-2 mb-3">
                                <label for="">From</label>
                                <select class="form-control" name="from[]" required data-parsley-required-message="Please select airport.">
                                    <option value="">Select Airport</option>
                                    @if ($airport != null && count($airport) > 0)
                                    @foreach ($airport as $key => $list)
                                    <option value="{{ $key + 1 }}">{{ $list ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">To</label>
                                <select class="form-control" name="to[]" required data-parsley-required-message="Please select city.">
                                    <option value="">Select City Name</option>
                                    @if (isset($city) && count($city) > 0)
                                    @foreach ($city as $list)
                                    <option value="{{ $list->id ?? '' }}">{{ $list->name ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            {{-- <div class="col-sm-2 mb-3">
                                <label for="">Cab Type</label>
                                <select class="form-control" name="cab_id[]" required data-parsley-required-message="Please select cab type.">
                                    <option value="">Select Cab Type</option>
                                    @if (count($typeList) > 0)
                                    @foreach ($typeList as $key => $list)
                                    <option value="{{ $key + 1 }}" {{ @$priceDetails->cab_type == $key + 1 ? 'selected' : '' }}>
                                        {{ $list ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div> --}}
                            <div class="col-sm-1 mb-3">
                                <label for="">Base Km.</label>
                                <input type="number" class="form-control" name="base_km[]" placeholder="Base km" required data-parsley-required-message="Please enter base km.">
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">Hatchback Base Fare</label>
                                <input type="number" class="form-control" name="hatchback_base_fare[]" placeholder="Hatchback base Fare" min="1" required data-parsley-required-message="Please enter hatchback base fare price.">
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">Sedan Base Fare</label>
                                <input type="number" class="form-control" name="sedan_base_fare[]" placeholder="Sedan base Fare" min="1" required data-parsley-required-message="Please enter sedan base fare price.">
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">Suv Base Fare</label>
                                <input type="number" class="form-control" name="suv_base_fare[]" placeholder="Suv base Fare" min="1" required data-parsley-required-message="Please enter suv base fare price.">
                            </div>
                        <div class="mt-4 col-sm-1" style="text-align: right">
                        <button class="btn btn-danger removeTabHtmlOne btn-sm " type="button"  data-id="${count}">
                            <i class="feather icon-trash-2"></i>
                        </button>
                    </div>
                </div>`;
                // $(".addNewTab1").remove();
                $(".removeTabHtmlOne").removeClass("d-none");
                $('.TabOneInput').append(html);
                count++;
            });

            $(document).on('click', '.removeTabHtmlOne', function(e) {
                var id = $(this).data('id');
                $('.add_' + id).remove();
            });
            $(document).on('click', '.addNewTab2', function(e) {
                e.preventDefault();
                var html = ` <div class="row add_two_${count}">
                               <div class="col-sm-2 mb-3">
                                <label for="">From</label>
                                 <select class="form-control" name="from[]" required data-parsley-required-message="Please select city.">
                                    <option value="">Select City Name</option>
                                    @if (isset($city) && count($city) > 0)
                                    @foreach ($city as $list)
                                    <option value="{{ $list->id ?? '' }}">{{ $list->name ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">To</label>
                                <select class="form-control" name="to[]" required data-parsley-required-message="Please select airport.">
                                    <option value="">Select Airport</option>
                                    @if ($airport != null && count($airport) > 0)
                                    @foreach ($airport as $key => $list)
                                    <option value="{{ $key + 1 }}">{{ $list ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            {{-- <div class="col-sm-2 mb-3">
                                <label for="">Cab Type</label>
                                <select class="form-control" name="cab_id[]" required data-parsley-required-message="Please select cab type.">
                                    <option value="">Select Cab Type</option>
                                    @if (count($typeList) > 0)
                                    @foreach ($typeList as $key => $list)
                                    <option value="{{ $key + 1 }}" {{ @$priceDetails->cab_type == $key + 1 ? 'selected' : '' }}>
                                        {{ $list ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div> --}}
                            <div class="col-sm-1 mb-3">
                                <label for="">Base Km.</label>
                                <input type="number" class="form-control" name="base_km[]" placeholder="Base km" min="1" required data-parsley-required-message="Please enter base km.">
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">Hatchback Base Fare</label>
                                <input type="number" class="form-control" name="hatchback_base_fare[]" placeholder="Hatchback base Fare" min="1" required data-parsley-required-message="Please enter hatchback base fare price.">
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">Sedan Base Fare</label>
                                <input type="number" class="form-control" name="sedan_base_fare[]" placeholder="Sedan base Fare" min="1" required data-parsley-required-message="Please enter sedan base fare price.">
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">Suv Base Fare</label>
                                <input type="number" class="form-control" name="suv_base_fare[]" placeholder="Suv base Fare" min="1" required data-parsley-required-message="Please enter suv base fare price.">
                            </div>
                                <div class="mt-4 col-sm-1" style="text-align: right">
                                    <button class="btn btn-danger removeTabHtmlTwo btn-sm " data-id="${count}"><i class="feather icon-trash-2"></i></button>
                                    </div>
                                    </div>`;
                // <button class="btn btn-primary addNewTab2 btn-sm" type="button"><i class="feather icon-plus"></i></button>
                // $(".addNewTab2").remove();
                $(".removeTabHtmlTwo").removeClass("d-none");
                $('.TabTwoInput').append(html);
                count++;
            });

            $(document).on('click', '.removeTabHtmlTwo', function(e) {
                var id = $(this).data('id');
                $('.add_two_' + id).remove();
            });

            $(document).on('click', '.addNewTab3', function(e) {
                e.preventDefault();
                var html = ` <div class="row add_three_${count}">
                                <div class="col-sm-2 mb-3">
                                <label for="">From</label>
                                <select class="form-control" name="from[]" required data-parsley-required-message="Please select airport.">
                                    <option value="">Select City Name</option>
                                    @if (isset($city) && count($city) > 0)
                                    @foreach ($city as $list)
                                    <option value="{{ $list->id ?? '' }}">{{ $list->name ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">To</label>
                                <select class="form-control" name="to[]" required data-parsley-required-message="Please select city.">
                                    <option value="">Select City Name</option>
                                    @if (isset($city) && count($city) > 0)
                                    @foreach ($city as $list)
                                    <option value="{{ $list->id ?? '' }}">{{ $list->name ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            {{-- <div class="col-sm-2 mb-3">
                                <label for="">Cab Type</label>
                                <select class="form-control" name="cab_id[]" required data-parsley-required-message="Please select cab type.">
                                    <option value="">Select Cab Type</option>
                                    @if (count($typeList) > 0)
                                    @foreach ($typeList as $key => $list)
                                    <option value="{{ $key + 1 }}" {{ @$priceDetails->cab_type == $key + 1 ? 'selected' : '' }}>
                                        {{ $list ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div> --}}
                            <div class="col-sm-1 mb-3">
                                <label for="">Base Km.</label>
                                <input type="number" class="form-control" name="base_km[]" placeholder="Base km" required data-parsley-required-message="Please enter base km.">
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">Hatchback Base Fare</label>
                                <input type="number" class="form-control" name="hatchback_base_fare[]" placeholder="Hatchback base Fare" min="1" required data-parsley-required-message="Please enter hatchback base fare price.">
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">Sedan Base Fare</label>
                                <input type="number" class="form-control" name="sedan_base_fare[]" placeholder="Sedan base Fare" min="1" required data-parsley-required-message="Please enter sedan base fare price.">
                            </div>
                            <div class="col-sm-2 mb-3">
                                <label for="">Suv Base Fare</label>
                                <input type="number" class="form-control" name="suv_base_fare[]" placeholder="Suv base Fare" min="1" required data-parsley-required-message="Please enter suv base fare price.">
                            </div>
                                <div class="mt-4 col-sm-1" style="text-align: right">
                                    <button class="btn btn-danger  removeTabHtmlThree btn-sm" data-id="${count}"><i class="feather icon-trash-2"></i></button>

                                </div>
                            </div>`;
                // $(".addNewTab3").remove();
                $(".removeTabHtmlThree").removeClass("d-none");
                $('.TabThreeInput').append(html);
                count++;
            });

            $(document).on('click', '.removeTabHtmlThree', function(e) {
                var id = $(this).data('id');
                $('.add_three_' + id).remove();
            });

            $(document).on('click', '.removeTabHtmlOneEdit', function(e) {
                e.preventDefault();
                var url = $(this).data('url');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't delete this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "GET",
                            success: function(response) {
                                Swal.fire("Success!", "Deleted successfully.",
                                    "success");
                                location.reload();
                            },
                            error: function() {
                                Swal.fire("Error!", "Something went wrong.", "error");
                            }
                        });
                    }
                });
            });

            $('#formTabOne,#formTabTwo,#formTabThree').on('submit', function(e) {
                e.preventDefault();

                var url = "{{ route('admin.setting.cabRate.store') }}";
                var formData = new FormData(this);

                $('#ajaxLoader').show(); // Show loader

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#ajaxLoader').hide(); // Hide loader
                        if (response.success) {
                            toastr.success(response.message || 'Submitted successfully!');
                            location.reload();
                        } else {
                            toastr.error(response.message || 'Something went wrong!');
                        }
                    },
                    error: function(xhr) {
                        $('#ajaxLoader').hide(); // Hide loader
                        toastr.error('Something went wrong!');
                    }
                });
            });

            $('#airport-pickup-percentage').on('submit',function (e) {
                e.preventDefault();

                var url = "{{ route('admin.setting.cabRate.airportPickupPercentageCalculation') }}";
                var formData = new FormData($('#airport-pickup-percentage')[0]);

                $('#ajaxLoader').show(); // Show loader

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#ajaxLoader').hide(); // Hide loader
                        if (response.success) {
                            toastr.success(response.message || 'Submitted successfully!');
                            location.reload();
                        } else {
                            toastr.error(response.message || 'Something went wrong!');
                        }
                    },
                    error: function(xhr) {
                        $('#ajaxLoader').hide(); // Hide loader
                        toastr.error('Something went wrong!');
                    }
                });
            });

        });

        // let page = 2;
        // let loading = false;
        // let nextPage = true;

        // window.onscroll = function() {
        //     if (loading || !nextPage) return;

        //     if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
        //         loading = true;
        //         if (tabId == 0) {
        //             document.getElementById('loader1').style.display = 'block';
        //         } else if (tabId == 1) {
        //             document.getElementById('loader2').style.display = 'block';
        //         } else if (tabId == 2) {
        //             document.getElementById('loader3').style.display = 'block';
        //         }

        //         fetch(`{{ route('admin.setting.cabRate.index') }}?page=` + page + `&tab=` + tabId, {
        //                 headers: {
        //                     'X-Requested-With': 'XMLHttpRequest'
        //                 }
        //             })
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (data.html.trim() !== '') {
        //                     if (tabId == 0) {
        //                         document.getElementById('pickupContainer').insertAdjacentHTML('beforeend', data
        //                             .html);
        //                     } else if (tabId == 1) {
        //                         document.getElementById('dropContainer').insertAdjacentHTML('beforeend', data.html);
        //                     } else if (tabId == 2) {
        //                         document.getElementById('cityRideContainer').insertAdjacentHTML('beforeend', data
        //                             .html);
        //                     }
        //                     page = data.next_page ?? page;
        //                     nextPage = data.next_page !== null;
        //                 } else {
        //                     nextPage = false;
        //                 }

        //                 if (tabId == 0) {
        //                     document.getElementById('loader1').style.display = 'none';
        //                 } else if (tabId == 1) {
        //                     document.getElementById('loader2').style.display = 'none';
        //                 } else if (tabId == 2) {
        //                     document.getElementById('loader3').style.display = 'none';
        //                 }
        //                 loading = false;
        //             })
        //             .catch(err => {
        //                 console.error('Error loading more data:', err);
        //                 loading = false;
        //             });
        //     }
        // };
    </script>

    <script>
        const pageMap = {
            0: 2,
            1: 2,
            2: 2
        }; // Keep track of page per tab
        const loadingMap = {
            0: false,
            1: false,
            2: false
        }; // Loading state per tab
        const nextPageMap = {
            0: true,
            1: true,
            2: true
        }; // next page flag per tab

        $('.loadMoreBtn').on('click', function() {
            const tabId = $(this).data('tab');

            if (loadingMap[tabId] || !nextPageMap[tabId]) return;

            var search_from = '';
            var search_to = '';
            if(tabId == 0){
                 search_from = $('#search_from').val();
                 search_to = $('#search_to').val();
            }
            else if(tabId  == 1){
                search_from = $('#drop_city_from').val();
                search_to = $('#drop_city_to').val();
            }
            else if(tabId  == 2){
                search_from = $('#city_ride_from').val();
                search_to = $('#city_ride_to').val();
            }

            loadingMap[tabId] = true;
            $(`#loader${tabId + 1}`).show();

            $.ajax({
                url: '{{ route('admin.setting.cabRate.index') }}',
                method: 'GET',
                data: {
                    page: pageMap[tabId],
                    tab: tabId,
                    search_from: search_from,
                    search_to: search_to
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    if (data.html && $.trim(data.html) !== '') {
                        if (tabId === 0) {
                            $('#pickupContainer').append(data.html);
                        } else if (tabId === 1) {
                            $('#dropContainer').append(data.html);
                        } else if (tabId === 2) {
                            $('#cityRideContainer').append(data.html);
                        }

                        pageMap[tabId] = data.next_page ?? pageMap[tabId];
                        nextPageMap[tabId] = data.next_page !== null;

                        if (!nextPageMap[tabId]) {
                            $(`#loadMoreBtn${tabId + 1}`).hide();
                        }
                    } else {
                        nextPageMap[tabId] = false;
                        $(`#loadMoreBtn${tabId + 1}`).hide();
                    }

                    $(`#loader${tabId + 1}`).hide();
                    loadingMap[tabId] = false;
                },
                error: function(err) {
                    console.error('Error loading more data:', err);
                    $(`#loader${tabId + 1}`).hide();
                    loadingMap[tabId] = false;
                }
            });
        });

        // Search button click
        $(document).on('click', '.search-airpot-pickup-btn', function () {
            const tabId = 0;

            // Reset pagination state for active tab
            pageMap[tabId] = 2;
            nextPageMap[tabId] = true;
            loadingMap[tabId] = false;

            let container = '#pickupContainer';

            // Clear old data
            $(container).html('');

            var search_from = $('#search_from').val();

            var search_to = $('#search_to').val();

            loadSearchData(tabId, container, search_from, search_to);

        });

        $(document).on('click', '.search-airport-drop-btn', function () {
            const tabId = 1;

            // Reset pagination state for active tab
            pageMap[tabId] = 2;
            nextPageMap[tabId] = true;
            loadingMap[tabId] = false;

            // Clear the container before loading new data
            let container = '#dropContainer';

            // Clear old data
            $(container).html('');

            var search_from = $('#drop_city_from').val();

            var search_to = $('#drop_city_to').val();

            loadSearchData(tabId, container, search_from, search_to);

        });

        $(document).on('click', '.search-city-ride-btn', function () {
            const tabId = 2;

            // Reset pagination state for active tab
            pageMap[tabId] = 2;
            nextPageMap[tabId] = true;
            loadingMap[tabId] = false;

            // Clear the container before loading new data
            let container = '#cityRideContainer';

            // Clear old data
            $(container).html('');

            var search_from = $('#city_ride_from').val();

            var search_to = $('#city_ride_to').val();

            loadSearchData(tabId, container, search_from, search_to);

        });

        function loadSearchData(tabId, container, search_from, search_to)
        {
            // Load first page with filters
            $.ajax({
                url: '{{ route('admin.setting.cabRate.index') }}',
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    page: 1,
                    tab: tabId,
                    search_from: search_from,
                    search_to: search_to
                },
                success: function (data) {
                    $(container).html(data.html);

                    let loadMoreBtn = `#loadMoreBtn${tabId + 1}`;
                    if (data.next_page) $(loadMoreBtn).show();
                    else $(loadMoreBtn).hide();
                }
            });
        }
    </script>


@endsection
