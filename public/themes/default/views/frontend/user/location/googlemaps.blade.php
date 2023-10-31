{{-- START LOCATION MODAL --}}

{{ Request::is('dash/settings') ? $force = false : $force = true }}

{{-- START modal for user location --}}
<div class="modal @if($force) modal-danger @else modal-success @endif fade modal-super-scaled" id="modal_user_location" @if($force) data-backdrop="static" data-keyboard="false" @endif>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{-- Open form to save location --}}
      {!! Form::open(array('url'=>'dash/settings/location', 'id'=>'form-savelocation', 'role'=>'form', 'parsley-validate'=>'','novalidate'=>' ')) !!}

      {{-- Start Modal header --}}
      <div class="modal-header">

        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

        <div class="title">
          {{-- Close button redirect to previous page or homepage --}}
          @if($force)
          <a href="@if(URL::previous() == URL::current()) {{ url('/') }} @else {{ URL::previous() }} @endif" class="close" >
            <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
          </a>
          @else
          <a data-dismiss="modal" data-toggle="modal" class="close" href="javascript:void(0)">
            <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
          </a>
          @endif
          {{-- Modal title (Set Location) --}}
          <h4 class="modal-title" id="myModalLabel">
            <i class="fa fa-map-marker" aria-hidden="true"></i>
            {{ trans('users.modal_location.title') }}
          </h4>
        </div>

      </div>
      {{-- End Modal header --}}

      {{-- Start Modal body --}}
      <div class="modal-body" style="z-index: 2 !important;">


        {{-- Start Location form --}}
        <div class="form-group" id="selectlocation" style="margin-bottom: 0 !important;">

          @if($force)
          {{-- Info --}}
          <div class="m-b-20">{{ trans('users.modal_location.info') }}</div>
          @endif


          <div class="form-group">
            <label class="f-s-16 f-w-700">
              Address
            </label>
            <input type="text" class="form-control rounded input-lg inline input" name="address" id="address" autocomplete="off" placeholder="Address" required/>
            <p id="addressHelpBlock" class="form-text text-muted">
              <i class="fas fa-info-circle"></i> Select the nearest available address. Drag and drop the pin in the bottom map to fine adjust the location.
            </p>{{--addressHelpBlock--}}
          </div>{{--form-group--}}



          <div class="google-maps-location m-b-20" id="maps">
          </div>{{--google-maps--}}

          {{--
          <div>
            <input type="search" id="input-map" class="form-control input" placeholder="{{ trans('users.modal_location.placeholder.where_are_we_going') }}" />
          </div> --}}


          <div id="address-info" {{ isset($location) ? '' : 'class=hidden' }}>
            <div class="selected-address">
              <img id="selected-country-flag" src="{{ isset($location->country_abbreviation) ? asset('img/flags/' . $location->country_abbreviation . '.svg') : '' }}" width="16" class="m-r-5"> <span id="selected-country" class="f-w-700">{{ isset($location->country) ? $location->country : '' }}</span> / <span id="selected-city" class="m-r-10">{{ isset($location->postal_code) ? $location->postal_code : '' }} {{ isset($location->place) ? $location->place : '' }}</span>
            </div>
          </div>{{--address-info--}}

          <div class="locality-search-status hidden bg-danger m-t-20" id="status">
          </div>{{--status--}}

        </div>
        {{-- End Location form --}}

        {{-- Location saved message --}}
        <div class="location-saved hidden" id="savedlocation">
            <div class="icon text-success">
              <i class="fa fa-check-circle" aria-hidden="true"></i>
            </div>
            <div class="text">
              {{ trans('users.modal_location.location_saved') }}
            </div>
        </div>

      </div>
      {{-- End Modal body --}}

      {{-- Start Modal footer for form --}}
      <div class="modal-footer" id="selectlocationfooter">
        @if($force)
        <a href="@if(URL::previous() == URL::current()) {{ url('/') }} @else {{ URL::previous() }} @endif" class="btn btn-dark btn-lg btn-animate btn-animate-vertical" ><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }} </span></a>
        @else
        <a data-dismiss="modal" data-toggle="modal" href="javascript:void(0)" class="btn btn-dark btn-lg btn-animate btn-animate-vertical" ><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }} </span></a>
        @endif
        <button class="btn @if($force) btn-danger @else btn-success @endif  btn-lg btn-animate btn-animate-vertical" type="submit" disabled>
            <span><i class="icon fa fa-check" aria-hidden="true"></i> {{ trans('users.modal_location.set_location') }}
            </span>
        </button>
        {!! Form::close() !!}
      </div>
      {{-- End Modal footer for form --}}

      {{-- Start Modal footer for saved location --}}
      <div class="modal-footer hidden" id="savedlocationfooter">
        <span style="opacity: 0.5">{{ trans('users.modal_location.close_sec_1') }} <span class="c" id="10"></span> {{ trans('users.modal_location.close_sec_2') }} </span>
        @if($force)
        <a data-dismiss="modal" class="btn btn-dark btn-animate btn-animate-vertical"><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('users.modal_location.close_now') }}</span></a>
        @else
        <a onClick="window.location.href=window.location.href" class="btn btn-dark btn-animate btn-animate-vertical"><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('users.modal_location.close_now') }}</span></a>
        @endif
      </div>
      {{-- End Modal footer for saved location --}}

    </div>
  </div>
</div>
{{-- END modal for user location --}}
{{-- END LOCATION MODAL --}}


{{-- START LOCATION CHECK SCRIPT --}}


@if(config('settings.google_maps_key'))
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ config('settings.google_maps_key') }}"></script>
@endif





