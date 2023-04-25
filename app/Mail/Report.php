<?php

namespace App\Mail;

use App\Models\ModReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Report extends Mailable
{
    use Queueable, SerializesModels;

    private ModReport $report;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ModReport $report)
    {
        $this->report = $report;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.report', [
            'report' => $this->report
        ]);
    }
}
