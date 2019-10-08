---
title: Sending a campaign
weight: 3
---

Before sending a campaign, ensure that the `subject`, `html` and `email_list_id` attributes are set.

A campaign can be sent with the `send` method.

```php
$campaign->send();
```

Alternatively you can set the email list and send the campaign in one go:

```php
$campaign->sendTo($emailList);
```

## What happens when a campaign is being sent

When you send a campaign a job called `SendCampaign` job will be dispatched. This job will create a `MailSend` model for each of the subscribers of the list your're sending the campaign to. A `MailSend` represent a mail that should be send to one subscriber. 

For each created `SendMail` model, a `SendMailJob` will be started. That job will send that actual mail. After the job has sent the mail, it will mark the `SendMail` as sent, by filling `sent_at` with the current timestamp. 
 
 You can customize on which queue the `SendCampaignJob` and `SendMailJob` jobs are dispatch  is dispatch in the `perform_on_queue` in the `email-campaigns` config file. We recommend the `SendMailJob` having its own queue because it could contain many pending jobs if you are send a campaign to a large list of subscribers.
 
 To not overwhelm your email service with a large amount of calls, the `SendMailJob` is also throttled by default. Only 5 of those jobs will be handle in the timespan of a second. To learn more about this, read [the docs on throttling sends](TODO: addlink).
