<?php

namespace Spatie\EmailCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EmailCampaigns\Actions\ConfirmSubscriptionAction;
use Spatie\EmailCampaigns\Actions\SubscribeAction;
use Spatie\EmailCampaigns\Enums\EmailCampaignStatus;
use Spatie\EmailCampaigns\Enums\EmailListSubscriptionStatus;
use Spatie\EmailCampaigns\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Subscriber extends Model
{
    use HasUuid;

    public $table = 'email_list_subscribers';

    protected $guarded = [];

    public static function findForEmail(string $email): ?Subscriber
    {
        return static::where('email', $email)->first();
    }

    public function emailLists(): HasManyThrough
    {
        return $this->hasManyThrough(EmailList::class, EmailListSubscription::class);
    }

    public function subscribeTo(EmailList $emailList): EmailListSubscription
    {
       return app(SubscribeAction::class)->execute($this, $emailList);
    }

    public function isSubscribedTo(EmailList $emailList): bool
    {
        return EmailListSubscription::query()
            ->where('email_list_subscriber_id', $this->id)
            ->where('email_list_id', $emailList->id)
            ->where('status', EmailListSubscriptionStatus::SUBSCRIBED)
            ->exists();
    }
}