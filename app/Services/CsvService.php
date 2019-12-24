<?php

namespace App\Services;

use App\Helpers\Arr;
use App\Helpers\FileFormats\CsvHelper;
use App\Helpers\NumberFormatter;
use App\Helpers\ToolHelper;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\CompletedStep;
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
use App\Models\UserActionPlanAdvice;
use App\Scopes\CooperationScope;
use Barryvdh\Debugbar\Twig\Extension\Dump;
use Illuminate\Support\Collection;
use Spatie\TranslationLoader\TranslationLoaders\Db;

class CsvService
{
    /**
     * CSV Report that returns the measures with year with full address data.
     *
     * @param $cooperation
     * @param $anonymize
     *
     * @return array
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

            // well in every case there is a uitzondering op de regel
            // normally we would pick the given input source
            // but when coach input is available we use the coach input source for that particular user
            // coach input is available when he has completed the general data step
            if ($user->building->hasCompleted($generalDataStep, $coachInputSource)) {
                $inputSourceForDump = $coachInputSource;
            }

            /** @var Building $building */
            $building = $user->building;

            /** @var Collection $conversationRequestsForBuilding */
            $conversationRequestsForBuilding = PrivateMessage::withoutGlobalScope(new CooperationScope())
                ->conversationRequestByBuildingId($building->id)
                ->where('to_cooperation_id', $cooperation->id)->get();

