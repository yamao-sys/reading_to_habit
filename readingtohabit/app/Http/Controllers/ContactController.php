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
        // $B$*Ld$$9g$o$;FbMF>pJs3NG'2hLL$+$i>pJs=$@5%j%s%/$,%/%j%C%/$5$l$?;~$N$?$a(B
        $request->session()->regenerate();
        
        return view('contact.form');
    }

    public function contact_check (ContactRequest $request) {
        // $BF~NO$5$l$?>pJs$r%;%C%7%g%sJQ?t$K3JG<$7$F$*$/$3$H$G!">pJsF~NO2hLL$XLa$C$?$H$-!">pJs$rJ];}$G$-$k(B
        $request->session()->regenerate();
        $request->session()->put('contact_info_email',   $request->email);
        $request->session()->put('contact_info_contact', $request->contact);
        
        return view('contact.check');
    }
    
    public function contact_check_get (Request $request) {
        $request->session()->regenerate();
        
        // $B>pJsF~NO2hLL$r7P$:$K>pJs3NG'2hLL$X%"%/%;%9$5$l$k$N$rKI$0$?$a(B
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
