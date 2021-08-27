<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Models\BuildingType;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Closure;

class ChecksConditionsForSubSteps
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var SubStep $subStep */
        $subStep = $request->route('subStep');

        $returnToNextStep = $request->user()->cannot('show', $subStep);

        // Custom conditional show stuff, should be refactored to class per tool question or sum
        if ($subStep->toolQuestions->contains('short', 'building-type')) {
            $building = HoomdossierSession::getBuilding(true);
            // check what type of building category the user has selected, we will determine if we should show this page or the next one
            // check what kind of category the user has selected, it will determine whether we have to show the building type or not.
            $buildingTypeCategoryId = $building->getAnswer(
                InputSource::findByShort(InputSource::MASTER_SHORT),
                ToolQuestion::findByShort('building-type-category')
            );

            // only one option would mean there are no multiple building types for the category, thus the page is redundant.
            // so multiple building types = next step.
            $returnToNextStep = BuildingType::where('building_type_category_id', $buildingTypeCategoryId)->count() <= 1;
        }


        if ($returnToNextStep) {
            // this indeed only covers the next step
            return redirect()->to(QuickScanHelper::getNextStepUrl($request->route('step'), $subStep));
        }

        return $next($request);
    }
}
