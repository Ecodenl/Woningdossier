<?php
// We want to unset keys once the input is placed
$locales = array_flip(config('hoomdossier.supported_locales'));
// default
$translationKey = '';
?>
@if(isset($exampleBuilding) && $exampleBuilding instanceof \App\Models\ExampleBuilding)
{{-- edits --}}
    @foreach($locales as $locale => $i)

        @foreach($exampleBuilding->getTranslations('name') as $translation)
            <?php $translationKey = $translation->key; ?>
            @if ($translation->language == $locale)
                <div class="form-group">
                    <label for="name-{{ $locale }}">{{ $locale }}:</label>
                    <input id="name-{{$locale}}" class="form-control" name="name[{{ $translation->language }}]" value="{{ old('name.' . $translation->language, $translation->translation) }}">
                    {{--<input id="name-{{$locale}}" class="form-control" name="name[{{ $translationKey }}][{{ $translation->language }}]" value="{{ $translation->translation }}">--}}
                </div>
                <?php unset($locales[$locale]); ?>
            @endif
        @endforeach

    @endforeach
@endif
{{-- creates --}}
@foreach($locales as $locale => $i)
    <div class="form-group">
        <label for="name-{{ $locale }}">{{ $locale }}:</label>
        <input class="form-control" name="name[{{ $locale }}]" value="{{ old('name.' . $locale) }}">
        {{--<input class="form-control" name="name[{{ $translationKey }}][{{ $locale }}]" value="">--}}
    </div>
@endforeach