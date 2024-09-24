@include('partials.email-header')
    <body>

        @yield('content')
    
        @include('partials.email-footer')
    </body>
</html>