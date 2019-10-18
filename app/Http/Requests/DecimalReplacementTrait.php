<?php

namespace App\Http\Requests;

use App\Helpers\Arr;
use App\Helpers\NumberFormatter;

trait DecimalReplacementTrait
{
    /**
     * Returns a dotted array of subjects that match the $subject.
     *
     * @param array  $subjects
     * @param string $subject
     *
     * @return array
     */
    private function extractSubjects(array $subjects, string $subject): array
    {
        $subjects = \Illuminate\Support\Arr::dot($subjects);
        foreach ($subjects as $subjectToCheck => $value) {
            if (! array_key_exists($subject, array_flip(explode('.', $subjectToCheck)))) {
                unset($subjects[$subjectToCheck]);
            }
        }

        return $subjects;
    }

    /**
     * Formats and replaces the decimals in the request.
     *
     * @param array $keys
     */
    protected function decimals(array $keys)
    {
        $merges = [];

        foreach ($keys as $mainInputKey => $inputKey) {
            // check if a main input key is set
            if (! is_int($mainInputKey)) {
                // if so, we need so extract a given subject from the input values
                $decimals = $this->input($mainInputKey);
                $subjects = $this->extractSubjects($decimals, $inputKey);

                foreach ($subjects as $subjectKey => $subjectValue) {
                    $decimal = NumberFormatter::reverseFormat($subjectValue);
                    $merges = array_replace_recursive($merges, Arr::arrayUndot([$mainInputKey.'.'.$subjectKey => $decimal]));
                }
            } else {
                $decimal = $this->input($inputKey);

                $dec = NumberFormatter::reverseFormat($decimal);
                $merges = array_merge_recursive($merges, Arr::arrayUndot([$inputKey => $dec]));
            }
        }
        $this->replace(array_replace_recursive($this->all(), $merges));
    }
}
