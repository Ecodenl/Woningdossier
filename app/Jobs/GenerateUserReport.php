<?php

namespace App\Jobs;

use App\Models\Cooperation;
use App\Models\User;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateUserReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $cooperation;
    public $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Cooperation $cooperation
     * @return void
     */
    public function __construct(Cooperation $cooperation, User $user)
    {
        $this->cooperation = $cooperation;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = PDF::loadView('cooperation.pdf.user-report.index', ['cooperation' => $this->cooperation, 'user' => $this->user]);

        $pdfOptions = $pdf->getDomPDF()->getOptions();
        $pdfOptions->setIsPhpEnabled(true);

        \Storage::disk('downloads')->put('test.pdf', $pdf->output());
    }
}
