@if (session('success'))
    <div class="alert alert-success alert-dismissible show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    @if(is_array(session('success')))
        <ul>
        @foreach(session('success') as $successmsg)
                <li>{{ $successmsg }}</li>
        @endforeach
        </ul>
    @else
            {{ session('success') }}
    @endif

    </div>
@endif