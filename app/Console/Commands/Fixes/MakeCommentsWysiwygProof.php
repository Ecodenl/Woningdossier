<?php

namespace App\Console\Commands\Fixes;

use App\Helpers\Sanitizers\HtmlSanitizer;
use App\Helpers\Sanitizers\Sanitizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MakeCommentsWysiwygProof extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixes:make-comments-wysiwyg-proof';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts old non-wysiwyg comments into wysiwyg format';

    protected Sanitizer $sanitizer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->sanitizer = new HtmlSanitizer();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tables = [
            'step_comments',
            'user_action_plan_advice_comments',
        ];

        foreach ($tables as $table) {
            DB::table($table)->where('comment', '!=', '')
                ->whereRaw('`comment` NOT LIKE "<p>%"')
                ->orderBy('id')
                ->eachById(function ($commentable) use ($table) {
                    $comment = $this->formatComment($commentable->comment);
                    DB::table($table)->where('id', $commentable->id)->update(compact('comment'));
                });
        }


        return 0;
    }

    private function formatComment(string $comment): string
    {
        return '<p>' . nl2br(trim($this->sanitizer->sanitize($comment))) . '</p>';
    }
}
