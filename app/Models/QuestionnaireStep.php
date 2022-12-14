<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\CooperationScan
 *
 * @property int $id
 * @property int $cooperation_id
 * @property int $scan_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationScan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationScan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationScan query()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationScan whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationScan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationScan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationScan whereScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationScan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionnaireStep extends Pivot
{
    use HasFactory;

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}
