@if (session('warning'))
    <div class="alert alert-warning alert-dismissible show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    @if(is_array(session('warning')))
        <ul>
        @foreach(session('warning') as $warningmsg)
                <li>{{ $warningmsg }}</li>
        @endforeach
        </ul>
    @else
            {{ session('warning') }}
    @endif

    </div>
@endif