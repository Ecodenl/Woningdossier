<?php

namespace App\Services;

use App\Helpers\FileFormats\CsvHelper;
use App\Helpers\NumberFormatter;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\Step;
use App\Models\User;
use Illuminate\Support\Collection;

class CsvService
{
    /**
     * CSV Report that returns the measures with year with full address data.
     *
     * @param $cooperation
     * @param $anonymize
     */
    public static function byMeasure($cooperation, $anonymize): array
    {
        // get the users from the cooperations
        $users = $cooperation->users()->whereHas('buildings')->get();

        if ($anonymize) {
            $csvHeaders = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.input-source'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        } else {
            $csvHeaders = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.input-source'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.coach-appointment-date'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        }

        // get all the measures ordered by step
        $measures = MeasureApplication::leftJoin('steps', 'measure_applications.step_id', '=', 'steps.id')
            ->orderBy('steps.order')
            ->orderBy('measure_applications.measure_type')
            ->select(['measure_applications.*'])
            ->get();

        // put the measures inside the header array
        foreach ($measures as $measure) {
            $csvHeaders[] = $measure->measure_name;
        }

        // new array for the userdata
        $rows = [];

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);

        $generalDataStep = Step::findByShort('general-data');
        foreach ($users as $key => $user) {
            // for each user reset the input source back to the base input source.
            $inputSourceForDump = $inputSource;

            // well in every case there is an exception on the rule
            // normally we would pick the given input source
            // but when coach input is available we use the coach input source for that particular user
            // coach input is available when he has completed the general data step
            if ($user->building->hasCompleted($generalDataStep, $coachInputSource)) {
                $inputSourceForDump = $coachInputSource;
            }

            /** @var Building $building */
            $building = $user->building;

            // normally we could use the PrivateMessage::allowedAccess, but we need to qeury on the to_cooperation_id.
            $allowedAccess = PrivateMessage::conversation($building->id)
                    ->accessAllowed()
                    ->where('to_cooperation_id', $cooperation->id)
                    ->first() instanceof PrivateMessage;

            $createdAt = optional($user->created_at)->format('Y-m-d');
            //$buildingStatus      = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
            $mostRecentStatus = $building->getMostRecentBuildingStatus();
            $buildingStatus = $mostRecentStatus->status->name;
            $allowAccess = $allowedAccess ? 'Ja' : 'Nee';
            $connectedCoaches = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);
            $connectedCoachNames = User::findMany($connectedCoaches->pluck('coach_id'))
                ->map(function ($user) {
                    return $user->getFullName();
                })->toArray();

            // implode it.
            $connectedCoachNames = implode($connectedCoachNames, ', ');

            $firstName = $user->first_name;
            $lastName = $user->last_name;
            $email = $user->account->email;
            $phoneNumber = CsvHelper::escapeLeadingZero($user->phone_number);

            $street = $building->street;
            $number = $building->number;
            $city = $building->city;
            $postalCode = $building->postal_code;

            // get the building features from the resident
            $buildingFeatures = $building
                ->buildingFeatures()
                ->forInputSource($inputSourceForDump)
                ->first();

            $buildingType = $buildingFeatures->buildingType->name ?? '';
            $buildYear = $buildingFeatures->build_year ?? '';
            $exampleBuilding = optional($building->exampleBuilding)->isSpecific() ? $building->exampleBuilding->name : '';

            $appointmentDate = optional($mostRecentStatus->appointment_date)->format('Y-m-d');

            if ($anonymize) {
                // set the personal userinfo
                $row[$key] = [
                    $inputSourceForDump->name, $createdAt, $buildingStatus, $postalCode, $city,
                    $buildingType, $buildYear, $exampleBuilding,
                ];
            } else {
                // set the personal userinfo
                $row[$key] = [
                    $inputSourceForDump->name, $createdAt, $appointmentDate, $buildingStatus, $allowAccess, $connectedCoachNames,
                    $firstName, $lastName, $email, $phoneNumber,
                    $street, $number, $postalCode, $city,
                    $buildingType, $buildYear, $exampleBuilding,
                ];
            }

            // Set a default: all measures to empty
            foreach ($measures as $measure) {
                $row[$key][$measure->measure_name] = '';
            }

