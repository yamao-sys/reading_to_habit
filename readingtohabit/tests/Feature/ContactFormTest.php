<?php

namespace Tests\Feature;

use App\User;
use App\DefaultMailTiming;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Response;

use Carbon\Carbon;

class ContactFormTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * $BL$%m%0%$%s!"$+$D!"(Bcontact_info_email, contact_info_contact$B$J$7(B
     *
     * @return void
     */
    public function testContactForm1()
    {
        $response = $this->get('contact_form');

        $response->assertViewIs('contact.form');
    }
    
    /**
     * $BL$%m%0%$%s!"$+$D!"(Bcontact_info_email, contact_info_contact$B$"$j(B
     *
     * @return void
     */
    public function testContactForm2()
    {
        $email    = 'aaa@bbb.ccc';
        $contact  = '$B$*Ld$$9g$o$;FbMF(B\n$B$*Ld$$9g$o$;FbMF(B';

        $response = $this->withSession(['contact_info_email' => $email, 'contact_info_contact' => $contact])
                         ->get('contact_form');

        $response->assertViewIs('contact.form')
                 ->assertSeeText($contact);
    }
    
    /**
     * $B%m%0%$%sCf!"$+$D!"(Bcontact_info_email, contact_info_contact$B$J$7(B
     *
     * @return void
     */
    public function testContactForm3()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();

        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->get('contact_form');

        $response->assertViewIs('contact.form_after_login')
                 ->assertSessionMissing('contact_info_email')
                 ->assertSessionMissing('contact_info_contact');
    }
    
    /**
     * $B%m%0%$%sCf!"$+$D!"(Bcontact_info_email, contact_info_contact$B$"$j(B
     *
     * @return void
     */
    public function testContactForm4()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user    = User::first();
        $email   = 'aaa@bbb.ccc';
        $contact = '$B$*Ld$$9g$o$;FbMF(B\n$B$*Ld$$9g$o$;FbMF(B';

        $response = $this->withSession([
                            'user_id'      => $user['id'],
                            'profile_img'  => $user['profile_img'],
                            'current_date' => Carbon::now('Asia/Tokyo'),
                            'contact_info_email'   => $email,
                            'contact_info_contact' => $contact
                         ])
                         ->get('contact_form');

        $response->assertViewIs('contact.form_after_login')
                 ->assertSeeText($contact);
    }
}
