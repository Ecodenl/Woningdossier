<div class="container">
    @includeWhen(session('success'), 'cooperation.layouts.success')
    @includeWhen($errors->any(), 'cooperation.layouts.errors')
</div>