<ul class="progress-list list-inline">
    @for($i = 0; $i < 9; $i++)
    <li class="list-inline-item @if($i < 3)done @elseif($i == 3)active @endif"><a href="#"><img src="http://placekitten.com/g/50/50" class="img-circle"></a></li>
    @endfor
</ul>