<?php

namespace App\Enums;

enum MappingType: string
{
    case BAG_MUNICIPALITY = 'bag-municipality';
    case MUNICIPALITY_VBJEHUIS = 'municipality-vbjehuis';

    case MEASURE_CATEGORY_VBJEHUIS = 'measure-category-vbjehuis';

    case MEASURE_APPLICATION_MEASURE_CATEGORY = 'measure-application-measure-category';
    case COOPERATION_MEASURE_APPLICATION_MEASURE_CATEGORY = 'cooperation-measure-application-measure-category';
    case CUSTOM_MEASURE_APPLICATION_MEASURE_CATEGORY = 'custom-measure-application-measure-category';

    /**
     * From a SmartTwin solution id to the measure application it is.
     *
     * Their catalogue grows without the API changing, so this one has to be addable without a
     * deploy. An id with no row here is reported rather than guessed at.
     */
    case SMARTTWIN_SOLUTION_MEASURE_APPLICATION = 'smarttwin-solution-measure-application';
}
