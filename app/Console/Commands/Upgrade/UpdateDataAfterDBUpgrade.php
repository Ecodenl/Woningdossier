<?php

namespace App\Console\Commands\Upgrade;

use App\Models\ToolQuestion;
use App\Models\ToolQuestionValuable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateDataAfterDBUpgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:after-changes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var ToolQuestion $wall */
        $wall = ToolQuestion::where('short', '=', 'current-wall-insulation')->first();
        $other = $wall->toolQuestionValuables()->where('extra', 'like', '%icon-other%')->first();
        if ($other->order <= 0){
            $this->line("Changing order for wall insulation");
            $max = DB::table('tool_question_valuables')->where('tool_question_id', '=', $wall->id)->max('order');
            $other->order = $max+1; // just put at the end. It will be corrected by the query.
            $other->save();
            DB::table('tool_question_valuables')->where('tool_question_id', '=', $wall->id)->update(['order' => DB::raw('`order` - 1')]);
        }

        $floor = ToolQuestion::where('short', '=', 'current-floor-insulation')->first();
        $otherAndNvt = $floor->toolQuestionValuables()->where('extra', 'like', '%icon-other%')->get();
        if ($otherAndNvt->first()->order <= 0){
            $this->line("Changing order for floor insulation");
            $max = DB::table('tool_question_valuables')->where('tool_question_id', '=', $floor->id)->where('extra', 'not like', '%icon-other%')->max('order');
            $next = $max+1;
            foreach($otherAndNvt as $i => $floorItem){
                $floorItem->order = $next + $i;
                $floorItem->save();
            }
            DB::table('tool_question_valuables')->where('tool_question_id', '=', $floor->id)->update(['order' => DB::raw('`order` - 1')]);
        }

        $roof = ToolQuestion::where('short', '=', 'current-roof-insulation')->first();
        $otherAndNvt = $roof->toolQuestionValuables()->where('extra', 'like', '%icon-other%')->get();
        if ($otherAndNvt->first()->order <= 0){
            $this->line("Changing order for roof insulation");
            $max = DB::table('tool_question_valuables')->where('tool_question_id', '=', $roof->id)->where('extra', 'not like', '%icon-other%')->max('order');
            $next = $max+1;
            foreach($otherAndNvt as $i => $roofItem){
                $roofItem->order = $next + $i;
                $roofItem->save();
            }
            DB::table('tool_question_valuables')->where('tool_question_id', '=', $roof->id)->update(['order' => DB::raw('`order` - 1')]);
        }

    }
}
