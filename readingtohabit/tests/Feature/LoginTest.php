<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use App\User;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;
use App\AutoLoginToken;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Response;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LoginTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * getメソッドによるloginへのアクセステスト
     * トークン(クッキー)の存在　トークンに相当するレコードの存在　有効期限　トークンの削除フラグ　ユーザーの存在　出力
     * Y                         Y                                 内        0                     Y               自動ログイン
     * @return void
     */
    public function testLoginGetMethod1()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $token = str_random(255);
        $user = User::first();
        factory(AutoLoginToken::class)->create([
            'user_id' => $user['id'],
            'token'   => $token,
            'expires' => Carbon::now('Asia/Tokyo')->addSeconds(1),
        ]);

        $response = $this->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user['id'],
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新しいトークンがDBに作成されている
        $new_expires_date = Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS);
        $new_token_record = AutoLoginToken::where('expires', $new_expires_date)->first();
        $new_token_record = AutoLoginToken::where('deleted', 0)->first();
        
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user['id'],
            'expires'    => $new_expires_date,
            'deleted'    => 0,
            'deleted_at' => null,
        ]);
        
        // レスポンスにセッション変数を含んでいる
        $response->assertSessionHasAll([
            'user_id'      => $user['id'],
            'profile_img'  => $user['profile_img'],
        ]);

        // 新しいトークンのクッキー付きでarticlesへリダイレクトされている
        $response->assertRedirect('https://localhost/articles')
                 ->assertCookie('auto_login', $new_token_record['token'])
                 ->assertCookieNotExpired('auto_login');
    }

    public function testLoginGetMethod2()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $token = str_random(255);
        $user = User::first();
        factory(AutoLoginToken::class)->create([
            'user_id' => $user['id'],
            'token'   => $token,
            'expires' => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user['id'],
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新しいトークンがDBに作成されている
        $new_expires_date = Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS);
        $new_token_record = AutoLoginToken::where('expires', $new_expires_date)->first();

        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user['id'],
            'token'      => $new_token_record['token'],
            'expires'    => $new_expires_date,
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいる
        $response->assertSessionHasAll([
            'user_id'      => $user['id'],
            'profile_img'  => $user['profile_img'],
        ]);

        // 新しいトークンのクッキー付きでarticlesへリダイレクトされている
        $response->assertRedirect('https://localhost/articles')
                 ->assertCookie('auto_login', $new_token_record['token'])
                 ->assertCookieNotExpired('auto_login');
    }
    
    /**
     * getメソッドによるloginへのアクセステスト
     * トークン(クッキー)の存在　トークンに相当するレコードの存在　有効期限　トークンの削除フラグ　ユーザーの存在　出力
     * Y                         Y                                 内        0                     N               ログインフォーム
     * @return void
     */
    public function testLoginGetMethod3()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $token = str_random(255);
        $user = User::first();
        User::first()->update(['deleted' => 1]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create(['user_id' => $user['id'], 'token' => $token]);

        $response = $this->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user['id'],
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新規トークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id' => $user['id'],
            'expires' => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
            'deleted' => 0,
        ]);

        // レスポンスにセッション変数を含んでいない
        $response->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');

        // クッキーなしでauth.viewが表示されている
        $response->assertViewIs('auth.login')
                 ->assertCookieMissing('auto_login');
    }

    /**
     * getメソッドによるloginへのアクセステスト
     * トークン(クッキー)の存在　トークンに相当するレコードの存在　有効期限　トークンの削除フラグ　ユーザーの存在　出力
     * Y                         Y                                 内        1                     N               ログインフォーム
     * @return void
     */
    public function testLoginGetMethod4()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $token = str_random(255);
        $user = User::first();
        User::first()->update(['deleted' => 1]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id'    => $user['id'],
            'token'      => $token,
            'deleted'    => 1,
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 新規トークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user['id'],
            'expires'    => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
            'deleted'    => 0,
        ]);

        // レスポンスにセッション変数を含んでいない
        $response->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');
        
        // クッキーなしでauth.loginが表示されている
        $response->assertViewIs('auth.login')
                 ->assertCookieMissing('auto_login');
    }
    
    /**
     * getメソッドによるloginへのアクセステスト
     * トークン(クッキー)の存在　トークンに相当するレコードの存在　有効期限　トークンの削除フラグ　ユーザーの存在　出力
     * Y                         Y                                 外        0                     Y               ログインフォーム
     * @return void
     */
    public function testLoginGetMethod5()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $token = str_random(255);
        $user = User::first();
        factory(AutoLoginToken::class)->create([
            'user_id' => $user['id'],
            'token' => $token,
            'expires' => Carbon::now('Asia/Tokyo')->subSeconds(1),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');
        
        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user['id'],
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user['id'],
            'expires'    => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいない
        $response->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');

        // クッキーなしでauth.loginが表示されている
        $response->assertViewIs('auth.login')
                 ->assertCookieMissing('auto_login');
    }
    
    public function testLoginGetMethod6()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $token = str_random(255);
        $user = User::first();
        factory(AutoLoginToken::class)->create([
            'user_id' => $user['id'],
            'token' => $token,
            'expires' => Carbon::now('Asia/Tokyo')->subHours(3),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');
        
        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user['id'],
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user['id'],
            'expires'    => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいない
        $response->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');

        // クッキーなしでauth.loginが表示されている
        $response->assertViewIs('auth.login')
                 ->assertCookieMissing('auto_login');
    }
    
    /**
     * getメソッドによるloginへのアクセステスト
     * トークン(クッキー)の存在　トークンに相当するレコードの存在　有効期限　トークンの削除フラグ　ユーザーの存在　出力
     * Y                         Y                                 外        0                     N               ログインフォーム
     * @return void
     */
    public function testLoginGetMethod8()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $token = str_random(255);
        $user = User::first();
        User::first()->update(['deleted' => 1]);

        factory(AutoLoginToken::class)->create([
            'user_id' => $user['id'],
            'token'   => $token,
            'expires' => Carbon::now('Asia/Tokyo')->subHours(3),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user['id'],
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user['id'],
            'expires'    => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいない
        $response->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');

        // クッキーなしでauth.loginが表示されている
        $response->assertViewIs('auth.login')
                 ->assertCookieMissing('auto_login');
    }
    
    /**
     * getメソッドによるloginへのアクセステスト
     * トークン(クッキー)の存在　トークンに相当するレコードの存在　有効期限　トークンの削除フラグ　ユーザーの存在　出力
     * Y                         Y                                 外        1                     Y               ログインフォーム
     * @return void
     */
    public function testLoginGetMethod9()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $token = str_random(255);
        $user = User::first();

        factory(AutoLoginToken::class)->create([
            'user_id' => $user['id'],
            'token'   => $token,
            'expires' => Carbon::now('Asia/Tokyo')->subHours(3),
            'deleted' => 1,
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user['id'],
            'expires'    => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいない
        $response->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');

        // クッキーなしでauth.loginが表示されている
        $response->assertViewIs('auth.login')
                 ->assertCookieMissing('auto_login');
    }
    
    /**
     * getメソッドによるloginへのアクセステスト
     * トークン(クッキー)の存在　トークンに相当するレコードの存在　有効期限　トークンの削除フラグ　ユーザーの存在　出力
     * Y                         Y                                 外        1                     N               ログインフォーム
     * @return void
     */
    public function testLoginGetMethod10()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $token = str_random(255);
        $user = User::first();
        User::first()->update(['deleted' => 1]);

        factory(AutoLoginToken::class)->create([
            'user_id' => $user['id'],
            'token'   => $token,
            'expires' => Carbon::now('Asia/Tokyo')->subHours(3),
            'deleted' => 1,
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user['id'],
            'expires'    => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいない
        $response->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');

        // クッキーなしでauth.loginが表示されている
        $response->assertViewIs('auth.login')
                 ->assertCookieMissing('auto_login');
    }
    
    /**
     * getメソッドによるloginへのアクセステスト
     * トークン(クッキー)の存在　トークンに相当するレコードの存在　有効期限　トークンの削除フラグ　ユーザーの存在　出力
     * Y                         N                                 -         -                     -               ログインフォーム
     * @return void
     */
    public function testLoginGetMethod11()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $token = str_random(255);
        $user = User::first();
        factory(AutoLoginToken::class)->create([
            'user_id' => $user['id'],
            'token'   => $token,
            'expires' => Carbon::now('Asia/Tokyo')->addHours(3),
        ]);

        do {
            $send_token = str_random(255);
        } while($send_token === $token);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $send_token)->get('login');

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user['id'],
            'expires'    => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいない
        $response->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');

        // クッキーなしでauth.loginが表示されている
        $response->assertViewIs('auth.login')
                 ->assertCookieMissing('auto_login');
    }
    
    /**
     * getメソッドによるloginへのアクセステスト
     * トークン(クッキー)の存在　トークンに相当するレコードの存在　有効期限　トークンの削除フラグ　ユーザーの存在　出力
     * N                         -                                 -         -                     -               ログインフォーム
     * @return void
     */
    public function testLoginGetMethod12()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $user = User::first();
        
        $response = $this->withoutExceptionHandling()->get('login');

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user['id'],
            'expires'    => Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいない
        $response->assertSessionMissing('user_id')
                 ->assertSessionMissing('profile_img');

        // クッキーなしでauth.loginが表示されている
        $response->assertViewIs('auth.login')
                 ->assertCookieMissing('auto_login');
    }
    
    /**
     * postメソッドによるloginへのアクセステスト
     * エラーチェック
     * └　未入力：data1～data3
     * └入力値に相当するユーザーが存在しない:data6～8
     *
     * エラーなし、かつ「自動ログインを許可する」のチェックあり：data4
     *
     * エラーなし、かつ「自動ログインを許可する」のチェックなし：data5
     * @return void
     */
    public function testLoginPostMethod1()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $email    = 'AAA@BBB.CCC';
        $password = str_random(rand(6, 12));
        User::first()->update(['email' => $email, 'password' => Hash::make($password)]);
        $user = User::first();

        $data1 = ['email' => '', 'password' => ''];
        $error_msg1 = ['email' => 'メールアドレスは必須項目です。', 'password' => 'パスワードは必須項目です。'];
        $data2 = ['email' => 'xxx@yyy.zzz', 'password' => ''];
        $error_msg2 = ['email' => '', 'password' => 'パスワードは必須項目です。'];
        $data3 = ['email' => '', 'password' => str_random(rand(6, 12))];
        $error_msg3 = ['email' => 'メールアドレスは必須項目です。', 'password' => ''];

        $request = new LoginRequest();
        $rules = $request->rules();
        $messages = $request->messages();
        $validator1 = Validator::make($data1, $rules, $messages);
        $validator2 = Validator::make($data2, $rules, $messages);
        $validator3 = Validator::make($data3, $rules, $messages);

        $errors1 = $validator1->errors();
        $errors2 = $validator2->errors();
        $errors3 = $validator3->errors();
        
        $this->assertEquals($error_msg1['email'], $errors1->first('email'));
        $this->assertEquals($error_msg1['password'], $errors1->first('password'));
        
        $this->assertEquals($error_msg2['email'], $errors2->first('email'));
        $this->assertEquals($error_msg2['password'], $errors2->first('password'));
        
        $this->assertEquals($error_msg3['email'], $errors3->first('email'));
        $this->assertEquals($error_msg3['password'], $errors3->first('password'));

        $data4 = ['email' => $email, 'password' => $password, 'auto_login' => "1"];
        $data5 = ['email' => $email, 'password' => $password];
        do {
            $different_password = str_random(rand(6, 12));
        } while($different_password === $password);
        $data6 = ['email' => $email, 'password' => $different_password];
        $data7 = ['email' => 'aaa@bbb.ccc', 'password' => $password];
        $data8 = ['email' => 'xxx@yyy.zzz', 'password' => $password];
        $response6 = $this->post('login', $data6);
        $response6->assertSessionHasErrors('is_not_exist');
        
        $response7 = $this->post('login', $data7);
        $response7->assertSessionHasErrors('is_not_exist');
        
        $response8 = $this->post('login', $data8);
        $response8->assertSessionHasErrors('is_not_exist');

        $response4 = $this->withoutExceptionHandling()->post('login', $data4);
        // 新しいトークンがDBに作成されている
        $new_expires_date = Carbon::now('Asia/Tokyo')->addDays(\AutoLoginTokenConst::EXPIRES_DAYS);
        $new_token_record = AutoLoginToken::where('expires', $new_expires_date)->first();

        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user['id'],
            'token'      => $new_token_record['token'],
            'expires'    => $new_expires_date,
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいる
        $response4->assertSessionHasAll([
            'user_id'      => $user['id'],
            'profile_img'  => $user['profile_img'],
        ]);

        // 新しいトークンのクッキー付きでarticlesへリダイレクトされている
        $response4->assertRedirect('https://localhost/articles')
                  ->assertCookie('auto_login', $new_token_record['token'])
                  ->assertCookieNotExpired('auto_login');

        $response5 = $this->withoutExceptionHandling()->post('login', $data5);
        
        // レスポンスにセッション変数を含んでいる
        $response5->assertSessionHasAll([
            'user_id'      => User::where('email', $data5['email'])->first()['id'],
            'profile_img'  => User::where('email', $data5['email'])->first()['profile_img'],
        ]);

        // 新しいトークンのクッキーなしでarticlesへリダイレクトされている
        $response5->assertRedirect('https://localhost/articles')
                  ->assertCookieMissing('auto_login');   
    }
}
