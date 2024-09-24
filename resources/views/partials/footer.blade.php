<div class="row">
    <footer class="col-sm-12" style="margin-top:20px;margin-bottom:20px;">

        {{-- &copy; {{ date('Y') }} Meggitt Plc Ltd. All rights reserved. Application version {{ App\AppVersion::getAppVersion() }} --}}

        <a class="label label-default pull-right" href="{{ route('issue-tracker.index') }}">
            <i class="fas fa-bell"></i> Report An Issue
        </a>

        <!-- Scripts -->
        <script src="{{ asset('/js/jquery-3.3.1.min.js') }}"></script>
        <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/sweetalert.min.js') }}"></script>
        <script src="{{ asset('js/global.js?v=1.3') }}"></script>

        <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>

        @stack('footer-scripts')

        <?php Session::forget('alert'); ?>
    </footer>
</div>