            // get the action plan advices for the user, but only for the resident his input source
            $userActionPlanAdvices = $user
                ->actionPlanAdvices()
                ->forInputSource($inputSourceForDump)
                ->leftJoin('measure_applications', 'user_action_plan_advices.measure_application_id', '=', 'measure_applications.id')
                ->leftJoin('steps', 'measure_applications.step_id', '=', 'steps.id')
                ->orderBy('steps.order')
                ->orderBy('measure_applications.measure_type')
                ->select(['user_action_plan_advices.*'])
                ->get();

            // get the user measures / advices
            foreach ($userActionPlanAdvices as $actionPlanAdvice) {
                $measureName = $actionPlanAdvice->measureApplication->measure_name;

                $plannedYear = UserActionPlanAdviceService::getYear($actionPlanAdvice);

                // fill the measure with the planned year
                $row[$key][$measureName] = $plannedYear;
            }

            $rows = $row;
        }

        array_unshift($rows, $csvHeaders);

        return $rows;
    }

    /**
     * Return the base headers for a csv.
     *
     * @param $anonymize
     */
    public static function getBaseHeaders($anonymize): array
    {
        if ($anonymize) {
            return [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.input-source'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
            ];
        } else {
            return [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.input-source'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
            ];
        }
    }

    /**
     * Method to dump the results of a given questionnaire.
     *
     * @param Cooperation $cooperation
     */
    public static function dumpForQuestionnaire(Questionnaire $questionnaire, bool $anonymize): array
    {
        $cooperation = $questionnaire->cooperation;
        $rows = [];
        $residentRole = Role::findByName('resident');

        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);

        $headers = self::getBaseHeaders($anonymize);

        // get the users from the cooperation, that have a building.
        $users = $cooperation
            ->users()
            ->whereHas('buildings')
            ->get();
        /**
         * @var User $user
         */
        foreach ($users as $user) {
            // reset it for each user.
            $inputSource = $residentRole->inputSource;
            $building = $user->building;
            if ($building instanceof Building) {
                // normally we would pick the given input source
                // but when coach input is available we use the coach input source for that particular user
                // coach input is available when he has completed the questionnaire
                if ($user->hasCompletedQuestionnaire($questionnaire, $coachInputSource)) {
                    $inputSource = $coachInputSource;
                }

                /** @var Collection $conversationRequestsForBuilding */
                $createdAt = optional($user->created_at)->format('Y-m-d');
                $buildingStatus = $building->getMostRecentBuildingStatus()->status->name;
                $allowAccess = PrivateMessage::allowedAccess($building) ? 'Ja' : 'Nee';

                $connectedCoaches = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);
                $connectedCoachNames = [];
                // get the names from the coaches and add them to a array
                foreach ($connectedCoaches->pluck('coach_id') as $coachId) {
                    array_push($connectedCoachNames, User::forMyCooperation($cooperation->id)->find($coachId)->getFullName());
                }
                // implode it.
                $connectedCoachNames = implode($connectedCoachNames, ', ');

                $firstName = $user->first_name;
                $lastName = $user->last_name;
                $email = $user->account->email;
                $phoneNumber = CsvHelper::escapeLeadingZero($user->phone_number);

                $street = $building->street;
                $number = $building->number;
                $city = $building->city;
                $postalCode = $building->postal_code;

                // get the building features from the resident
                $buildingFeatures = $building
                    ->buildingFeatures()
                    ->forInputSource($inputSource)
                    ->first();

                $buildingType = $buildingFeatures->buildingType->name ?? '';
                $buildYear = $buildingFeatures->build_year ?? '';

                // set the personal user info only if the user has question answers.
                if ($anonymize) {
                    $rows[$building->id] = [
                        $inputSource->name, $createdAt, $buildingStatus, $allowAccess, $postalCode, $city,
                        $buildingType, $buildYear,
                    ];
                } else {
                    $rows[$building->id] = [
                        $inputSource->name, $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
                        $firstName, $lastName, $email, $phoneNumber,
                        $street, $number, $postalCode, $city,
                        $buildingType, $buildYear,
                    ];
                }

                $questionAnswerForBuilding = [];
                // note the order, this is important.
                // otherwise the data will be retrieved in a different order each time and that will result in mixed data in the rows
                $questionAnswersForCurrentQuestionnaire =
                    \DB::table('questionnaires')
                        ->where('questionnaires.id', $questionnaire->id)
                        ->join('questions', 'questionnaires.id', '=', 'questions.questionnaire_id')
                        // this may cause weird results, but meh
                        ->whereNull('questions.deleted_at')
                        ->leftJoin('translations', function ($leftJoin) {
                            $leftJoin->on('questions.name', '=', 'translations.key')
                                ->where('language', '=', app()->getLocale());
                        })
                        ->leftJoin('questions_answers',
                            function ($leftJoin) use ($building, $inputSource) {
                                $leftJoin->on('questions.id', '=', 'questions_answers.question_id')
                                    ->where('questions_answers.input_source_id', $inputSource->id)
                                    ->where('questions_answers.building_id', '=', $building->id);
                            })
                        ->select('questions_answers.answer', 'questions.id as question_id', 'translations.translation as question_name', 'questions.deleted_at')
                        ->orderBy('questions.order')
                        ->get();

                foreach ($questionAnswersForCurrentQuestionnaire as $questionAnswerForCurrentQuestionnaire) {
                    $answer = $questionAnswerForCurrentQuestionnaire->answer;
                    $currentQuestion = Question::withTrashed()->find($questionAnswerForCurrentQuestionnaire->question_id);

                    if ($currentQuestion instanceof Question) {
                        // when the question has options, the answer is imploded.
                        if ($currentQuestion->hasQuestionOptions()) {
                            if (!empty($answer)) {
                                // this will contain the question option ids
                                // and filter out the empty answers.
                                $answers = array_filter(explode('|', $answer));

                                $questionAnswers = collect();
                                foreach ($answers as $questionOptionId) {
                                    $questionOption = QuestionOption::find($questionOptionId);
                                    // it is possible a answer options gets removed, but a user still has it saved as an answer.
                                    if ($questionOption instanceof QuestionOption) {
                                        $questionAnswers->push($questionOption->name);
                                    }
                                }
                                $answer = $questionAnswers->implode(' | ');
                            }
                        }
                    }

                    $questionName = "{$questionAnswerForCurrentQuestionnaire->question_id}-{$questionAnswerForCurrentQuestionnaire->question_name}";
                    $rows[$building->id][$questionName] = preg_replace("/\r|\n/", ' ', $answer);
                    $headers[$questionName] = $questionAnswerForCurrentQuestionnaire->question_name;
                }
            }
        }

        array_unshift($rows, $headers);

        return $rows;
    }

    /**
     * Get the total report for all users by the cooperation.
     */
    public static function totalReport(Cooperation $cooperation, InputSource $inputSource, bool $anonymized): array
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        // TODO: Remove this when done
        $start = microtime(true);
        \DB::enableQueryLog();

        $generalDataStep = Step::findByShort('general-data');
        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);
        $users = $cooperation->users()
            ->with(['building' => function ($query) use ($coachInputSource, $generalDataStep) {
                $query->with(['completedSteps' => function ($query) use ($coachInputSource, $generalDataStep) {
                    $query->forInputSource($coachInputSource)->where('step_id', $generalDataStep->id);
                }]);
            }])
            ->has('buildings')
            ->skip(20)
            ->limit(10) // TODO: Remove this when done
            ->get();


        $coachIds = [];
        $residentIds = [];
        // We first check each user
        foreach ($users as $user) {
            // we eager loaded the completed steps from the coach, so if its not empty we use that
            if ($user->building->completedSteps->isNotEmpty()) {
                $coachIds[] = $user->id;
            } else {
                $residentIds[] = $user->id;
            }
        }

        // We separate users based on ID
        $coaches = $users->whereIn('id', $coachIds);
        $residents = $users->whereIn('id', $residentIds);

        // Then we lazy eagerload all the data with the right input source in one go
        $coaches->load(
            ['building' => function ($query) use ($coachInputSource) {
                $query->with(
                    [
                        'buildingFeatures' => function ($query) use ($coachInputSource) {
                            $query->forInputSource($coachInputSource)
                            ->with([
                                'roofType', 'energyLabel', 'damagedPaintwork', 'buildingHeatingApplication', 'plasteredSurface',
                                'contaminatedWallJoints', 'wallJoints'
                            ]);
                        },
                        'buildingVentilations' => function ($query) use ($coachInputSource) {
                            $query->forInputSource($coachInputSource);
                        },
                        'buildingServices' => function ($query) use ($coachInputSource) {
                            $query->forInputSource($coachInputSource);
                        }
                    ]
                );
            }, 'energyHabit' => function ($query) use ($coachInputSource) {
                $query->forInputSource($coachInputSource);
            }]
        );

        $residents->load(
            ['building' => function ($query) use ($inputSource) {
                $query->with(
                    [
                        'buildingFeatures' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource)
                                ->with([
                                    'roofType', 'energyLabel', 'damagedPaintwork', 'buildingHeatingApplication', 'plasteredSurface',
                                    'contaminatedWallJoints', 'wallJoints'
                                ]);
                        },
                        'buildingVentilations' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        },
                        'buildingServices' => function ($query) use ($inputSource) {
                            $query->forInputSource($inputSource);
                        }
                    ]
                );
            }, 'energyHabit' => function ($query) use ($inputSource) {
                $query->forInputSource($inputSource);
            }]
        );
        // Then we merge
        $users = $residents->merge($coaches);

        $rows = [];

        $headers = DumpService::getStructureForTotalDumpService($anonymized);
        $newHeaders = DumpService::dissectHeaders($headers);

        $rows[] = $headers;

        /**
         * Get the data for every user.
         *
         * @var User $user
         */


        foreach ($users as $user) {
            $inputSource = $user->building->buildingFeatures->inputSource;

            $rows[$user->building->id] = DumpService::totalDump($newHeaders, $cooperation, $user, $inputSource, $anonymized, false)['user-data'];
        }
        // TODO: Remove this when done
        $stop = microtime(true);
        dd($stop - $start, $rows[$users->first()->building->id], \DB::getQueryLog());

        return $rows;
    }


    protected static function formatFieldOutput($column, $value, $maybe1, $maybe2)
    {
        $decimals = 0;
        $shouldRound = false;

        if (self::isYear($column) || self::isYear($maybe1, $maybe2)) {
            return $value;
        }

        if (!is_numeric($value)) {
            return $value;
        }

        if (in_array($column, ['interest_comparable'])) {
            $decimals = 1;
        }
        if ('specs' == $column && 'size_collector' == $maybe1) {
            $decimals = 1;
        }
        if ('paintwork' == $column && 'costs' == $maybe1) {
            /// round the cost for paintwork
            $shouldRound = true;
        }

        return self::formatOutput($column, $value, $decimals, $shouldRound);
    }

    /**
     * Format the output of the given column and value.
     *
     * @param string $column
     * @param mixed $value
     * @param int $decimals
     * @param bool $shouldRound
     *
     * @return float|int|string
     */
    protected static function formatOutput($column, $value, $decimals = 0, $shouldRound = false)
    {
        if (in_array($column, ['percentage_consumption']) ||
            false !== stristr($column, 'savings_') ||
            stristr($column, 'cost')) {
            $value = NumberFormatter::round($value);
        }
        if ($shouldRound) {
            $value = NumberFormatter::round($value);
        }
        // We should let Excel do the separation of thousands
        return number_format($value, $decimals, ',', '');
        //return NumberFormatter::format($value, $decimals, $shouldRound);
    }

    protected static function translateExtraValueIfNeeded($value)
    {
        if (in_array($value, ['yes', 'no', 'unknown'])) {
            $key = 'general.options.%s.title';

            return Translation::translate(sprintf($key, $value));
        }
    }

    /**
     * Returns whether or not two (optional!) columns contain a year or not.
     *
     * @param string $column
     * @param string $extraValue
     *
     * @return bool
     */
    protected static function isYear($column, $extraValue = '')
    {
        if (!is_null($column)) {
            if (false !== stristr($column, 'year')) {
                return true;
            }
            if ('extra' == $column) {
                return in_array($extraValue, [
                    'year',
                ]);
            }
        }

        return false;
    }
}
