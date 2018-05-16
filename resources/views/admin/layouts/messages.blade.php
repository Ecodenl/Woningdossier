<div class="container">
    @includeWhen(session('success'), 'admin.layouts.success')
    @includeWhen($errors->any(), 'admin.layouts.errors')
</div>