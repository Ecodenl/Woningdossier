<?php

namespace App\Helpers;

use App\Scopes\GetValueScope;
use Illuminate\Database\Eloquent\Relations\Relation;

class Hoomdossier
{
    public static function convertDecimal($input)
    {
        return str_replace(',', '.', $input);
    }

    public static function getMostCredibleValue(Relation $relation, $column){
	    $found = $relation
		    ->withoutGlobalScope(GetValueScope::class)
		    ->join('input_sources', $relation->getRelated()->getTable() . '.input_source_id', '=', 'input_sources.id')
		    ->orderBy('input_sources.order', 'ASC')
		    ->get([$relation->getRelated()->getTable() . '.*', 'input_sources.short']);

	    $results = $found->pluck($column, 'short');

	    // treating them in order
	    foreach($results as $inputSourceShort => $value){
	    	if ($inputSourceShort == 'resident'){
	    		// no matter what
	    		return $value;
		    }
	    	if (!is_null($value) && $value != ""){
				return $value;
		    }
	    }
	    // No value found
	    return null;
	}

}
