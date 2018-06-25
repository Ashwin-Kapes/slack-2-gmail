@section('body')
    @section('header')
        {{-- Comment this include to remove header section --}}
        @include('layout.header')
    @show

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