<script type="text/javascript">
$(document).ready(function(){

@if($force)
  {{-- Open modal for user location --}}
  $("#modal_user_location").modal();
@endif

  var map = new google.maps.Map(document.getElementById('maps'), {

      @if(isset($location))
      center: {lat: {{ $location->latitude }}, lng: {{ $location->longitude }}},
      zoom: 15
      @else
      center: {lat: 48, lng: 16},
      zoom: 1
      @endif
  });

  {{-- Status --}}
  var status = $('#status');
  var input = document.getElementById('address');
  var current_place;
  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);
  autocomplete.setTypes(['address']);


  var marker = new google.maps.Marker({
    map: map,
    draggable: true,
    animation: google.maps.Animation.DROP,
    anchorPoint: new google.maps.Point(0, -29)
  });

  @if(isset($location))
  marker.setPosition({lat: {{ $location->latitude }}, lng: {{ $location->longitude }}});
  @else
  marker.setPosition({lat: 48, lng: 16});
  @endif
  marker.setVisible(true);


  google.maps.event.addListener(marker, 'dragend', function() {
    geocodePosition(marker.getPosition());
  })


  function addressValue(place, value, short = false)
  {
    filtered_array = place.address_components.filter(function(address_component){
        return address_component.types.includes(value);
    });

    return filtered_array.length ? (short ? filtered_array[0].short_name : filtered_array[0].long_name) : "";
  }

  function selectedPlace(place)
  {
    $('[type="submit"]').prop('disabled', false);

    $('#address-info').slideDown('fast');
    var country = addressValue(place, 'country');
    var country_code = addressValue(place, 'country', true);

    var city = addressValue(place, 'locality');

    if (city == '') {
      city = addressValue(place, 'administrative_area_level_2');
    }

    if (city == '') {
      city = addressValue(place, 'administrative_area_level_1');
    }

    var postal_code = addressValue(place, 'postal_code');
    var route = addressValue(place, 'route');
    var street_number = addressValue(place, 'street_number');

    $('#place_id').val(place.place_id);
    $('#address').val(place.formatted_address);
    $('#selected-country').html(country);
    $('#selected-city').html(postal_code + ' ' + city);
    $("#selected-country-flag").attr("src","{{ asset('/img/flags') }}/" + country_code + '.svg');
    current_place = place;
  }

  function geocodePosition(pos)
  {
     geocoder = new google.maps.Geocoder();
     geocoder.geocode
      ({
          latLng: pos
      },
          function(results, status)
          {
              if (status == google.maps.GeocoderStatus.OK)
              {
                  selectedPlace(results[0]);
                  $("#mapErrorMsg").hide(100);
              }
              else
              {
                  $("#mapErrorMsg").html('Cannot determine address at this location.'+status).show(100);
              }
          }
      );
  }

  autocomplete.addListener('place_changed', function() {
    place = autocomplete.getPlace();
    marker.setVisible(false);
    if (!place.geometry) {
      // User entered the name of a Place that was not suggested and
      // pressed the Enter key, or the Place Details request failed.
      window.alert("No details available for input: '" + place.name + "'");
      return;
    }

    selectedPlace(place);

    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);  // Why 17? Because it looks good.
    }
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);

    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }
  });


  {{-- CLOSE WINDOW COUNTER --}}
  function c(){
      var n=$('.c').attr('id');
      var c=n;
      $('.c').text(c);
      setInterval(function(){
          c--;
          if(c>=0){
              $('.c').text(c);
          }
          if(c==0){
              $('.c').text(n);
          }
      },1000);
  };


  {{-- process the form --}}
  $('#form-savelocation').submit(function(event) {

    // stop the form from submitting the normal way and refreshing the page
    event.preventDefault();

    var data = {
      address_components: current_place.address_components,
      lng: current_place.geometry.location.lng(),
      lat: current_place.geometry.location.lat()
    };

    {{-- process the form --}}
    $.ajax({
        type        : 'POST',
        url         : $(this).attr('action'),
        data        : data,
        dataType    : 'json',
        {{-- Send CSRF Token over ajax --}}
        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
        beforeSend: function() {
          status.slideUp('fast');
        },
        success: function() {

          $('#selectlocation').slideUp('fast', function(){
              $('#savedlocation').slideDown('fast');
          });

          $('#selectlocationfooter').slideUp('fast', function(){
              $('#savedlocationfooter').slideDown('fast');
          });
          @if($force)
          setTimeout(function() {$('#modal_user_location').modal('hide');}, 10000);
          @else
          setTimeout(function() {window.location.href = window.location.href;}, 10000);
          @endif

          {{-- Start counter for closing modal --}}
          c();

        },
        error: function() {
          status.slideDown('fast');
          status.html('<i class="fa fa-minus-circle" aria-hidden="true"></i> {{ trans('users.modal_location.error') }}');

        }
    });


  });



});
</script>
{{-- END LOCATION CHECK SCRIPT --}}

{{-- Google Maps suggestions modal fix --}}
<style>
.pac-container {
    background-color: #323232;
    z-index: 20;
    position: fixed;
    display: inline-block;
    float: left;
    border-radius: 5px;
    border-top: 0px;
    border-radius: 5px;
    border-top-right-radius: 0px;
    border-top-left-radius: 0px;

}
.pac-container:after{display:none !important;}
.pac-item	{
  color: #bbb;
  border-top: 0px;
  border-bottom: 1px solid rgba(255,255,255,0.05);
  padding: 5px;
  -moz-transition: all .3s ease 0s;
  -webkit-transition: all .3s ease 0s;
  -o-transition: all .3s ease 0s;
  transition: all .3s ease 0s;
}

.pac-item:hover	{
  background-color: #444;
}
.pac-item-query	{
  color: #fff;
  font-weight: 700;
}
.modal{
    z-index: 20;
}
.modal-backdrop{
    z-index: 10;
}​

</style>
