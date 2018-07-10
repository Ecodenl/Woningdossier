<div class="container">
    @includeWhen(session('success'), 'cooperation.admin.layouts.success')
    @includeWhen($errors->any(), 'cooperation.admin.layouts.errors')
</div>