@if (session('success'))
    <div class="alert alert-success" role="alert">
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