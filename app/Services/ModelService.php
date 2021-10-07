<?php

namespace App\Services;

class ModelService
{
    /**
     * In some cases there are models / tables that have multiple element_ids with different element_value_ids (we take the building_elements table as example).
     *
     * row examples:
     * 1 row
     * element_id = 8
     * element_value_id = 34
     * 2 row
     * element_id = 8
     * element_value_id = 35
     *
     * if its saved like that then its probably a multiselect / checkbox, so if the checkbox with element_value_id 35 gets deselected then we dont know that
     * so we have to delete everything that has a element_id 8 and then we have to reinsert the new data
     *
     * Delete the records with the matching the attributes and create it with the given values
     *
     * @param $model
     * @param  array  $attributes
     * @param  array  $values
     * @param  bool  $withEvents
     */
    public static function deleteAndCreate($model, array $attributes, array $values, bool $withEvents = false)
    {
        // make the model
        $model = new $model();

        // delete the old values
        if ($withEvents) {
            // Sometimes we need to trigger delete events on the model.
            // We delete each model individually on Eloquent basis to trigger these, instead of deleting them in
            // SQL directly.
            $modelsToDelete = $model->where($attributes)->get();
            foreach ($modelsToDelete as $modelToDelete) {
                $modelToDelete->delete();
            }
        } else {
            $model->where($attributes)->delete();
        }


        // insert the data
        foreach ($values as $columnName => $newValues) {
            $createArray = array_merge($attributes, $newValues);
            $model->create($createArray);
        }
    }
}
