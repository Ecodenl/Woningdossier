<?php

namespace App\Jobs;

use App\Helpers\NumberFormatter;
use App\Helpers\ToolHelper;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\User;
use App\Scopes\GetValueScope;
use App\Services\BuildingCoachStatusService;
use App\Services\DumpService;
use App\Services\Models\AlertService;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
use Throwable;

class PdfReport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected User $user;
    protected FileType $fileType;
    protected FileStorage $fileStorage;
    protected Scan $scan;

    /**
     * PdfReport constructor.
     */
    public function __construct(User $user, FileType $fileType, FileStorage $fileStorage, Scan $scan)
    {
        $this->fileType = $fileType;
        $this->fileStorage = $fileStorage;
        $this->user = $user;
        $this->scan = $scan;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (App::runningInConsole()) {
            Log::debug(__CLASS__ . ' Is running in the console with a maximum execution time of: ' . ini_get('max_execution_time'));
        }

        $scanShort = $this->scan->short;
        if ($scanShort === Scan::LITE) {
            $short = ToolHelper::STRUCT_PDF_LITE;
        } else {
            $short = ToolHelper::STRUCT_PDF_QUICK;
        }

        // Always retrieve from master.
        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $user = $this->user;
        $building = $user->building;
        // Note the term 'userCooperation' instead of "cooperation". It is of ABSOLUTE IMPORTANCE this is NOT named
        // 'cooperation', as this will conflict with the CooperationComposer and will leave the variable as null and
        // therefore not usable!
        $userCooperation = $user->cooperation;

        $dumpService = DumpService::init()
            ->user($user)
            ->inputSource($inputSource)
            ->setMode(DumpService::MODE_PDF)
            ->anonymize() // See comment above unset below
            ->createHeaderStructure($short, false);

        // Retrieve headers AFTER the dump is done, as conditionally incorrect data will be removed.
        $dump = $dumpService->generateDump();
        $headers = $dumpService->headerStructure;

        // So we don't use the initial headers (currently). Therefore, we anonymize, as then we only have to unset
        // the first four keys.
        unset(
            $dump[0],
            $dump[1],
            $dump[2],
            $dump[3],
        );

        $simpleDump = [];
        $expertDump = [];
        $expertStepShorts = Scan::findByShort(Scan::EXPERT)->steps()->pluck('short')->toArray();

        // Manipulate the dump so it's categorized by step.
        foreach ($dump as $key => $data) {
            // Step short is at first dot
            $parts = explode('.', $key, 2);
            $stepShort = $parts[0];
            $key = $parts[1];

            if (in_array($stepShort, $expertStepShorts)) {
                $expertDump[$stepShort][$key] = $data;
            } else {
                $simpleDump[$stepShort][$key] = $data;
            }
        }

        $coachHelp = [];

        if (array_key_exists('small-measures-lite', $simpleDump)) {
            $data = $simpleDump['small-measures-lite'];
            // In case of the lite scan, we need to move and merge some data.
            foreach ($data as $key => $value) {
                if (Str::contains($key, 'label_')) {
                    $coachHelp[$key] = $value;
                } elseif (Str::endsWith($key, '-coach-help')) {
                    // We don't want the coach help values here... so we move them.
                    $dataKey = Str::replaceLast('-coach-help', '', $key);
                    $coachHelp[$dataKey] = $value;
                    unset($data[$key]);
                } elseif (Str::endsWith($key, '-how')) {
                    // We do want the how values but not standalone.
                    if ($value !== __('cooperation/frontend/tool.no-answer-given')) {
                        $dataKey = Str::replaceLast('-how', '', $key);
                        $data[$dataKey] .= "; {$value}";
                    }
                    unset($data[$key]);
                }
            }
            $simpleDump['small-measures-lite'] = $data;

            // Now we need to remove empty labels from the coach help, else it looks weird...
            $previousKey = '';
            foreach ($coachHelp as $key => $value) {
                if (! empty($previousKey)) {
                    if (Str::contains($key, 'label_') && Str::contains($previousKey, 'label_')) {
                        unset($coachHelp[$previousKey]);
                    }
                }
                $previousKey = $key;
            }

            // Last short in the array, if this is also a label it's wrong
            if (Str::contains($previousKey, 'label_')) {
                unset($coachHelp[$previousKey]);
            }
        }

        $categorizedAdvices = $user->userActionPlanAdvices()
            ->forInputSource($inputSource)
            ->withoutDeletedCooperationMeasureApplications($inputSource)
            ->with(['userActionPlanAdvisable' => fn ($q) => $q->withoutGlobalScope(GetValueScope::class)])
            ->getCategorized()
            ->map(function ($advices) {
                return $advices->map(function ($userActionPlanAdvice) {
                    if ($userActionPlanAdvice->category !== UserActionPlanAdviceService::CATEGORY_COMPLETE) {
                        $costs = $userActionPlanAdvice->costs ?? [];
                        $from = $costs['from'] ?? null;
                        $to = $costs['to'] ?? null;

                        if (! is_null($from)) {
                            NumberFormatter::round($from);
                        }
                        if (! is_null($to)) {
                            NumberFormatter::round($to);
                        }

                        $userActionPlanAdvice->costs = NumberFormatter::range($from, $to);
                        $userActionPlanAdvice->savings_money = NumberFormatter::round($userActionPlanAdvice->savings_money ?? 0);
                    }

                    return $userActionPlanAdvice;
                });
            });

        $adviceComments = $user->userActionPlanAdviceComments()
            ->allInputSources()
            ->with('inputSource')
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->get();

        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building->id);
        $connectedCoachNames = User::whereIn('id', $connectedCoaches->pluck('coach_id')->toArray())
            ->selectRaw("CONCAT(first_name, ' ', last_name) AS full_name")
            ->pluck('full_name')
            ->toArray();

        $alerts = AlertService::init()
            ->inputSource($inputSource)
            ->building($building)
            ->getAlerts();

        // https://github.com/mccarlosen/laravel-mpdf
        // To style container margins of the PDF, see config/pdf.php
        $pdf = LaravelMpdf::loadView('cooperation.pdf.user-report.index', compact(
            'scanShort',
            'userCooperation',
            'building',
            'user',
            'inputSource',
            'connectedCoachNames',
            'headers',
            'simpleDump',
            'expertDump',
            'coachHelp',
            'categorizedAdvices',
            'adviceComments',
            'alerts',
        ))->output();

        // save the pdf report
        Storage::disk('downloads')->put($this->fileStorage->filename, $pdf);

        $this->fileStorage->isProcessed();
    }

    public function failed(Throwable $exception)
    {
        $this->fileStorage->delete();

        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }
    }
}
