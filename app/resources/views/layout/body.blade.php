@section('body')
    @section('header')
        {{-- Comment this include to remove header section --}}
        @include('layout.header')
    @show

    <a href="https://github.com/samber/slack-2-gmail" target="_blank">
        <img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub">
    </a>

    <div id="main">
        {{-- CONTENT --}}
        @yield('content')
        {{-- /CONTENT --}}
    </div>

    @section('footer')
        {{-- Comment this include to remove footer section --}}
        @include('layout.footer')
    @show
@show
