<?php

namespace App\Mail;

use App\Models\ModCommentReport;
use App\Models\ModReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportComment extends Mailable
{
    use Queueable, SerializesModels;

    private ModCommentReport $report;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ModCommentReport $report)
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
        return $this->markdown('emails.reportComment', [
            'report' => $this->report
        ]);
    }
}
