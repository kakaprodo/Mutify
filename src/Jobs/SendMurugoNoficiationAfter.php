<?php

namespace RWBuild\Mutify\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExecuteNotificationAfter implements ShouldQueue
{
    /**
     * ExecuteNotificationAfter
     * ------------------------------------------------------------------------
     * This will execute murogo notifications that have been queued
     */

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $endpoint;

    public $requestData;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $endpoint, array $requestData)
    {
        $this->endpoint = $endpoint;

        $this->requestData = $requestData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        
        $client->request('POST', $this->endpoint, [
            'form_params' => $this->requestData
        ]);
    }
}
