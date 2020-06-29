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
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

use Carbon\Carbon;

class ContactDoTest extends TestCase
{
    use DatabaseMigrations;
   
    /**
     * セッション変数(contact_info_email, contact_info_contact)なし
     *
     * @return void
     */
    public function testContactDo1()
    {
        $response = $this->get('contact_do');

        $response->assertRedirect('https://localhost/login');
    }
    
    /**
     * 未ログイン、かつ、セッション変数(contact_info_email, contact_info_contact)あり
     *
     * @return void
     */
    public function testContactDo2()
    {
        Mail::fake();

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $user     = User::first();
        $email    = 'aaa@bbb.ccc';
        $contact  = mb_convert_encoding($this->makeAlphaStringIncludingKana(1, 1000), 'UTF-8');
        $response = $this->withoutExceptionHandling()->withSession([
                                'contact_info_email'   => $email,
                                'contact_info_contact' => $contact
                          ])
                          ->get('contact_do');
        
        $this->assertDatabaseHas('contacts', [
            'email'   => $email,
            'contact' => $contact,
            'deleted' => 0
        ]);

        $contact_mailadd = \ContactMailConst::MAIL_ADD;
        Mail::assertSent(
                ContactMail::class,
                function ($mail) use ($contact_mailadd){
                    return $mail->to[0]['address'] === $contact_mailadd;
                });
            
        $response->assertViewIs('contact.finish')
                 ->assertSessionMissing('contact_info_email')
                 ->assertSessionMissing('contact_info_contact');
    }
    
    /**
     * ログイン中、かつ、セッション変数(contact_info_email, contact_info_contact)あり
     *
     * @return void
     */
    public function testContactDo3()
    {
        Mail::fake();

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user     = User::first();
        $email    = 'aaa@bbb.ccc';
        $contact  = mb_convert_encoding($this->makeAlphaStringIncludingKana(1, 1000), 'UTF-8');
        $response = $this->withoutExceptionHandling()->withSession([
                                'user_id'      => $user['id'],
                                'profile_img'  => $user['profile_img'],
                                'current_date' => Carbon::now('Asia/Tokyo'),
                                'contact_info_email'   => $email,
                                'contact_info_contact' => $contact
                          ])
                          ->get('contact_do');
        
        $this->assertDatabaseHas('contacts', [
            'email'   => $email,
            'contact' => $contact,
            'deleted' => 0
        ]);

        $contact_mailadd = \ContactMailConst::MAIL_ADD;
        Mail::assertSent(
                ContactMail::class,
                function ($mail) use ($contact_mailadd){
                    return $mail->to[0]['address'] === $contact_mailadd;
                });
            
        $response->assertViewIs('contact.finish_after_login')
                 ->assertSessionMissing('contact_info_email')
                 ->assertSessionMissing('contact_info_contact');
    }
}
