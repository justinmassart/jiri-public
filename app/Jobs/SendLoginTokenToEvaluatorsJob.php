<?php

namespace App\Jobs;

use App\Mail\SendLoginTokenToEvaluatorsMail;
use App\Models\AccessToken;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLoginTokenToEvaluatorsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AccessToken $accessToken,
        public Contact $contact
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->contact)->send(new SendLoginTokenToEvaluatorsMail($this->accessToken, $this->contact));
    }
}
