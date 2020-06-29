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
     * 未ログイン、かつ、contact_info_email, contact_info_contactなし
     *
     * @return void
     */
    public function testContactForm1()
    {
        $response = $this->get('contact_form');

        $response->assertViewIs('contact.form');
    }
    
    /**
     * 未ログイン、かつ、contact_info_email, contact_info_contactあり
     *
     * @return void
     */
    public function testContactForm2()
    {
        $email    = 'aaa@bbb.ccc';
        $contact  = 'お問い合わせ内容\nお問い合わせ内容';

        $response = $this->withSession(['contact_info_email' => $email, 'contact_info_contact' => $contact])
                         ->get('contact_form');

        $response->assertViewIs('contact.form')
                 ->assertSeeText($contact);
    }
    
    /**
     * ログイン中、かつ、contact_info_email, contact_info_contactなし
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
     * ログイン中、かつ、contact_info_email, contact_info_contactあり
     *
     * @return void
     */
    public function testContactForm4()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user    = User::first();
        $email   = 'aaa@bbb.ccc';
        $contact = 'お問い合わせ内容\nお問い合わせ内容';

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
