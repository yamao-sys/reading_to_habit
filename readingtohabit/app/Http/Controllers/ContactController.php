<?php

namespace App\Http\Controllers;

use App\User;
use App\DefaultMailTiming;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function contact_form (Request $request) {
        // お問い合わせ内容情報確認画面から情報修正リンクがクリックされた時のため
        $request->session()->regenerate();
        
        return view('contact.form');
    }

    public function contact_check (ContactRequest $request) {
        // 入力された情報をセッション変数に格納しておくことで、情報入力画面へ戻ったとき、情報を保持できる
        $request->session()->regenerate();
        $request->session()->put('contact_info_email',   $request->email);
        $request->session()->put('contact_info_contact', $request->contact);
        
        return view('contact.check');
    }
    
    public function contact_check_get (Request $request) {
        $request->session()->regenerate();
        
        // 情報入力画面を経ずに情報確認画面へアクセスされるのを防ぐため
        if (empty($request->session()->get('contact_info_email'))) {
            return view('common.invalid');
        }
        
        return view('contact.check');
    }
    
    public function contact_do (Request $request) {
        $request->session()->regenerate();
        if (empty($request->session()->get('contact_info_email'))) {
            return view('common.invalid');
        }
        
        DB::beginTransaction();

        try {
            $contact = Contact::create([
                        'email'   => $request->session()->get('contact_info_email'),
                        'contact' => $request->session()->get('contact_info_contact')
                       ]);

            Mail::to(\ContactMailConst::MAIL_ADD)->send(new ContactMail($contact['created_at'], $contact['email'], $contact['contact']));
        }
        catch (Exception $e) {
            DB::rollback();
            return view('common.invalid');
        }

        DB::commit();

        $request->session()->forget('contact_info_email');
        $request->session()->forget('contact_info_contact');
        return view('contact.finish');
    }
}
