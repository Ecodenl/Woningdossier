<?php

namespace App\Http\Requests;

use App\Helpers\Arr;
use App\Helpers\NumberFormatter;

trait DecimalReplacementTrait
{

    private function getInput($answers)
    {
        foreach ($answers as $key) {

        }
    }

    private function extractSubjects(array $subjects, string $subject): array
    {
        $subjects = \Illuminate\Support\Arr::dot($subjects);
        foreach ($subjects as $subjectToCheck => $value) {
            if (!str_contains($subjectToCheck, $subject)) {
                unset($subjects[$subjectToCheck]);
            }
        }
        return $subjects;
    }

    protected function decimals(array $keys)
    {
//
        $merges = [];
        foreach ($keys as $mainInputKey => $inputKey) {


            if (!is_int($mainInputKey)) {
                $decimals = $this->input($mainInputKey);

                $subjects = $this->extractSubjects($decimals, $inputKey);

                foreach ($subjects as $subjectKey => $subjectValue) {
                    dump($subjectKey);

                    $decimal = NumberFormatter::reverseFormat($subjectValue);
                    dump(Arr::arrayUndot([$mainInputKey.'.'.$subjectKey => $decimal]));
                    $merges = array_merge($merges, Arr::arrayUndot([$mainInputKey.'.'.$subjectKey => $decimal]));
                }

            } else {
                $decimal = $this->input($inputKey);

                $dec = NumberFormatter::reverseFormat($decimal);
                $merges = array_merge_recursive($merges, Arr::arrayUndot([$inputKey => $dec]));
            }

        }

//        $this->replace(array_replace_recursive($this->all(), $merges));

        dd($this->all(), $merges, $this->replace(array_replace_recursive($this->all(), $merges))->all());
    }
}

//foreach ($decimal as $dec) {
//    $dec = NumberFormatter::reverseFormat($dec);
//    $merges = array_merge_recursive($merges, Arr::arrayUndot([$key => $dec]));
//}