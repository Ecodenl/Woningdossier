<div class="w-full flex justify-center">
    <div class="w-1/2 justify-center bg-gray rounded-md">
        <ul>
            @foreach($account->recoveryCodes() as $code)
                <li class="inline-block text-center" style="width: 48%">
                    {{$loop->iteration}}. {{$code}}</li>
            @endforeach
        </ul>
    </div>
</div>