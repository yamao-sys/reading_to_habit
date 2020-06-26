<?php

namespace Tests\Feature;

use App\User;
use App\DefaultMailTiming;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;
use App\AutoLoginToken;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Response;

use Carbon\Carbon;

class AfterLoginMiddlewareTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * ログインアクションへリダイレクト(正常系1)
     * セッション変数: N
     * クッキー('auto_login'): N
     *
     * @return void
     */
    public function testRedirectToLogin1()
    {
        $response = $this->get('articles');

        $response->assertRedirect('https://localhost/login');
    }

    /**
     * ログインアクションへリダイレクト(異常系1)
     * セッション変数: N
     * クッキー('auto_login'): Y
     * DB上にトークン存在: N
     *
     * @return void
     */
    public function testRedirectToLogin2()
    {
        factory(User::class)->create();
        $user = User::first();
        factory(DefaultMailTiming::class)->create(['user_id' => $user['id']]);

        $default_mail_timing = DefaultMailTiming::first();
        factory(DefaultMailTimingMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);

        factory(AutoLoginToken::class)->create(['user_id' => $user['id']]);
        do {
            $invalid_token = str_random(255);
        } while(!empty(AutoLoginToken::where('token', $invalid_token)->first()));

        $response = $this->withoutExceptionHandling()
                         ->withCookie('auto_login', $invalid_token)
                         ->get('articles');

        $response->assertRedirect('https://localhost/login');
    }
    
    /**
     * ログインアクションへリダイレクト(正常系2)
     * セッション変数: N
     * クッキー('auto_login'): Y
     * DB上にトークン存在: Y(ただし削除済み)
     *
     * @return void
     */
    public function testRedirectToLogin3()
    {
        factory(User::class)->create();
        $user = User::first();
        factory(DefaultMailTiming::class)->create(['user_id' => $user['id']]);
        
        $default_mail_timing = DefaultMailTiming::first();
        factory(DefaultMailTimingMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        
        factory(AutoLoginToken::class)->create(['user_id' => $user['id']]);
        $token = AutoLoginToken::first();
        AutoLoginToken::where('token', $token['token'])->first()->update(['deleted' => 1]);

        $response = $this->withoutExceptionHandling()
                         ->withCookie('auto_login', $token['token'])
                         ->get('articles');
        
        $response->assertRedirect('https://localhost/login');
    }
    
    /**
     * ログインアクションへリダイレクト(正常系3：境界値)
     * セッション変数: N
     * クッキー('auto_login'): Y
     * DB上にトークン存在: Y
     * 有効期限: 外(1s)
     *
     * @return void
     */
    public function testRedirectToLogin4()
    {
        factory(User::class)->create();
        $user = User::first();
        factory(DefaultMailTiming::class)->create(['user_id' => $user['id']]);
        
        $default_mail_timing = DefaultMailTiming::first();
        factory(DefaultMailTimingMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        
        factory(AutoLoginToken::class)->create(['user_id' => $user['id'], 'expires' => Carbon::now()->subSecond(1)]);

        $response = $this->withoutExceptionHandling()
                         ->withCookie('auto_login', AutoLoginToken::first()['token'])
                         ->get('articles');
        
        $response->assertRedirect('https://localhost/login');
    }
    
    /**
     * ログインアクションへリダイレクト(正常系4：代表値)
     * セッション変数: N
     * クッキー('auto_login'): Y
     * DB上にトークン存在: Y
     * 有効期限: 外(3h)
     *
     * @return void
     */
    public function testRedirectToLogin5()
    {
        factory(User::class)->create();
        $user = User::first();
        factory(DefaultMailTiming::class)->create(['user_id' => $user['id']]);
        
        $default_mail_timing = DefaultMailTiming::first();
        factory(DefaultMailTimingMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        
        factory(AutoLoginToken::class)->create(['user_id' => $user['id'], 'expires' => Carbon::now()->subHours(3)]);

        $response = $this->withoutExceptionHandling()
                         ->withCookie('auto_login', AutoLoginToken::first()['token'])
                         ->get('articles');
        
        $response->assertRedirect('https://localhost/login');
    }
    
    /**
     * ログインアクションへリダイレクト(異常系2)
     * セッション変数: N
     * クッキー('auto_login'): Y
     * DB上にトークン存在: Y
     * 有効期限: 内(3h)
     * ユーザーの存在: N
     *
     * @return void
     */
    public function testRedirectToLogin6()
    {
        factory(User::class)->create();
        $user = User::first();
        factory(DefaultMailTiming::class)->create(['user_id' => $user['id']]);
        
        $default_mail_timing = DefaultMailTiming::first();
        factory(DefaultMailTimingMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        
        factory(AutoLoginToken::class)->create(['user_id' => $user['id'], 'expires' => Carbon::now()->addHours(3)]);
        User::first()->update(['deleted' => 1]);

        $response = $this->withoutExceptionHandling()
                         ->withCookie('auto_login', AutoLoginToken::first()['token'])
                         ->get('articles');
        
        $response->assertRedirect('https://localhost/login');
    }
    
    /**
     * 認証 + 新規自動ログイントークン発行(正常系5：境界値)
     * セッション変数: N
     * クッキー('auto_login'): Y
     * DB上にトークン存在: Y
     * 有効期限: 内(1s)
     * ユーザーの存在: Y
     *
     * @return void
     */
    public function testNext1()
    {
        factory(User::class)->create();
        $user = User::first();

        factory(AutoLoginToken::class)->create(['user_id' => $user['id'], 'expires' => Carbon::now()->addSecond(1)]);
        factory(DefaultMailTiming::class)->create(['user_id' => $user['id']]);
        
        $default_mail_timing = DefaultMailTiming::first();
        factory(DefaultMailTimingMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        
        $token = AutoLoginToken::first();

        $response = $this->withoutExceptionHandling()
                         ->withCookie('auto_login', $token['token'])
                         ->get('articles');
        
        $this->assertDatabaseHas('auto_login_tokens', [
            'token'   => $token['token'],
            'deleted' => 1,
        ]);
        $this->assertDatabaseHas('auto_login_tokens', [
            'expires' => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
        ]);

        $new_token = AutoLoginToken::where('expires', Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS))->first();
        
        $response->assertSessionHas(['user_id' => $user['id'], 'profile_img' => $user['profile_img']])
                 ->assertCookie('auto_login', $new_token['token'])
                 ->assertViewIs('article.articles');
    }
    
    /**
     * 認証 + 新規自動ログイントークン発行(正常系6：代表値)
     * セッション変数: N
     * クッキー('auto_login'): Y
     * DB上にトークン存在: Y
     * 有効期限: 内(3h)
     * ユーザーの存在: Y
     *
     * @return void
     */
    public function testNext2()
    {
        factory(User::class)->create();
        $user = User::first();
        factory(DefaultMailTiming::class)->create(['user_id' => $user['id']]);
        $default_mail_timing = DefaultMailTiming::first();
        factory(DefaultMailTimingMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);

        factory(AutoLoginToken::class)->create(['user_id' => $user['id'], 'expires' => Carbon::now()->addDays(3)]);
        $token = AutoLoginToken::first();

        $response = $this->withoutExceptionHandling()
                         ->withCookie('auto_login', $token['token'])
                         ->get('articles');
        
        $this->assertDatabaseHas('auto_login_tokens', [
            'token'   => $token['token'],
            'deleted' => 1,
        ]);
        $this->assertDatabaseHas('auto_login_tokens', [
            'expires' => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
        ]);

        $new_token = AutoLoginToken::where('expires', Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS))->first();
        
        $response->assertSessionHas(['user_id' => $user['id'], 'profile_img' => $user['profile_img']])
                 ->assertCookie('auto_login', $new_token['token'])
                 ->assertViewIs('article.articles');
    }
    
    /**
     * セッションIDの再生成(異常系3)
     * セッション変数: Y
     * ユーザーの存在: N
     *
     * @return void
     */
    public function testRedirectToTop()
    {
        factory(User::class)->create();
        $user = User::first();
        factory(DefaultMailTiming::class)->create(['user_id' => $user['id']]);
        
        $default_mail_timing = DefaultMailTiming::first();
        factory(DefaultMailTimingMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        User::first()->update(['deleted' => 1]);

        $response = $this->withoutExceptionHandling()
                         ->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img']])
                         ->get('articles');
        
        $response->assertRedirect('https://localhost/top')
                 ->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');
    }
    
    /**
     * セッションIDの再生成(正常系7：代表値)
     * セッション変数: Y
     * ユーザーの存在: Y
     *
     * @return void
     */
    public function testNext3()
    {
        factory(User::class)->create();
        $user = User::first();
        factory(DefaultMailTiming::class)->create(['user_id' => $user['id']]);
        
        $default_mail_timing = DefaultMailTiming::first();
        factory(DefaultMailTimingMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => $default_mail_timing['id']]);

        $response = $this->withoutExceptionHandling()
                         ->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img']])
                         ->get('articles');
        
        $response->assertViewIs('article.articles')
                 ->assertSessionHasAll(['user_id' => $user['id'], 'profile_img' => $user['profile_img']]);
    }
}
