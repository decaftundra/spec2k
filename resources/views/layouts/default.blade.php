<!doctype html>
<html lang="{{ app()->getLocale() }}">
    @include('partials.header')
    <body class="{{ app()->environment() }}">
        <div class="container">
            <div id="main-nav">
                @include('partials.main-nav')
            </div>
            
            <div class="row">
                @include('partials.maintenance-notice')
            </div>
            
            <div id="content">
                @yield('loader')
                
                @yield('content')
            </div>
            <div id="footer">
                @include('partials.footer')
            </div>
        </div>
    </body>
</html>