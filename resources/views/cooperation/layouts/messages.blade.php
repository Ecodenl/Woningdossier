<div class="container">
    @includeWhen(session('success'), 'cooperation.layouts.success')
    @includeWhen(session('warning'), 'cooperation.layouts.warning')
    @includeWhen($errors->any(), 'cooperation.layouts.errors')
</div>