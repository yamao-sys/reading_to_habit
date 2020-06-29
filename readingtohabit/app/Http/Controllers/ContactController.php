<?php

namespace App\Http\Controllers;

use App\Contact;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    public function contact_form (Request $request) {
        $request->session()->regenerate();
        
        if (empty($request->session()->get('user_id'))) {
            return view('contact.form');
        }
        else {
            return view('contact.form_after_login');
        }

    }

    public function contact_check (ContactRequest $request) {
        $request->session()->regenerate();
        $request->session()->put('contact_info_email',   $request->email);
        $request->session()->put('contact_info_contact', $request->contact);
        
        if (empty($request->session()->get('user_id'))) {
            return view('contact.check');
        }
        else {
            return view('contact.check_after_login');
        }
    }
    
    public function contact_check_get (Request $request) {
        $request->session()->regenerate();
        
        if (empty($request->session()->get('contact_info_email'))) {
            return view('common.invalid');
        }
        
        if (empty($request->session()->get('user_id'))) {
            return view('contact.check');
        }
        else {
            return view('contact.check_after_login');
        }
    }
    
    public function contact_do (Request $request) {
        $request->session()->regenerate();
        if (empty($request->session()->get('contact_info_email'))) {
            return redirect()->secure('login');
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
        if (empty($request->session()->get('user_id'))) {
            return view('contact.finish');
        }
        else {
            return view('contact.finish_after_login');
        }
    }
}
