<a onclick="document.getElementById('logout-form').submit();">
    @lang('auth.logout.form.header')
</a>

<form id="logout-form" action="{{ route('cooperation.auth.logout') }}" method="POST" class="hidden">
    @csrf
</form>