@php
    $method = json_decode($data)->method;
@endphp
<div class="site-badge primary-bg">{{ ucwords(str_replace("_"," ",$method)) }}</div>
