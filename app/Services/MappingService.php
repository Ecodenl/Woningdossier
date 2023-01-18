<?php

namespace App\Services;

use App\Models\Mapping;
use App\Traits\FluentCaller;
use Illuminate\Database\Eloquent\Model;

class MappingService
{
    use FluentCaller;

    public $from;
    public $target;

    public function __construct() {}

    public function from($from)
    {
        $this->from = $from;
        return $this;
    }

    public function target($target)
    {
        $this->target = $target;
        return $this;
    }

    public function sync(): Mapping
    {
        $where = [];
        $mapping = [];

        if ($this->from instanceof Model) {
            $where['from_model_type'] = $this->from->getMorphClass();
            $where['from_model_id'] = $this->from->id;
        } else {
            $where['from_value'] = $this->from;
        }

        if ($this->target instanceof Model) {
            $mapping['target_model_type'] = $this->target->getMorphClass();
            $mapping['target_model_id'] = $this->target->id;
        } else {
            if (is_array($this->target)) {
                $mapping['target_data'] = $this->target;
            } else {
                $mapping['target_value'] = $this->target;
            }
        }

        return Mapping::updateOrCreate($where, $mapping);
    }
}