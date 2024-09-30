<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UniqueSlug extends LocaleBasedRule implements Rule
{
    public string $class;
    public string $column;
    public ?Model $ignore;

    public function __construct(string $class, string $column, ?Model $ignore = null, $countryIso3166alpha2 = null)
    {
        parent::__construct($countryIso3166alpha2);

        $this->class = $class;
        $this->column = $column;
        $this->ignore = $ignore;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes(string $attribute, $value): bool
    {
        $instance = new $this->class;

        // Check if translatable
        $translatableSlugField = false;
        if (method_exists($instance, 'isTranslatableAttribute')) {
            $translatableSlugField = $instance->isTranslatableAttribute($this->column);
        }

        $value = Str::slug($value);
        $column = $translatableSlugField ? "{$this->column}->{$this->country}" : $this->column;

        $query = DB::table($instance->getTable())->where($column, $value);

        if (! is_null($this->ignore)) {
            $query->where('id', '!=', $this->ignore->id);
        }

        return is_null($query->first());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.unique');
    }
}
