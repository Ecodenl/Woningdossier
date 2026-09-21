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

        $withdrawn = SmartTwinSolution::withdrawn()->pluck('id')->flip();

        // Grouped by kind, because the products of one kind are usually the same measure to us and
        // deciding them together is far less work than 138 separate calls.
        $kinds = $solutions->groupBy(fn (SmartTwinSolution $solution) => $solution->kind ?? '')->sortKeys();

        // Listed on their short, with the name behind it. A measure application's name is editable
        // in the admin, the short is what the mapping resolves to — so the short is what somebody
        // coupling needs to recognise.
        $measureApplications = MeasureApplication::with('step')
            ->get()
            ->sortBy('short')
            ->groupBy(fn (MeasureApplication $measure) => $measure->step->name ?? '');

        $undecided = $solutions->reject(fn (SmartTwinSolution $s) => array_key_exists($s->id, $couplings))->count();
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

        // The form is built from this table, so a key that is not in it was never on the page.
        $solutions = SmartTwinSolution::whereIn('id', array_keys($submitted))->get()->keyBy('id');
        $current = $this->couplings();
        $measures = MeasureApplication::whereIn('id', array_filter($submitted, 'is_numeric'))->get()->keyBy('id');

        $changed = 0;

        foreach ($submitted as $solutionId => $choice) {
            $solution = $solutions->get((int) $solutionId);

            if (! $solution instanceof SmartTwinSolution || $this->unchanged($current, $solution->id, $choice)) {
                continue;
            }

            $service = MappingService::init()->from($solution)->type(SmartTwinSolution::mappingType());

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
     * What each coupled solution currently points at: a measure application id, or null for one
     * deliberately left uncoupled. A solution absent here has not been decided on.
     *
     * An array rather than a collection, because it is passed on and Collection's value type is
     * invariant — a narrowed generic would not be accepted as an argument.
     *
     * @return array<int, int|null>
     */
    private function couplings(): array
    {
        return Mapping::forType(SmartTwinSolution::mappingType())
            ->where('from_model_type', (new SmartTwinSolution())->getMorphClass())
            ->pluck('target_model_id', 'from_model_id')
            ->all();
    }

    /**
     * @param  array<int, int|null>  $current
     */
    private function unchanged(array $current, int $solutionId, string $choice): bool
    {
        if (! array_key_exists($solutionId, $current)) {
            return '' === $choice;
        }

        $coupledTo = $current[$solutionId];

        return is_null($coupledTo)
            ? self::NOT_COUPLED === $choice
            : (string) $coupledTo === $choice;
    }
}
