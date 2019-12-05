@component('cooperation.pdf.components.new-page')
    <div class="container">

        @include('cooperation.pdf.user-report.parts.measure-page.step-intro')

        @include('cooperation.pdf.user-report.parts.measure-page.filled-in-data')

        @include('cooperation.pdf.user-report.parts.measure-page.insulation-advice')

        @include('cooperation.pdf.user-report.parts.measure-page.indicative-costs-and-measures')

        @include('cooperation.pdf.user-report.parts.measure-page.advices')

        @include('cooperation.pdf.user-report.parts.measure-page.comments')
    </div>
@endcomponent