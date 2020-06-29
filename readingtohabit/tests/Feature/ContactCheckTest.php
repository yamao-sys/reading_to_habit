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
     * バリデーションチェック
     * email: 空
     * contact: 3000文字以内
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

        $response->assertSessionHasErrors('email', 'メールアドレスは必須項目です。')
                 ->assertSessionDoesntHaveErrors('contact');
    }
    
    /**
     * バリデーションチェック
     * email: 空
     * contact: 空
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

        $response->assertSessionHasErrors('email', 'メールアドレスは必須項目です。')
                 ->assertSessionHasErrors('contact', 'お問い合わせ内容は必須項目です。');
    }
    
    /**
     * バリデーションチェック
     * email: 空
     * contact: 3000文字超え
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

        $response->assertSessionHasErrors('email', 'メールアドレスは必須項目です。')
                 ->assertSessionHasErrors('contact', 'お問い合わせ内容は3000文字以内でご入力ください');
    }
    
    /**
     * バリデーションチェック
     * email: aaa@bbb.cc
     * contact: 3000文字(境界値)
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
     * バリデーションチェック
     * email: aaa@bbb.cc
     * contact: 1000文字(代表値)
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
     * getメソッドによるアクセス:セッション変数(contact_info_email, contact_info_contact)なし
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
     * getメソッドによるアクセス:セッション変数(contact_info_email, contact_info_contact)あり
     * email: aaa@bbb.cc
     * contact: 1000文字(代表値)
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
