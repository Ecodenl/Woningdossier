<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\DatabaseRule;

/**
 * Works like Rule::unique, except _with_ support for JSON columns (e.g. extra.contact_id).
 * Uses copied code from the Rule::unique to simplify life.
 */
class RuleUnique implements Rule
{
    use DatabaseRule;

    /**
     * The ID that should be ignored.
     *
     * @var mixed
     */
    protected $ignore;

    /**
     * The name of the ID column.
     *
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * Ignore the given ID during the unique check.
     *
     * @param  mixed  $id
     * @param  string|null  $idColumn
     * @return $this
     */
    public function ignore($id, $idColumn = null)
    {
        if ($id instanceof Model) {
            return $this->ignoreModel($id, $idColumn);
        }

        $this->ignore = $id;
        $this->idColumn = $idColumn ?? 'id';

        return $this;
    }

    /**
     * Ignore the given model during the unique check.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string|null  $idColumn
     * @return $this
     */
    public function ignoreModel($model, $idColumn = null)
    {
        $this->idColumn = $idColumn ?? $model->getKeyName();
        $this->ignore = $model->{$this->idColumn};

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $column = str_replace('.', '->', $this->column);

        $query = DB::table($this->table)
            ->where($column, $value);

        if (isset($this->ignore)) {
            $query->where($this->idColumn, '!=', $this->ignore);
        }

        return is_null($query->first());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.custom-rules.rule-unique');
    }
}