<div class="container">
    @includeWhen(session('success'), 'layouts.success')
    @includeWhen($errors->any(), 'layouts.errors')
</div>