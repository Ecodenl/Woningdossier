<a href="{{ route('cooperation.auth.logout', compact('cooperation')) }}" class="in-text"
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    @lang('auth.logout.form.header')
</a>

<form id="logout-form" action="{{ route('cooperation.auth.logout', compact('cooperation')) }}" method="POST" class="hidden">
    @csrf
</form>