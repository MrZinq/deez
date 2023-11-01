@extends(Theme::getLayout())

@section('subheader')
  {{-- Start Subheader --}}
  <div class="subheader">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
    <div class="background-color"></div>

    <div class="content">
      <span class="title"><i class="fa fa-plus"></i> {{ trans('games.add.add_game') }}</span>
    </div>

  </div>
  {{-- End Subheader --}}
@stop


@section('content')
  {{-- Start Select Game Panel --}}
  <section class="panel">

    {{-- Panel Header --}}
    <div class="panel-heading">
      <h3 class="panel-title">
        <i class="fa fa-search"></i> {{ trans('games.add.search_game') }}
      </h3>
    </div>
    {{-- Open form for search --}}
    <form id="searchForm" method="POST" novalidate="novalidate">

      <div class="panel-body">

        {{-- Loading bar --}}
        <div class="loading-bar hidden" id="loading_bar">
          <i class="fa fa-spinner fa-pulse fa-fw"></i> {{ trans('games.add.searching') }}
        </div>

        {{-- Start Input Group with system select and input for search value --}}
        <div class="input-group input-group-lg" id="search_bar">
            <div class="input-group-btn search-panel">
                {{-- Select for systems --}}
                <div class="btn dropdown-system">
                    <i class="fa fa-gamepad"></i>
                </div>
            </div>
          {{-- Search param - in this case system acronym --}}
          <input type="hidden" name="search_param" value="all" id="search_param">
          {{-- Input for search value --}}
          <input type="text" id="appendedInput" name="game" class="form-control input" placeholder="{{ trans('games.add.enter_title') }}" autocomplete="off">
        </div>
        {{-- End Input Group with system select and input for search value --}}
      </div>

      <div class="panel-footer">
        <div></div>
        {{-- Form submit --}}
        <button type="submit" class="button send-search" id="startsearch">
          <i class="fa fa-search" aria-hidden="true"></i> {{ trans('general.search') }}
        </button>
      </div>

    </form>
    {{-- Close form for search --}}

  </section>
  {{-- End Select Game Panel --}}



  <div id="searchresult">
  </div>

  {{-- Start Loading Modal --}}
  <div class="modal fade modal-fade-in-scale-up" id="modal_game_add" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body modal-loading">
          <div class="loader-item"><div class="loader pacman-loader lg"></div></div>
          <span>
              <strong>{{ trans('games.add.adding',  ['pagename' => config('settings.page_name')]) }}</strong> <br> <span id="please_wait">{{ trans('games.add.wait') }}</span>
          </span>
        </div>
      </div>
    </div>
  </div>
  {{-- End Loading Modal --}}



@stop


@section('after-scripts')
<script type="text/javascript">
$(document).ready(function(){
  {{-- Center loading modal --}}
  (function ($) {
      "use strict";
      function centerModal() {
          $(this).css('display', 'block');
          var $dialog  = $(this).find(".modal-dialog"),
          offset       = ($(window).height() - $dialog.height()) / 2,
          bottomMargin = parseInt($dialog.css('marginBottom'), 10);
          if(offset < bottomMargin) offset = bottomMargin;
          $dialog.css("margin-top", offset);
      }

      $(document).on('show.bs.modal', '.modal', centerModal);
      $(window).on("resize", function () {
          $('.modal:visible').each(centerModal);
      });
  }(jQuery));

  {{-- Loading dots animation --}}
  var originalText = $("#please_wait").text(),
      i  = 0;
  setInterval(function() {

      $("#please_wait").append(".");
      i++;

      if(i === 4)
      {
          $("#please_wait").html(originalText);
          i = 0;
      }

  }, 500);

  {{-- Check if search input have value --}}
  $("#appendedInput").keyup(function(event){
    $('#appendedInput').val() === '' ? $('.send-search').attr('disabled', true) : $('.send-search').attr('disabled', false);
  });

  {{-- Send CSRF Token over ajax --}}
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': Laravel.csrfToken }
  });

  {{-- Start Form submit and get ajax results --}}
  $("#searchForm").submit(function(e){
    e.preventDefault();
    $.searchGames();
  });
  {{-- End Form submit and get ajax results --}}

  jQuery.searchGames = function searchGames(page = 1) {
    if ($('#appendedInput').val()) {
      var searchForm = $("#searchForm");
      var searchData = searchForm.serialize();

      searchData += '&page='+page;

      $.ajax({
        url:'{{ url("games/api/search") }}',
        type:'POST',
        data:searchData,
        beforeSend: function(){
          $( "#searchresult" ).fadeOut('slow');

          $('.send-search').attr('disabled', true);
          $(".send-search").html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

          $('#loadingoffercomplete').hide();
          $('#loadingoffersearch').show();

          $('#search_bar').fadeOut(200).promise().done(function(){
            $('#loading_bar').fadeIn(200);
          });

        },
        success:function(data){
          $( "#searchresult" ).hide().html(data).fadeIn('slow');


          $('#loadingoffercomplete').show();
          $('#loadingoffersearch').hide();

          $('#loading_bar').fadeOut(200).promise().done(function(){
            $('#search_bar').fadeIn(200);
          });
          $('.send-search').attr('disabled', false);
          $(".send-search").html('<i class="fa fa-search" aria-hidden="true"></i> {{ trans('general.search') }}');

        },
        error: function (data) {
          alert('Oops, an error occurred!')
          $('#loadingoffercomplete').show();
          $('#loadingoffersearch').hide();

          $('#loading_bar').fadeOut(300).promise().done(function(){
            $('#search_bar').fadeIn(200);
          });
          $('.send-search').attr('disabled', false);
          $(".send-search").html('<i class="fa fa-search" aria-hidden="true"></i> {{ trans('general.search') }}');
        }
      });
    }
  }

})
</script>
@stop
