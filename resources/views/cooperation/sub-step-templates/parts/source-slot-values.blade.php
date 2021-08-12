@foreach($values as $inputSourceShort => $answer)
    <li class="change-input-value" data-input-source-short="{{$inputSourceShort}}" data-input-value="{{$answer}}">
        {{\App\Models\InputSource::findByShort($inputSourceShort)->name}}: {{$answer}}
    </li>
@endforeach