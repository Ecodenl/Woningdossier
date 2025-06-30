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
}
