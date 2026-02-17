<?php

namespace App\Observers;

use App\Models\Member;

class QrCampaignObserver
{
    public function created(Member $member): void
    {
        if ($member->qr_campaign_id) {
            $member->qrCampaign()->increment('registrations_count');
        }
    }

    public function deleted(Member $member): void
    {
        if ($member->qr_campaign_id) {
            $member->qrCampaign()->decrement('registrations_count');
        }
    }

    public function restored(Member $member): void
    {
        if ($member->qr_campaign_id) {
            $member->qrCampaign()->increment('registrations_count');
        }
    }
}
