<?php

namespace App\Jobs;


use App\Models\Audience;
use App\Models\Campaigns;
use App\Models\Messages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaign;

    public function __construct(Campaigns $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle()
    {
        $audiences = Audience::where('segment', $this->campaign->segment)->get();
        
        $frequencyCap = 2;

        $budgetPerMessage = 0.05; // example cost per message
        $maxMessages = floor($this->campaign->budget / $budgetPerMessage);
        $sentMessages = 0;

        foreach ($audiences as $audience) {

       

        $sentCount = Messages::where('audience_id', $audience->id)
                             ->where('status', 'sent')
                             ->count();

            if ($sentCount >= $frequencyCap) {
                \Log::info("Skipping audience {$audience->id} due to frequency cap");
                continue; // skip this audience
            }

            // Create a message record
            $message = Messages::create([
                'audience_id' => $audience->id,
                'campaign_id' => $this->campaign->id,
                'status' => 'pending',
            ]);

            // example: simulate sending
            \Log::info("Sending campaign {$this->campaign->id} to audience {$audience->id}");

            // Update status to "sent" (actual DLR would update later)
            $message->status = 'sent';
            $message->save();

            $sentMessages++;
            if ($sentMessages >= $maxMessages) {
                \Log::info("Budget exhausted for campaign {$this->campaign->id}");
                break; // stop if budget is exhausted   
            }
        }

        // Mark campaign completed once all messages are processed
        $this->campaign->status = 'completed';
        $this->campaign->save();
         

        //last touch update
        $audience->last_campaign_id = $this->campaign->id;
        $audience->last_touch_at = now();
        $audience->save();

    }
}
