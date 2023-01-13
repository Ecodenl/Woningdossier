@if(($withPageBreak ?? true))
    @include('cooperation.pdf.user-report.parts.page-break')
@endif

<div id="{{ $id }}" class="container">
    {{$slot}}
</div>