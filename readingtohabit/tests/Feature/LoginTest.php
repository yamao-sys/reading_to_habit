<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use App\User;
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
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id' => $user_id,
            'token' => $token,
            'expires' => Carbon::now()->addSeconds(1),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新しいトークンがDBに作成されている
        $new_expires_date = Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS);
        $new_token_record = AutoLoginToken::where('expires', $new_expires_date)->first();

        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => $new_expires_date,
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいる
        $response->assertSessionHasAll([
            'user_id'    => $user_id,
            'profile_img'  => User::where('id', $user_id)->first()['profile_img'],
        ]);

        // 新しいトークンのクッキー付きでarticlesへリダイレクトされている
        $response->assertRedirect('articles')
                 ->assertCookie('auto_login', $new_token_record['token'])
                 ->assertCookieNotExpired('auto_login');
    }

    public function testLoginGetMethod2()
    {
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create(['user_id' => $user_id, 'token' => $token]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新しいトークンがDBに作成されている
        $new_expires_date = Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS);
        $new_token_record = AutoLoginToken::where('expires', $new_expires_date)->first();

        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'token'      => $new_token_record['token'],
            'expires'    => $new_expires_date,
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいる
        $response->assertSessionHasAll([
            'user_id'    => $user_id,
            'profile_img'  => User::where('id', $user_id)->first()['profile_img'],
        ]);

        // 新しいトークンのクッキー付きでarticlesへリダイレクトされている
        $response->assertRedirect('articles')
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
        $user_id = mt_rand(1, 20);
        factory(User::class)->create([
            'id'         => $user_id,
            'deleted'    => 1,
        ]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create(['user_id' => $user_id, 'token' => $token]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新規トークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id' => $user_id,
            'expires' => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS)
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
        $user_id = mt_rand(1, 20);
        factory(User::class)->create([
            'id'         => $user_id,
            'deleted'    => 1,
        ]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id'    => $user_id,
            'token'      => $token,
            'deleted'    => 1,
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 新規トークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
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
     * Y                         Y                                 外        0                     Y               ログインフォーム
     * @return void
     */
    public function testLoginGetMethod5()
    {
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id' => $user_id,
            'token' => $token,
            'expires' => Carbon::now(),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');
        
        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
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
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id' => $user_id,
            'token'   => $token,
            'expires' => Carbon::now()->subSeconds(1),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
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
    
    public function testLoginGetMethod7()
    {
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id' => $user_id,
            'token'   => $token,
            'expires' => Carbon::now()->subHours(3),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
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
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id, 'deleted' => 1]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id' => $user_id,
            'token'   => $token,
            'expires' => Carbon::now()->subHours(3),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 現在のトークンが論理削除されている
        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'token'      => $token,
            'deleted'    => 1,
        ]);

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
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
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id' => $user_id,
            'token'   => $token,
            'expires' => Carbon::now()->subHours(3),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
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
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id, 'deleted' => 1]);

        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id' => $user_id,
            'token'   => $token,
            'expires' => Carbon::now()->subHours(3),
        ]);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $token)->get('login');

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
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
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id]);
        
        $token = str_random(255);
        factory(AutoLoginToken::class)->create([
            'user_id' => $user_id,
            'token'   => $token,
            'expires' => Carbon::now()->addHours(3),
        ]);

        do {
            $send_token = str_random(255);
        } while($send_token === $token);

        $response = $this->withoutExceptionHandling()->withCookie('auto_login', $send_token)->get('login');

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
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
        $user_id = mt_rand(1, 20);
        factory(User::class)->create(['id' => $user_id]);
        
        $response = $this->withoutExceptionHandling()->get('login');

        // 新規のトークンが作成されていない
        $this->assertDatabaseMissing('auto_login_tokens', [
            'user_id'    => $user_id,
            'expires'    => Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS),
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
     * └　入力値に相当するユーザーがいない：data6～data8
     *
     * エラーなし、かつ「自動ログインを許可する」のチェックあり：data4
     *
     * エラーなし、かつ「自動ログインを許可する」のチェックなし：data5
     * @return void
     */
    public function testLoginPostMethod1()
    {
        $user_id  = mt_rand(1, 20);
        $email    = 'AAA@BBB.CCC';
        $password = str_random(rand(6, 12));
        factory(User::class)->create([
            'id'       => $user_id,
            'email'    => $email,
            'password' => Hash::make($password),
        ]);

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

        $response4 = $this->withoutExceptionHandling()->post('login', $data4);
        // 新しいトークンがDBに作成されている
        $new_expires_date = Carbon::now()->addDays(\AutoLoginTokenConst::EXPIRES_DAYS);
        $new_token_record = AutoLoginToken::where('expires', $new_expires_date)->first();

        $this->assertDatabaseHas('auto_login_tokens', [
            'user_id'    => $user_id,
            'token'      => $new_token_record['token'],
            'expires'    => $new_expires_date,
            'deleted'    => 0,
            'deleted_at' => null,
        ]);

        // レスポンスにセッション変数を含んでいる
        $response4->assertSessionHasAll([
            'user_id'      => $user_id,
            'profile_img'  => User::where('id', $user_id)->first()['profile_img'],
        ]);

        // 新しいトークンのクッキー付きでarticlesへリダイレクトされている
        $response4->assertRedirect('articles')
                  ->assertCookie('auto_login', $new_token_record['token'])
                  ->assertCookieNotExpired('auto_login');

        $response5 = $this->withoutExceptionHandling()->post('login', $data5);
        
        // レスポンスにセッション変数を含んでいる
        $response5->assertSessionHasAll([
            'user_id'      => User::where('email', $data5['email'])->first()['id'],
            'profile_img'  => User::where('email', $data5['email'])->first()['profile_img'],
        ]);

        // 新しいトークンのクッキーなしでarticlesへリダイレクトされている
        $response5->assertRedirect('articles')
                  ->assertCookieMissing('auto_login');
        
        /*
        // エラーあり、元ページへリダイレクトされている
        $response6 = $this->post('login', $data6);

        $response6->assertSessionHasErrors(['is_not_exist' => '存在しないユーザーです。'])
                  ->assertCookieMissing('auto_login');

        // エラーあり、元ページへリダイレクトされている
        $response7 = $this->post('login', $data7);
        
        $response7->assertSessionHasErrors(['is_not_exist' => '存在しないユーザーです。'])
                  ->assertCookieMissing('auto_login');

        // エラーあり、元ページへリダイレクトされている
        $response8 = $this->post('login', $data8);

        $response8->assertSessionHasErrors(['is_not_exist' => '存在しないユーザーです。'])
                  ->assertCookieMissing('auto_login');
        */
    }
}