            $createdAt = optional($user->created_at)->format('Y-m-d');
            //$buildingStatus      = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
            $buildingStatus = $building->getMostRecentBuildingStatus()->status->name;
            $allowAccess = $conversationRequestsForBuilding->contains('allow_access', true) ? 'Ja' : 'Nee';
            $connectedCoaches = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);
            $connectedCoachNames = [];
            // get the names from the coaches and add them to a array
            foreach ($connectedCoaches->pluck('coach_id') as $coachId) {
                array_push($connectedCoachNames, User::find($coachId)->getFullName());
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
                ->forInputSource($inputSourceForDump)
                ->first();

            $buildingType = $buildingFeatures->buildingType->name ?? '';
            $buildYear = $buildingFeatures->build_year ?? '';
            $exampleBuilding = optional($building->exampleBuilding)->isSpecific() ? $building->exampleBuilding->name : '';

            if ($anonymize) {
                // set the personal userinfo
                $row[$key] = [
                    $inputSourceForDump->name, $createdAt, $buildingStatus, $postalCode, $city,
                    $buildingType, $buildYear, $exampleBuilding,
                ];
            } else {
                // set the personal userinfo
                $row[$key] = [
                    $inputSourceForDump->name, $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
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
     * CSV Report that returns the questionnaire results.
     *
     * @param Cooperation $cooperation
     * @param bool $anonymize
     *
     * @return array
     */
    public static function questionnaireResults(Cooperation $cooperation, bool $anonymize): array
    {
        $questionnaires = Questionnaire::withoutGlobalScope(new CooperationScope())
            ->where('cooperation_id', $cooperation->id)
            ->get();
        $rows = [];

        $residentInputSource = InputSource::findByShort('resident');

        if ($anonymize) {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
            ];
        } else {
            $headers = [
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

        // get the users from the current cooperation that have the resident role
        $usersFromCooperation = $cooperation->getUsersWithRole(Role::findByName('resident'));

        /** @var User $user */
        foreach ($usersFromCooperation as $user) {
            $building = $user->building;
            if ($building instanceof Building && $user->hasRole('resident', $cooperation->id)) {
                /** @var Collection $conversationRequestsForBuilding */
                $conversationRequestsForBuilding = PrivateMessage::withoutGlobalScope(new CooperationScope())
                    ->conversationRequestByBuildingId($building->id)
                    ->where('to_cooperation_id', $cooperation->id)->get();

                $createdAt = optional($user->created_at)->format('Y-m-d');
                //$buildingStatus      = BuildingCoachStatus::getCurrentStatusForBuildingId($building->id);
                $buildingStatus = $building->getMostRecentBuildingStatus()->status->name;
                $allowAccess = $conversationRequestsForBuilding->contains('allow_access', true) ? 'Ja' : 'Nee';
                $connectedCoaches = BuildingCoachStatus::getConnectedCoachesByBuildingId($building->id);
                $connectedCoachNames = [];
                // get the names from the coaches and add them to a array
                foreach ($connectedCoaches->pluck('coach_id') as $coachId) {
                    array_push($connectedCoachNames, User::find($coachId)->getFullName());
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
                    ->forInputSource($residentInputSource)
                    ->first();

                $buildingType = $buildingFeatures->buildingType->name ?? '';
                $buildYear = $buildingFeatures->build_year ?? '';

                // set the personal user info only if the user has question answers.
                if ($building->questionAnswers()->forInputSource($residentInputSource)->count() > 0) {
                    if ($anonymize) {
                        $rows[$building->id] = [
                            $createdAt, $buildingStatus, $allowAccess, $postalCode, $city,
                            $buildingType, $buildYear,
                        ];
                    } else {
                        $rows[$building->id] = [
                            $createdAt, $buildingStatus, $allowAccess, $connectedCoachNames,
                            $firstName, $lastName, $email, $phoneNumber,
                            $street, $number, $postalCode, $city,
                            $buildingType, $buildYear,
                        ];
                    }
                }
                foreach ($questionnaires as $questionnaire) {
                    $questionAnswersForCurrentQuestionnaire =
                        \DB::table('questionnaires')
                            ->where('questionnaires.id', $questionnaire->id)
                            ->join('questions', 'questionnaires.id', '=', 'questions.questionnaire_id')
                            ->leftJoin('translations', function ($leftJoin) {
                                $leftJoin->on('questions.name', '=', 'translations.key')
                                    ->where('language', '=', app()->getLocale());
                            })
                            ->leftJoin('questions_answers',
                                function ($leftJoin) use ($building) {
                                    $leftJoin->on('questions.id', '=', 'questions_answers.question_id')
                                        ->where('questions_answers.building_id', '=', $building->id);
                                })
                            ->select('questions_answers.answer', 'questions.id as question_id',
                                'translations.translation as question_name')
                            ->get();

                    // loop through the answers for ONE questionnaire
                    foreach ($questionAnswersForCurrentQuestionnaire as $questionAnswerForCurrentQuestionnaire) {
                        $answer = $questionAnswerForCurrentQuestionnaire->answer;
                        $currentQuestion = Question::withTrashed()->find($questionAnswerForCurrentQuestionnaire->question_id);

                        // check if the question
                        if ($currentQuestion instanceof Question) {
                            // if the question has options, we have to get the translations from that table otherwise there would be ids in the csv
                            if ($currentQuestion->hasQuestionOptions()) {
                                $questionOptionAnswer = [];

                                // explode on array since some questions are multi select
                                $explodedAnswers = explode('|', $answer);

                                foreach ($explodedAnswers as $explodedAnswer) {
                                    // check if the current question has options
                                    // the question can contain a int but can be a answer to a question like "How old are you"
                                    if ($currentQuestion->hasQuestionOptions() && !empty($explodedAnswer)) {
                                        $questionOption = QuestionOption::find($explodedAnswer);
                                        array_push($questionOptionAnswer, $questionOption->name);
                                    }
                                }

                                // the questionOptionAnswer can be empty if the the if statements did not pass
                                // so we check that before assigning it.
                                if (!empty($questionOptionAnswer)) {
                                    // implode it
                                    $answer = implode($questionOptionAnswer, '|');
                                }
                            }
                            // set the question name in the headers
                            // yes this overwrites it all the time, but thats the point.
                            $headers[$questionAnswerForCurrentQuestionnaire->question_name] = $questionAnswerForCurrentQuestionnaire->question_name;

                            // set the question answer
                            $rows[$building->id][] = $answer;
                        }
                    }
                }
            }
        }

        // unset the whole empty arrays
        // so we only set rows with answers.
        foreach ($rows as $buildingId => $row) {
            if (Arr::isWholeArrayEmpty($row)) {
                unset($rows[$buildingId]);
            }
        }

        array_unshift($rows, $headers);

        return $rows;
    }

    /**
     * Get the total report for all users by the cooperation.
     *
     * @param Cooperation $cooperation
     * @param InputSource $inputSource
     * @param bool $anonymized
     *
     * @return array
     */
    public static function totalReport(Cooperation $cooperation, InputSource $inputSource, bool $anonymized): array
    {
        $users = $cooperation->users()->whereHas('buildings')->get();

        $rows = [];

        $headers = DumpService::getStructureForTotalDumpService($anonymized);

        $rows[] = $headers;

        /**
         * Get the data for every user.
         *
         * @var User $user
         */
        $generalDataStep = Step::findByShort('general-data');
        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);

        foreach ($users as $user) {
            // for each user reset the input source back to the base input source.
            $inputSourceForDump = $inputSource;

            // well in every case there is a uitzondering op de regel
            // normally we would pick the given input source
            // but when coach input is available we use the coach input source for that particular user
            // coach input is available when he has completed the general data step
            if ($user->building->hasCompleted($generalDataStep, $coachInputSource)) {
                $inputSourceForDump = $coachInputSource;
            }

            $rows[$user->building->id] = DumpService::totalDump($headers, $cooperation, $user, $inputSourceForDump, $anonymized, false)['user-data'];
        }

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
