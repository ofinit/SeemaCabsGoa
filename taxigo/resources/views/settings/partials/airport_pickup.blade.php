 @if (isset($airPortPickup) && count($airPortPickup) > 0)
     @foreach ($airPortPickup as $itemKey => $item)
         <div class="row d-flex one_edit_{{ $item->id }}">
             <div class="col-sm-2 mb-3">
                 <input type="hidden" name="edit_id[]" value="{{ $item->id ?? '' }}" id="">
                 <label for="">From</label>
                 <select class="form-control" name="edit_from[]" required
                     data-parsley-required-message="Please select airport.">
                     <option value="">Select Airport</option>
                     @if ($airport != null && count($airport) > 0)
                         @foreach ($airport as $key => $list)
                             <option value="{{ $key + 1 }}" {{ $item->from == $key + 1 ? 'Selected' : '' }}>
                                 {{ $list ?? '' }}</option>
                         @endforeach
                     @endif
                 </select>
             </div>
             <div class="col-sm-2 mb-3">
                 <label for="">To</label>
                 <select class="form-control" name="edit_to[]" required
                     data-parsley-required-message="Please select city.">
                     <option value="">Select City Name</option>
                     @if (isset($city) && count($city) > 0)
                         @foreach ($city as $list)
                             <option value="{{ $list->id ?? '' }}" {{ $item->to == $list->id ? 'Selected' : '' }}>
                                 {{ $list->name ?? '' }}</option>
                         @endforeach
                     @endif
                 </select>
             </div>
             <div class="col-sm-1 mb-3">
                 <label for="">Base Km.</label>
                 <input type="number" class="form-control" name="edit_base_km[]" placeholder="Base km"
                     value="{{ $item->base_km ?? '' }}" required data-parsley-required-message="Please enter base km.">
             </div>
             @if (isset($item->cabPriceType) && count($item->cabPriceType) > 0)
                 @foreach ($item->cabPriceType as $typeList)
                     @if ($typeList->cab_type == App\Enums\Type::CAB_HATCHBACK_ID)
                         <div class="col-sm-2 mb-3">
                             <label for="">Hatchback Base Fare</label>
                             <input type="number" class="form-control" name="edit_hatchback_base_fare[]"
                                 value="{{ $typeList->base_fare }}" placeholder="Hatchback base Fare" min="1"
                                 required data-parsley-required-message="Please enter hatchback base fare price.">
                         </div>
                     @endif
                     @if ($typeList->cab_type == App\Enums\Type::CAB_SEDAN_ID)
                         <div class="col-sm-2 mb-3">
                             <label for="">Sedan Base Fare</label>
                             <input type="number" class="form-control" name="edit_sedan_base_fare[]"
                                 value="{{ $typeList->base_fare }}" placeholder="Sedan base Fare" min="1"
                                 required data-parsley-required-message="Please enter sedan base fare price.">
                         </div>
                     @endif
                     @if ($typeList->cab_type == App\Enums\Type::CAB_SUV_ID)
                         <div class="col-sm-2 mb-3">
                             <label for="">Suv Base Fare</label>
                             <input type="number" class="form-control" name="edit_suv_base_fare[]"
                                 value="{{ $typeList->base_fare }}" placeholder="Suv base Fare" min="1" required
                                 data-parsley-required-message="Please enter suv base fare price.">
                         </div>
                     @endif
                 @endforeach
             @endif
             {{-- @if ($itemKey == 0) --}}

             <div class="mt-4 col-sm-1" style="text-align: right">
                 <button class="btn btn-danger removeTabHtmlOneEdit btn-sm" data-id="{{ $item->id ?? '' }}"
                     data-url="{{ route('admin.setting.cabRate.delete', $item->id) }}"><i
                         class="feather icon-trash-2"></i></button>
                 {{-- @if ($itemKey == count($airPortPickup) - 1)
                     <button class="btn btn-primary addNewTab1 btn-sm" type="button"><i
                             class="feather icon-plus"></i></button>
                 @endif --}}
             </div>
         </div>
     @endforeach
 @else
  <div class="row d-flex ">
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
                     <option value="{{ $list->id ?? '' }}">{{ $list->name ?? '' }}
                     </option>
                 @endforeach
             @endif
         </select>
     </div>
     <div class="col-sm-1 mb-3">
         <label for="">Base Km.</label>
         <input type="number" class="form-control" name="base_km[]" placeholder="Base km" required
             data-parsley-required-message="Please enter base km.">
     </div>
     <div class="col-sm-2 mb-3">
         <label for="">Hatchback Base Fare</label>
         <input type="number" class="form-control" name="hatchback_base_fare[]" placeholder="Hatchback base Fare"
             min="1" required data-parsley-required-message="Please enter hatchback base fare price.">
     </div>
     <div class="col-sm-2 mb-3">
         <label for="">Sedan Base Fare</label>
         <input type="number" class="form-control" name="sedan_base_fare[]" placeholder="Sedan base Fare"
             min="1" required data-parsley-required-message="Please enter sedan base fare price.">
     </div>
     <div class="col-sm-2 mb-3">
         <label for="">Suv Base Fare</label>
         <input type="number" class="form-control" name="suv_base_fare[]" placeholder="Suv base Fare"
             min="1" required data-parsley-required-message="Please enter suv base fare price.">
     </div>
     </div>
     {{-- <div class="mt-4 col-sm-1" style="text-align: right">
         <button class="btn btn-primary addNewTab1 btn-sm"><i class="feather icon-plus"></i></button>
     </div> --}}

 @endif
