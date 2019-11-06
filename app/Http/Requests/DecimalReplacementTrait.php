<?php

namespace App\Http\Requests;

use App\Helpers\Arr;
use App\Helpers\NumberFormatter;

trait DecimalReplacementTrait
{

    protected $replacements = [];

    /**
     * Returns a dotted array of subjects that match the $subject
     *
     * @param  array  $subjects
     * @param  string  $subject
     *
     * @return array
     */
    private function extractSubjects(array $subjects, string $subject): array
    {
        $subjects = \Illuminate\Support\Arr::dot($subjects);
        foreach ($subjects as $subjectToCheck => $value) {
            if (!array_key_exists($subject, array_flip(explode('.', $subjectToCheck)))) {
                unset($subjects[$subjectToCheck]);
            }
        }
        return $subjects;
    }

    /**
     * Formats and replaces the decimals in the request
     *
     * @param  array  $keys
     */
    protected function decimals(array $keys)
    {

        foreach ($keys as $mainInputKey => $inputKey) {
            // check if a main input key is set
            if (!is_int($mainInputKey)) {
                // if so, we need so extract a given subject from the input values
                $decimals = $this->input($mainInputKey);
                $subjects = $this->extractSubjects($decimals, $inputKey);

                foreach ($subjects as $subjectKey => $subjectValue) {
                    $this->createReplaceArray($mainInputKey.'.'.$subjectKey);
                }
            }  else {
                $this->createReplaceArray($inputKey);
            }

        }
        $this->replace(array_replace_recursive($this->all(), $this->replacements));
    }

    private function createReplaceArray($requestKey)
    {
        $decimal = $this->input($requestKey);
        if (!is_null($decimal)) {
            $decimal = $value = NumberFormatter::reverseFormat($decimal);
            $this->replacements = array_replace_recursive($this->replacements, Arr::arrayUndot([$requestKey => $decimal]));
        }
    }
}