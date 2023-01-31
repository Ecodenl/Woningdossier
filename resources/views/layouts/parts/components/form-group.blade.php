@php
    $input_name ??= '';
    $parts = explode('.', $input_name);
    $potentialLocale = array_pop($parts);

    $localeName = '';
    foreach (config('hoomdossier.supported_locales') as $locale) {
        if ($potentialLocale === $locale) {
            $localeName = implode('.', $parts);
            break;
        }
    }

    $hasError = $errors->has($input_name);
    $hasLocaleError = $errors->has($localeName);
    $message = $hasError ? $errors->first($input_name) : ($hasLocaleError ? $errors->first($localeName) : null);
@endphp

<div class="form-group @if($hasError || $hasLocaleError) has-error @endif">
    {{$slot}}

    @if($hasError || $hasLocaleError)
        <span class="help-block">
            <strong>
                {{ $message }}
            </strong>
        </span>
    @endif
</div>