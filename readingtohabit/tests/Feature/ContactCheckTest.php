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

class ContactCheckTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * $B%P%j%G!<%7%g%s%A%'%C%/(B
     * email: $B6u(B
     * contact: 3000$BJ8;z0JFb(B
     *
     * @return void
     */
    public function testContactCheck1()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user     = User::first();
        $email    = '';
        $contact  = $this->makeAlphaStringIncludingKana(1, 3000);
        $response = $this->post('contact_check', ['email' => $email, 'contact' => $contact]);

        $response->assertSessionHasErrors('email', '$B%a!<%k%"%I%l%9$OI,?\9`L\$G$9!#(B')
                 ->assertSessionDoesntHaveErrors('contact');
    }
    
    /**
     * $B%P%j%G!<%7%g%s%A%'%C%/(B
     * email: $B6u(B
     * contact: $B6u(B
     *
     * @return void
     */
    public function testContactCheck2()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user     = User::first();
        $email    = '';
        $contact  = '';
        $response = $this->post('contact_check', ['email' => $email, 'contact' => $contact]);

        $response->assertSessionHasErrors('email', '$B%a!<%k%"%I%l%9$OI,?\9`L\$G$9!#(B')
                 ->assertSessionHasErrors('contact', '$B$*Ld$$9g$o$;FbMF$OI,?\9`L\$G$9!#(B');
    }
    
    /**
     * $B%P%j%G!<%7%g%s%A%'%C%/(B
     * email: $B6u(B
     * contact: 3000$BJ8;zD6$((B
     *
     * @return void
     */
    public function testContactCheck3()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user     = User::first();
        $email    = '';
        $contact  = $this->makeAlphaStringIncludingKana(1, 3001);
        $response = $this->post('contact_check', ['email' => $email, 'contact' => $contact]);

        $response->assertSessionHasErrors('email', '$B%a!<%k%"%I%l%9$OI,?\9`L\$G$9!#(B')
                 ->assertSessionHasErrors('contact', '$B$*Ld$$9g$o$;FbMF$O(B3000$BJ8;z0JFb$G$4F~NO$/$@$5$$(B');
    }
    
    /**
     * $B%P%j%G!<%7%g%s%A%'%C%/(B
     * email: aaa@bbb.cc
     * contact: 3000$BJ8;z(B($B6-3&CM(B)
     *
     * @return void
     */
    public function testContactCheck4()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user      = User::first();
        $email     = 'aaa@bbb.ccc';
        $contact   = $this->makeAlphaStringIncludingKana(1, 3000);
        $response1 = $this->post('contact_check', ['email' => $email, 'contact' => $contact]);

        $response1->assertViewIs('contact.check')
                  ->assertSessionHasAll([
                        'contact_info_email' => $email,
                        'contact_info_contact' => $contact
                  ])
                  ->assertSessionDoesntHaveErrors('email')
                  ->assertSessionDoesntHaveErrors('contact');
        
        $response2 = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                          ->post('contact_check', ['email' => $email, 'contact' => $contact]);

        $response2->assertViewIs('contact.check_after_login')
                  ->assertSessionHasAll([
                        'contact_info_email'   => $email,
                        'contact_info_contact' => $contact
                  ])
                  ->assertSessionDoesntHaveErrors('email')
                  ->assertSessionDoesntHaveErrors('contact');
    }
    
    /**
     * $B%P%j%G!<%7%g%s%A%'%C%/(B
     * email: aaa@bbb.cc
     * contact: 1000$BJ8;z(B($BBeI=CM(B)
     *
     * @return void
     */
    public function testContactCheck5()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user      = User::first();
        $email     = 'aaa@bbb.ccc';
        $contact   = $this->makeAlphaStringIncludingKana(1, 1000);
        $response1 = $this->post('contact_check', ['email' => $email, 'contact' => $contact]);

        $response1->assertViewIs('contact.check')
                  ->assertSessionHasAll([
                        'contact_info_email'   => $email,
                        'contact_info_contact' => $contact
                  ])
                  ->assertSessionDoesntHaveErrors('email')
                  ->assertSessionDoesntHaveErrors('contact');
        
        $response2 = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                          ->post('contact_check', ['email' => $email, 'contact' => $contact]);

        $response2->assertViewIs('contact.check_after_login')
                  ->assertSessionHasAll([
                        'contact_info_email' => $email,
                        'contact_info_contact' => $contact
                  ])
                  ->assertSessionDoesntHaveErrors('email')
                  ->assertSessionDoesntHaveErrors('contact');
    }
    
    /**
     * get$B%a%=%C%I$K$h$k%"%/%;%9(B:$B%;%C%7%g%sJQ?t(B(contact_info_email, contact_info_contact)$B$J$7(B
     *
     * @return void
     */
    public function testContactCheck6()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user      = User::first();
        $response1 = $this->get('contact_check');

        $response1->assertViewIs('common.invalid');
        
        $response2 = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                          ->get('contact_check');

        $response2->assertViewIs('common.invalid');
    }
    
    /**
     * get$B%a%=%C%I$K$h$k%"%/%;%9(B:$B%;%C%7%g%sJQ?t(B(contact_info_email, contact_info_contact)$B$"$j(B
     * email: aaa@bbb.cc
     * contact: 1000$BJ8;z(B($BBeI=CM(B)
     *
     * @return void
     */
    public function testContactCheck7()
    {
        factory(DefaultMailTimingMaster::class)->create();        
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user      = User::first();
        $email     = 'aaa@bbb.ccc';
        $contact   = $this->makeAlphaStringIncludingKana(1, 1000);
        $response1 = $this->withSession([
                                'contact_info_email'   => $email,
                                'contact_info_contact' => $contact
                          ])
                          ->get('contact_check');

        $response1->assertViewIs('contact.check');
        
        $response2 = $this->withSession([
                                'user_id'      => $user['id'],
                                'profile_img'  => $user['profile_img'],
                                'current_date' => Carbon::now('Asia/Tokyo'),
                                'contact_info_email'   => $email,
                                'contact_info_contact' => $contact
                          ])
                          ->get('contact_check');

        $response2->assertViewIs('contact.check_after_login');
    }
}
