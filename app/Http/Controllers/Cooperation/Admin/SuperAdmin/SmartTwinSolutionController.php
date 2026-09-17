<?php

namespace App\Http\Controllers\Cooperation\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\SuperAdmin\SmartTwinSolutionCoupleRequest;
use App\Models\Cooperation;
use App\Models\Mapping;
use App\Models\MeasureApplication;
use App\Models\SmartTwinSolution;
use App\Services\MappingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Couples SmartTwin's solution catalogue to our measure applications.
 *
 * SmartTwin advises a product; we advise a measure. Which is which cannot be derived — their
 * `Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls` is our `cavity-wall-insulation`
 * only because somebody who knows both says so — so this screen is where that knowledge is
 * recorded. Uncoupled products reach no woonplan and are reported as unmapped.
 *
 * Three states, and the difference between the first two matters: a product nobody has looked at
 * yet, one deliberately left uncoupled because we have no such measure, and one coupled. Only the
 * first is an open decision.
 */
class SmartTwinSolutionController extends Controller
{
    /** The value the form submits for a product deliberately left uncoupled. */
    public const NOT_COUPLED = 'none';

    public function index(Cooperation $cooperation): View
    {
        $solutions = SmartTwinSolution::orderBy('kind')->orderBy('name')->get();
        $couplings = $this->couplings();

        $withdrawn = SmartTwinSolution::withdrawn()->pluck('external_id')->flip();

        // Grouped by kind, because the products of one kind are usually the same measure to us and
        // deciding them together is far less work than 138 separate calls.
        $kinds = $solutions->groupBy(fn (SmartTwinSolution $solution) => $solution->kind ?? '')->sortKeys();

        $measureApplications = MeasureApplication::with('step')
            ->get()
            ->sortBy('name')
            ->groupBy(fn (MeasureApplication $measure) => $measure->step->name ?? '');

        $undecided = $solutions->reject(fn (SmartTwinSolution $s) => $couplings->has($s->external_id))->count();
        $notCoupled = self::NOT_COUPLED;

        return view('cooperation.admin.super-admin.smart-twin-solutions.index', compact(
            'kinds',
            'couplings',
            'withdrawn',
            'measureApplications',
            'undecided',
            'notCoupled',
        ));
    }

    public function couple(SmartTwinSolutionCoupleRequest $request, Cooperation $cooperation): RedirectResponse
    {
        $submitted = $request->validated()['couplings'] ?? [];

        $known = SmartTwinSolution::pluck('external_id')->flip();
        $current = $this->couplings();
        $measures = MeasureApplication::whereIn('id', array_filter($submitted, 'is_numeric'))->get()->keyBy('id');

        $changed = 0;

        foreach ($submitted as $externalId => $choice) {
            // The form is built from this table, so anything else was not on the page. Writing it
            // would couple a product nobody can see here.
            if (! $known->has($externalId)) {
                continue;
            }

            if ($this->unchanged($current, $externalId, $choice)) {
                continue;
            }

            $service = MappingService::init()->from($externalId)->type(SmartTwinSolution::mappingType());

            if ('' === $choice) {
                // Back to undecided, which is the absence of a row rather than a row saying no.
                $service->detach();
            } else {
                $measure = self::NOT_COUPLED === $choice ? null : $measures->get((int) $choice);

                // A targetless row is how "deliberately not coupled" is recorded; see MappingService.
                $service->sync(is_null($measure) ? [] : [$measure], SmartTwinSolution::mappingType());
            }

            ++$changed;
        }

        return to_route('cooperation.admin.super-admin.smart-twin-solutions.index')
            ->with('success', trans_choice(
                'cooperation/admin/super-admin/smart-twin-solutions.couple.success',
                $changed,
                ['count' => $changed],
            ));
    }

    /**
     * What each coupled solution id currently points at: a measure application id, or null for one
     * deliberately left uncoupled. A solution id absent here has not been decided on.
     *
     * @return Collection<string, int|null>
     */
    private function couplings(): Collection
    {
        return Mapping::forType(SmartTwinSolution::mappingType())
            ->whereNotNull('from_value')
            ->pluck('target_model_id', 'from_value');
    }

    /**
     * @param  Collection<string, int|null>  $current
     */
    private function unchanged(Collection $current, string $externalId, string $choice): bool
    {
        if (! $current->has($externalId)) {
            return '' === $choice;
        }

        $coupledTo = $current->get($externalId);

        return is_null($coupledTo)
            ? self::NOT_COUPLED === $choice
            : (string) $coupledTo === $choice;
    }
}
