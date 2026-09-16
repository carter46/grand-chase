<?php

namespace App\Console\Commands;

use App\Services\SeventhTradeHub\SeventhTradeHubService;
use Illuminate\Console\Command;

class SeventhTradeHubPollCommand extends Command
{
    protected $signature = 'seventh-tradehub:poll';

    protected $description = 'Poll 7th Trade Hub owned subscription and drain credential sync outbox';

    public function handle(SeventhTradeHubService $hub)
    {
        $drained = $hub->drainCredentialSyncOutbox(10);
        $this->info('Credential outbox drained: ' . $drained);

        $owned = $hub->getByContext(SeventhTradeHubService::CONTEXT_OWNED);
        if ($owned && $hub->isIntegrationOperational($owned)) {
            $body = $hub->pollSubscription($owned);
            if ($body === null) {
                $this->warn('Subscription poll failed or returned nothing.');
            } else {
                $this->info('Subscription status: ' . ($body['status'] ?? 'unknown'));
            }
        } else {
            $this->line('Owned integration not operational — skip poll.');
        }

        return 0;
    }
}
