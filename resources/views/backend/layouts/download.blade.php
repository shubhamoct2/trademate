<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('backend.include.__download_head')

<body>
<!--Full Layout-->
<div class="layout">
    <!--Page Content-->
    <div class="page-container download-container">
        @yield('content')
    </div>
    <!--Page Content-->
</div>
<!--/Full Layout-->

</body>
</html>






