<form id="logout-form" action="{{ route('cooperation.auth.logout', compact('cooperation')) }}" method="POST">
    @csrf

    <button class="as-link in-text">
        @lang('auth.logout.form.header')
    </button>
</form>