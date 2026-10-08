@extends('layouts.main')

@section('title', 'Ad Pricing')
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.advertisements.index') }}">Advertisements</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Ad Pricing</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Ad Pricing</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @include('layouts.message')
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Add Advertisement Pricing</h5>
                </div>
                <form method="POST" action="{{ route('admin.advertisements.screenPrice.storeUpdate') }}">
                    @csrf
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Ad Screen</th>
                                    <th>Ad Price Per Day</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (isset($priceLists) && count($priceLists) > 0)
                                    @foreach ($priceLists as $key => $item)
                                        @if (isset($item->screen))
                                            <tr>
                                                <td>
                                                    <h5>{{ $item->title ?? '' }}</h5>
                                                    <input type="hidden" name="title[]" value="{{ $item->title ?? '' }}" class="title">
                                                    <input type="hidden" name="screen[]" value="{{ $item->screen ?? '' }}"
                                                        id="homeScreenPrice">
                                                </td>
                                                <td>
                                                    <div class="input-group ">
                                                        <span class="input-group-text" id="basic-addon2">₹</span>
                                                        <input class="form-control" type="number" name="value[]" id="homeScreenPrice"
                                                            value="{{ $item->price_per_day ?? '' }}" placeholder="Enter price"
                                                            data-price="">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection