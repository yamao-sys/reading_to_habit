<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use App\User;
use App\ResetPasswordToken;

use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordFinish;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ResetPasswordDoTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * パスワードリセット実行のバリデーションエラーテスト(無効同値クラスA)
     *
     * @dataProvider dataproviderValidationError
     *
     * @param array 項目名 => 値
     * @param array 項目名 => 値
     * @return void
     */
    public function testResetPasswordValidationError($data, $error_msg)
    {
        $token_before_expires_3h = str_random(50);

        factory(User::class)->create(['id' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_before_expires_3h,
            'expires' => Carbon::now()->addHours(3),
        ]);

        $response = $this->post('reset_password_do?key='.$token_before_expires_3h, ['password' => $data['password'], 'password_to_check' => $data['password_to_check']]);

        if (empty($error_msg['password']) && empty($error_msg['password_to_check'])) {
            $response->assertRedirect('')
                     ->assertSessionHasErrors([
                            'password'          => $error_msg['password'],
                            'password_to_check' => $error_msg['password_to_check']
                       ]);
        }

        if(empty($error_msg['password']) && !empty($error_msg['password_to_check'])) {
            $response->assertRedirect('')
                     ->assertSessionHasErrors([
                            'password_to_check' => $error_msg['password_to_check']
                       ]);
        }

        if (!empty($error_msg['password']) && empty($error_msg['password_to_check'])) {
            $response->assertRedirect('')
                     ->assertSessionHasErrors([
                            'password' => $error_msg['password']
                       ]);
        }
        
        if (!empty($error_msg['password']) && !empty($error_msg['password_to_check'])) {
            $response->assertRedirect('')
                     ->assertSessionHasErrors([
                            'password'          => $error_msg['password'],
                            'password_to_check' => $error_msg['password_to_check']
                       ]);
        }
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラスB)
     * クエリパラメータなし、変数名keyでないクエリパラメータあり
     *
     * @dataProvider dataproviderValidData
     *
     * @param array 項目名 => 値
     *
     * @return void
     */
    public function testURLParamIsNotKey($data)
    {
        $response1 = $this->withoutExceptionHandling()->post('reset_password_do', $data);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');

        // key以外のクエリパラメータ
        $token = str_random(50);
        do {
            $token_different = str_random(50);
        } while($token_different == $token);
        
        factory(User::class)->create(['id' => 1]);
        factory(ResetPasswordToken::class)->create(['user_id' => 1, 'token' => $token]);

        $response2 = $this->withoutExceptionHandling()->post('reset_password_do?test='.$token_different, $data);

        $response2->assertStatus(200)
                  ->assertViewIs('common.invalid');

        $response3 = $this->withoutExceptionHandling()->post('reset_password_do?test='.$token, $data);
        
        $response3->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(有効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=0, 有効期限内
     * ユーザー存在する
     *
     * @dataProvider dataproviderValidData
     *
     * @param array 項目名 => 値
     *
     * @return void
     */
    public function testURLParamIsKey1($data)
    {
        Mail::fake();
        
        $token_expires_now = str_random(50);
        
        do {
            $token_before_expires_1s = str_random(50);
        } while($token_before_expires_1s == $token_expires_now);

        do {
            $token_before_expires_3h = str_random(50);
        } while($token_before_expires_3h == $token_before_expires_1s || $token_before_expires_3h == $token_expires_now);

        factory(User::class)->create(['id' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_expires_now,
            'expires' => Carbon::now(),
        ]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_before_expires_1s,
            'expires' => Carbon::now()->addSeconds(1),
        ]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_before_expires_3h,
            'expires' => Carbon::now()->addHours(3),
        ]);
        
        // 有効期限の境界値
        $response1 = $this->withoutExceptionHandling()
                          ->post('reset_password_do?key='.$token_expires_now, $data);

        /*
        $this->assertDatabaseHas('users', [
            'id' => 1,
            'password' => Hash::make($data['password']),
        ]);
        */

        $modified_password = User::where('id', 1)->first()['password'];
        // $this->assertSame(password_verify($data['password'], $modified_password), true);
        $this->assertSame(Hash::check($data['password'], $modified_password), true);

        $this->assertDatabaseHas('reset_password_tokens', [
            'token' => $token_expires_now,
            'deleted' => 1,
        ]);
        
        $email = User::where('id', 1)->first()['email'];
        Mail::assertSent(
                ResetPasswordFinish::class,
                function ($mail) use ($email){
                    Return $mail->hasTo($email);
                });

        $response1->assertViewIs('reset_password.finish');

        // 有効期限の境界値
        $response2 = $this->withoutExceptionHandling()
                          ->post('reset_password_do?key='.$token_before_expires_1s, $data);
        
        /*
        $this->assertDatabaseHas('users', [
            'id' => 1,
            'password' => Hash::make($data['password']),
        ]);
        */
        
        $modified_password = User::where('id', 1)->first()['password'];
        $this->assertSame(password_verify($data['password'], $modified_password), true);

        $this->assertDatabaseHas('reset_password_tokens', [
            'token' => $token_before_expires_1s,
            'deleted' => 1,
        ]);
        
        $email = User::where('id', 1)->first()['email'];
        Mail::assertSent(
                ResetPasswordFinish::class,
                function ($mail) use ($email){
                    return $mail->hasTo($email);
                });

        $response2->assertViewIs('reset_password.finish');

        // 有効期限の代表値
        $response3 = $this->withoutExceptionHandling()
                          ->post('reset_password_do?key='.$token_before_expires_3h, $data);
        
        /*
        $this->assertDatabaseHas('users', [
            'id' => 1,
            'password' => Hash::make($data['password']),
        ]);
        */
        
        $modified_password = User::where('id', 1)->first()['password'];
        $this->assertSame(password_verify($data['password'], $modified_password), true);

        $this->assertDatabaseHas('reset_password_tokens', [
            'token' => $token_before_expires_3h,
            'deleted' => 1,
        ]);
        
        $email = User::where('id', 1)->first()['email'];
        Mail::assertSent(
                ResetPasswordFinish::class,
                function ($mail) use ($email){
                    return $mail->hasTo($email);
                });

        $response3->assertViewIs('reset_password.finish');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=0, 有効期限内
     * key=パスワードリセット用トークンテーブルに存在しない文字列
     *
     * @dataProvider dataproviderValidData
     *
     * @param array 項目名 => 値
     *
     * @return void
     */
    public function testURLParamIsKey2($data)
    {
        $token_before_expires_3h = str_random(50);

        factory(User::class)->create(['id' => 1, 'deleted' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_before_expires_3h,
            'expires' => Carbon::now()->addHours(3),
        ]);

        $response1 = $this->withoutExceptionHandling()->post('reset_password_do?key='.str_random(49), $data);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=0, 有効期限内
     * ユーザー存在しない
     *
     * @dataProvider dataproviderValidData
     *
     * @param array 項目名 => 値
     *
     * @return void
     */
    public function testURLParamIsKey3($data)
    {
        $token_before_expires_3h = str_random(50);

        factory(User::class)->create(['id' => 1, 'deleted' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_before_expires_3h,
            'expires' => Carbon::now()->addHours(3),
        ]);

        $response1 = $this->withoutExceptionHandling()->post('reset_password_do?key='.$token_before_expires_3h, $data);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=0, 有効期限外
     *
     * @dataProvider dataproviderValidData
     *
     * @param array 項目名 => 値
     *
     * @return void
     */
    public function testURLParamIsKey4($data)
    {
        $token_after_expires_1s = str_random(50);
        
        do {
            $token_after_expires_3h = str_random(50);
        } while($token_after_expires_3h == $token_after_expires_1s);
        
        factory(User::class)->create(['id' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_after_expires_1s,
            'expires' => Carbon::now()->subSeconds(1),
        ]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_after_expires_3h,
            'expires' => Carbon::now()->subHours(3),
        ]);

        
        // 有効期限の境界値
        $response1 = $this->withoutExceptionHandling()->post('reset_password_do?key='.$token_after_expires_1s, $data);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
        
        // 有効期限の代表値
        $response2 = $this->withoutExceptionHandling()->post('reset_password_do?key='.$token_after_expires_3h, $data);

        $response2->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=1, 有効期限内
     *
     * @dataProvider dataproviderValidData
     *
     * @param array 項目名 => 値
     *
     * @return void
     */
    public function testURLParamIsKey5($data)
    {
        $token_before_expires_3h = str_random(50);
        
        factory(User::class)->create(['id' => 1, 'deleted' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_before_expires_3h,
            'expires' => Carbon::now()->addHours(3),
            'deleted' => 1,
        ]);

        $response1 = $this->withoutExceptionHandling()->post('reset_password_do?key='.$token_before_expires_3h, $data);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=1, 有効期限外
     *
     * @dataProvider dataproviderValidData
     *
     * @param array 項目名 => 値
     *
     * @return void
     */
    public function testURLParamIsKey6($data)
    {
        $token_after_expires_3h = str_random(50);
        
        factory(User::class)->create(['id' => 1, 'deleted' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_after_expires_3h,
            'expires' => Carbon::now()->subHours(3),
            'deleted' => 1,
        ]);

        $response1 = $this->withoutExceptionHandling()->post('reset_password_do?key='.$token_after_expires_3h, $data);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }

    public function dataproviderValidationError () {
        $error = [
                    'password.required' => '新しいパスワードは必須項目です。',
                    'password.regex' => '新しいパスワードは半角英数字6文字以上12文字以内でご登録ください。',
                    'password_to_check.required' => 'パスワード確認用は必須項目です。',
                    'password_to_check.same' => '新しいパスワードで入力したものと同じものをご入力ください。',
                 ];

        $password_valid = str_random(rand(6, 12));
        do {
            $password_different = str_random(rand(6, 12));
        } while ($password_different == $password_valid);

        $password_including_sign = $this->makeAlphaStringIncludingSign(1, 10);
        $password_including_kana = $this->makeAlphaStringIncludingKana(1, 10);

        $password_5_chars  = str_random(5);
        $password_13_chars = str_random(13);

        $data['test1'] = ['password' => '', 'password_to_check' => '',];
        $error_msg['test1'] = [
                                'password'          => $error['password.required'],
                                'password_to_check' => $error['password_to_check.required'],
                              ];

        $data['test2'] = ['password' => $password_valid, 'password_to_check' => '',];
        $error_msg['test2'] = [
                                'password' => '',
                                'password_to_check' => $error['password_to_check.required'],
                              ];

        $data['test3'] = ['password' => '', 'password_to_check' => $password_valid,];
        $error_msg['test3'] = [
                                'password' => $error['password.required'],
                                'password_to_check' => $error['password_to_check.same'],
                              ];

        $data['test4'] = ['password' => $password_valid, 'password_to_check' => $password_different,];
        $error_msg['test4'] = [
                                'password' => '',
                                'password_to_check' => $error['password_to_check.same'],
                              ];

        $data['test5'] = ['password' => $password_including_sign, 'password_to_check' => $password_including_sign,];
        $error_msg['test5'] = [
                                'password' => $error['password.regex'],
                                'password_to_check' => '',
                              ];

        $data['test6'] = ['password' => $password_including_kana, 'password_to_check' => $password_including_kana,];
        $error_msg['test6'] = [
                                'password' => $error['password.regex'],
                                'password_to_check' => '',
                              ];

        $data['test7'] = ['password' => $password_5_chars, 'password_to_check' => $password_5_chars,];
        $error_msg['test7'] = [
                                'password' => $error['password.regex'],
                                'password_to_check' => '',
                              ];

        $data['test8'] = ['password' => $password_13_chars, 'password_to_check' => $password_13_chars,];
        $error_msg['test8'] = [
                                'password' => $error['password.regex'],
                                'password_to_check' => '',
                              ];

        $keys = ['test1', 'test2', 'test3', 'test4', 'test5', 'test6', 'test7', 'test8'];

        foreach($keys as $key) {
            $test_data[] = [$data[$key], $error_msg[$key]];
        }

        return $test_data;
    }
    

    public function dataproviderValidData () {
        $password_valid['boundary_6_chars']  = str_random(6);
        $password_valid['utility_10_chars']  = str_random(10);
        $password_valid['boundary_12_chars'] = str_random(12);

        foreach($password_valid as $password) {
            $test_data[] = [
                               [
                                 'password'          => $password,
                                 'password_to_check' => $password,
                               ]
                           ];
        }

        return $test_data;
    }
}
