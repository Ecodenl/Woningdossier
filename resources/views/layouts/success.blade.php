@if (session('success'))
    @if(is_array(session('success')))
        @foreach(session('success') as $successmsg)
            <div class="alert alert-success" role="alert">
                {{ $successmsg }}
            </div>
        @endforeach
    @else
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
@endif