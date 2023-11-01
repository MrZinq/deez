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

          <div id="map-example-container" class="m-b-20"></div>
          <div>
            <input type="search" id="input-map" class="form-control input" placeholder="{{ trans('users.modal_location.placeholder.where_are_we_going') }}" />
          </div>


          {{-- Selected location --}}
          <div class="selected-location m-t-20 hidden" id="selectedlocation_panel">
            <span>{{ trans('users.modal_location.selected_location') }}</span>
            <div>
              <i class="fa fa-map-marker" aria-hidden="true"></i> <span id="selectedlocation"> </span>
            </div>
          </div>
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



<link rel="stylesheet" href="https://cdn.jsdelivr.net/leaflet/1/leaflet.css" />
<script src="https://cdn.jsdelivr.net/leaflet/1/leaflet.js"></script>

<style>
  #map-example-container {height: 150px; border-radius: 5px; opacity: 0.99;};
  #input-styling-address .ap-suggestion { background-color: #000 !important; }
  .ap-dropdown-menu {  background-color: #191818 !important; border-radius: 5px !important;}
  .ap-cursor { background-color: #212121; }
</style>

<script src="https://cdn.jsdelivr.net/places.js/1/places.min.js"></script>
<script>
(function() {

})();
</script>







<script type="text/javascript">
$(document).ready(function(){

@if($force)
  {{-- Open modal for user location --}}
  $("#modal_user_location").modal();
@endif


  {{-- Location data (JSON) --}}
  var location_data = null;

  var placesAutocomplete = places({
    container: document.querySelector('#input-map'),
    language: '{{ session()->has('locale') ? session()->get('locale') : config('settings.locale_selector') }}'
  });

  var map = L.map('map-example-container', {
    scrollWheelZoom: false,
    zoomControl: false
  });

  var osmLayer = new L.TileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      minZoom: 1,
      maxZoom: 13,
      attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>'
    }
  );

  var markers = [];

  map.setView(new L.LatLng(0, 0), 1);
  map.addLayer(osmLayer);



  placesAutocomplete.on('suggestions', handleOnSuggestions);
  placesAutocomplete.on('cursorchanged', handleOnCursorchanged);
  placesAutocomplete.on('change', handleOnChange);
  placesAutocomplete.on('clear', handleOnClear);

  function handleOnSuggestions(e) {
    markers.forEach(removeMarker);
    markers = [];

    if (e.suggestions.length === 0) {
      map.setView(new L.LatLng(0, 0), 1);
      return;
    }

    e.suggestions.forEach(addMarker);
    findBestZoom();
  }

  function handleOnChange(e) {

    location_data = e.suggestion;

    $('#selectedlocation').text( ' ' + e.suggestion.countryCode.toUpperCase() + ', '+ (e.suggestion.city ? e.suggestion.city : e.suggestion.name) + (e.suggestion.postcode ? ' ('+ e.suggestion.postcode +')' : ''));
    $('#selectedlocation_panel').slideDown('fast');

    $('[type="submit"]').prop('disabled', false);

    markers
      .forEach(function(marker, markerIndex) {
        if (markerIndex === e.suggestionIndex) {
          markers = [marker];
          marker.setOpacity(1);
          findBestZoom();
        } else {
          removeMarker(marker);
        }
      });
  }

  function handleOnClear() {
    map.setView(new L.LatLng(0, 0), 1);
    markers.forEach(removeMarker);
  }

  function handleOnCursorchanged(e) {
    markers
      .forEach(function(marker, markerIndex) {
        if (markerIndex === e.suggestionIndex) {
          marker.setOpacity(1);
          marker.setZIndexOffset(1000);
        } else {
          marker.setZIndexOffset(0);
          marker.setOpacity(0.5);
        }
      });
  }

  function addMarker(suggestion) {
    var marker = L.marker(suggestion.latlng, {opacity: .4});
    marker.addTo(map);
    markers.push(marker);
  }

  function removeMarker(marker) {
    map.removeLayer(marker);
  }

  function findBestZoom() {
    var featureGroup = L.featureGroup(markers);
    map.fitBounds(featureGroup.getBounds().pad(0.5), {animate: false});
  }

  {{-- Map modal size fix --}}
  $('#modal_user_location').on('shown.bs.modal', function (e) {
    map.invalidateSize();
  })




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

    {{-- process the form --}}
    $.ajax({
        type        : 'POST',
        url         : $( this ).attr( 'action' ),
        data        : location_data,
        dataType    : 'json',
        {{-- Send CSRF Token over ajax --}}
        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
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
          $('#selectedlocation_panel').slideUp('fast');
          status.removeClass('bg-dark').removeClass('bg-success').addClass('bg-danger');
          status.html('<i class="fa fa-minus-circle" aria-hidden="true"></i> {{ trans('users.modal_location.error') }}');
            $("#postalcode").val("");
            locality.attr('disabled', 'disabled');
            locality.empty().append('<option selected="selected" value="whatever">&larr; {{ trans('users.modal_location.placeholder.postal_code_locality') }}</option>');
            $('[type="submit"]').prop('disabled', true);
        }
    });

    // stop the form from submitting the normal way and refreshing the page
    event.preventDefault();
  });



});
</script>
{{-- END LOCATION CHECK SCRIPT --}}
