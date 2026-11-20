<?php
namespace App\Traits;

use Illuminate\Support\Facades\Mail;

trait EmailNotification{
    public function sendEmail($emailTo,$linkLable,$receiverName,$emailContent,$regardsName,$emailSubject,$emailLink){
        $email_data = [
            'fromName' => config('app.name'),
            'emailTo' => $emailTo,
            'name' => $receiverName,
            'emailContent' => $emailContent,
            'regardsName'=>$regardsName,
            'emailSubject' => $emailSubject,
            'emailLink'=>$emailLink,
            'linkLable'=>$linkLable,
        ];
        Mail::send('mails.NotificationEmail', $email_data, function ($message) use ($email_data) {
            $message
                ->to($email_data['emailTo'])
                ->priority(1)
                ->subject($email_data['emailSubject']);
        });
    }
}