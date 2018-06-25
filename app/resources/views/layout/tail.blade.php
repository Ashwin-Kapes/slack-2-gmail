@if(config('h5b.modernizr'))
    <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/{{ config('h5b.modernizr_version') }}/modernizr.min.js"></script>
@endif
@if(config('h5b.jquery'))
    <script src="https://code.jquery.com/jquery-{{ config('h5b.jquery_version') }}.min.js" integrity="{{ config('h5b.jquery_integrity')}}" crossorigin="anonymous"></script>
    {{-- <script>window.jQuery || document.write('<script src="js/vendor/jquery-{{ config('h5b.jquery') }}.min.js"><\/script>')</script> --}}
@endif

@section('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@show

@if(config('h5b.ga'))
    <script>
        window.ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;
        ga('create','{{ config('h5b.ga_id') }}','auto');ga('send','pageview')
    </script>
    <script src="https://www.google-analytics.com/analytics.js" async defer></script>
@endif

<script>
  // hack
  document.addEventListener("DOMContentLoaded", function (event) {
    if (window.location.pathname != "/")
        return;
    $(".navbar-default").removeClass("active").addClass("navbar-fixed-top"), $(document).scroll(function () {
        if ((e = $(this).scrollTop()) > 440) $(".navbar-default").addClass("active");
        else {
            if ($(".navbar-default").hasClass("home") && $(".navbar-collapse").hasClass("in")) return;
            $(".navbar-default").removeClass("active")
        }
    }), $(".navbar-toggle").click(function (n) {
        $(".navbar-default").hasClass("active") ? $(".navbar-default").hasClass("home") && e < 440 && $(".navbar-default").removeClass("active") : $(".navbar-default").addClass("active home")
    }), $(window).resize(function () {
        var e = window.innerWidth;
        $(".navbar-default").hasClass("home") && $(".navbar-collapse").hasClass("in") && (e > 767 ? $(".navbar-default").removeClass("active") : $(".navbar-default").addClass("active"))
    })
  });
</script>
