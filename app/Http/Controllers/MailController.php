<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use App\Models\MailList;
use Illuminate\Http\Request;

class MailController extends Controller
{

    public function add_mail_view()
    {
        $lists = MailList::all();
        return view('add-email',['lists'=>$lists]);
    }

    public function add_mail(Request $request)
    {
        $request->validate([
            'name' => "required|string|min:6|max:64",
            'email' => "email|unique:mails,mail|required",
            'phone' => "nullable",
            'list' => "nullable|exists:mail_lists,id",
        ]);
        $mail = new Mail();
        $mail->name = $request->name;
        $mail->mail = $request->email;
        $mail->phone = $request->phone;
        $mail->mail_list_id = $request->list;
        $mail->save();
        return redirect()->back()->with('success', 'Email has been successfully added to the list');
    }


    public function unsubscribe_view(){
        return view('unsubscribe');
    }

    public function unsubscribeauto_view(){
        return view('unsubscribeauto');
    }

    public function resubscribe_view(){
        return view('resubscribe');
    }

    public function unsubscribe(Request $request){
        $request->validate([
            'email'=>"email|exists:mails,mail",
        ]);
        $mail = Mail::where('mail',$request->email)->first();
        if($mail->unsubscribed == 1)
            return redirect()->back()->with('error', 'Email has already been marked as unsubscribed');

        $mail->unsubscribed = 1;
        $mail->save();
        return redirect()->back()->with('success', 'Email has been successfully marked as unsubscribed');
    }
    public function unsubscribe_()
    {
        return view('unsubscribe');
    }

    public function unsubscribe_email($email)
    {
        $mail = Mail::where('mail', $email)->first();
        if(!$mail){
            return redirect()->route('unsubscribe')->with('error', 'This email isn\'t registered');
        }
        if ($mail->unsubscribed == 1)
            return redirect()->route('unsubscribe')->with('error', 'Email has already been marked as unsubscribed');

        $mail->unsubscribed = 1;
        $mail->save();
        return redirect()->route('unsubscribe')->with('success', 'Email has been successfully marked as unsubscribed');
    }

    public function resubscribe_email($email)
    {
        $mail = Mail::where('mail', $email)->first();
        if(!$mail){
            return redirect()->route('resubscribe')->with('error', 'This email isn\'t registered');
        }
        if ($mail->unsubscribed == 1)
            return redirect()->route('resubscribe')->with('error', 'Email has already been marked as resubscribed');

        $mail->unsubscribed = 0;
        $mail->save();
        return redirect()->route('resubscribe')->with('success', 'Email has been successfully marked as resubscribed');
    }
}
