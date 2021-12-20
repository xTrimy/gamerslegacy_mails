<?php

use App\Http\Controllers\MailController;
use App\Mail\Egycon;
use App\Models\Mail;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Illuminate\Support\Facades\Route;
use Postmark\Models\PostmarkAttachment;
use Postmark\PostmarkClient;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mails', function () {
    $mails = Mail::all()->pluck('mail');
    dd($mails);
});

Route::get('/unsubscribe/{email}', [MailController::class, 'unsubscribe_email'])->name('unsubscribe');
Route::get('/resubscribe/{email}', [MailController::class, 'resubscribe_email'])->name('resubscribe');
// Route::post('/unsubscribe', [MailController::class, 'unsubscribe']);

Route::get('/add', [MailController::class, 'add_mail_view']);
Route::post('/add', [MailController::class, 'add_mail']);

// Route::get('/setup/campaigners', function () {
//     dd('x');
//     set_time_limit(8000000);
//     $client = new PostmarkClient(env("POSTMARK_SECRET_CAMP"));
//     $bounces = $client->getSuppressions()['suppressions'];
//     $emails = [];
//     foreach ($bounces as $result) {
//         $emails[] = $result['EmailAddress'];
//     }
//     $recipients = Mail::where([
//         ['unsubscribed', 0],
//         ['mail_list_id', 5],
//         ["is_miu_mail", 1],
//     ])->orderBy('id', 'ASC')->get();
//     // $recipients = Mail::where([['mail',"mohamed1812470@miuegypt.edu.eg"], ['mail_list_id', 5],])->orderBy('id', 'ASC')->get();
//     dd($recipients);
//     foreach ($recipients as $recipient) {
//         if (in_array($recipient->mail, array_values($emails))) {
//             continue;
//         }
//         echo $recipient->id . "<br>";
//         // $sendResult = $client->sendEmailWithTemplate(
//         //     "info@gamerslegacy.net",
//         //     $recipient->mail,
//         //     25505171,
//         //     [
//         //         "name" => $recipient->name,
//         //     ]
//         // );
//         $sendResult = $client->sendEmailWithTemplate(
//             "noreply@campaignersmiu.com",
//             $recipient->mail,
//             26338472,
//             [
//                 "name" => ucfirst(explode(' ', $recipient->name)[0]),
//             ],true,NULL,NULL,NULL,NULL,NULL,NULL,
//             // array(PostmarkAttachment::fromFile(public_path(). '/ticket.png', "ticket-sample.png", "image/png"))
//         );
//     }
// });

Route::get('/setup', function () {
    dd('x');
    set_time_limit(8000000);
    $client = new PostmarkClient(env("POSTMARK_SECRET"));
    $bounces = $client->getSuppressions()['suppressions'];
    $emails = [];
    foreach ($bounces as $result) {
        $emails[] = $result['EmailAddress'];
    }
    $recipients = Mail::where([
        ["id",">", "4363"],
        ['unsubscribed',0],
        // ['mail_list_id',1],
        ["is_miu_mail",1],
        ])->orderBy('id','ASC')->get()->unique(function ($e) {
        return strtolower($e->mail);
    });
    // $recipients = Mail::where([['mail',"mohamed1812470@miuegypt.edu.eg"]])->orderBy('id', 'ASC')->get()->unique(function ($e) {
    //     return strtolower($e->mail);
    // }); 
    // dd($recipients);
    foreach ($recipients as $recipient){
        if (in_array($recipient->mail,array_values($emails))){
            continue;
        }
        // if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
        //     continue;
        // }
        echo $recipient->id . "<br>";
        if($recipient->name == null){
            $name = "MIU Student";
        }else{
            $name = ucfirst(explode(' ', $recipient->name)[0]);
        }
            $sendResult = $client->sendEmailWithTemplate(
                "info@gamerslegacy.net",
                $recipient->mail,
                26387885,
                [
                    "name" => $name,
                    "email" => $recipient->mail,
                ]
            );
    }
})->middleware('auth.base');
