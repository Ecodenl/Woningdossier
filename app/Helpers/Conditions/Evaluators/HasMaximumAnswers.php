<?php

namespace App\Helpers\Conditions\Evaluators;

class HasMaximumAnswers extends ShouldEvaluate
{
    public function evaluate($value = null): array
    {
        $building = $this->building;
        $inputSource = $this->inputSource;
        $answers = $this->answers;

        // This evaluator checks if the given answers doesn't exceed maximum
        // $value must be an array, where
        // 'column' => The tool question short to check
        // 'max' => Max amount of answers
        // 'ignore' => Potential answers to ignore (applicable for array)

        $answer = $this->getAnswer($value['column']);
        $ignores = (array) ($value['ignore'] ?? []);
        $key = md5(json_encode(['answer' => $answer, 'ignore' => $ignores]));

        if (array_key_exists($key, $this->override)) {
            $totalAnswers = $this->override[$key];
            return [
                'results' => $totalAnswers,
                'bool' => $totalAnswers < $value['max'],
                'key' => $key,
            ];
        }

        // TODO: Make this work with INT/STRING etc.
        if (is_array($answer)) {
            foreach ($ignores as $ignore) {
                $index = array_search($ignore, $answer);

                if ($index !== false) {
                    unset($answer[$index]);
                }
            }

            $totalAnswers = count($answer);

            return [
                'results' => $totalAnswers,
                'bool' => $totalAnswers < $value['max'],
                'key' => $key,
            ];
        }

        return [
            'results' => 999,
            'bool' => false,
            'key' => $key,
        ];
    }
}