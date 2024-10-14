@php
    $original_local = setlocale(LC_TIME,0);
    setlocale(LC_TIME,App::getLocale());
@endphp
{{ !empty($value) ? ucfirst(date('d/m/Y', strtotime($value))) : null }}
@php
    setlocale(LC_TIME,$original_local);
@endphp
