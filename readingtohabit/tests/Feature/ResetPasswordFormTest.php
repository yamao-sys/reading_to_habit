<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use App\User;
use App\ResetPasswordToken;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

// use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;

use Carbon\Carbon;

class ResetPasswordFormTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータなし、変数名keyでないクエリパラメータあり
     *
     * @return void
     */
    public function testURLParamIsNotKey()
    {
        $response1 = $this->get('reset_password_form');

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');

        // key以外のクエリパラメータ
        $token = str_random(50);
        do {
            $token_different = str_random(50);
        } while($token_different == $token);
        
        factory(User::class)->create(['id' => 1]);
        factory(ResetPasswordToken::class)->create(['user_id' => 1, 'token' => $token]);

        $response2 = $this->get('reset_password_form?test='.$token_different);

        $response2->assertStatus(200)
                  ->assertViewIs('common.invalid');

        $response3 = $this->get('reset_password_form?test='.$token);
        
        $response3->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(有効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=0, 有効期限内
     * ユーザー存在する
     *
     * @return void
     */
    public function testURLParamIsKey1()
    {
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
        
        // 境界値
        $response1 = $this->get('reset_password_form?key='.$token_expires_now);

        $response1->assertViewIs('reset_password.form')
                  ->assertViewHas('token', $token_expires_now);

        // 境界値
        $response2 = $this->get('reset_password_form?key='.$token_before_expires_1s);

        $response2->assertViewIs('reset_password.form')
                  ->assertViewHas('token', $token_before_expires_1s);
        
        // 代表値
        $response3 = $this->get('reset_password_form?key='.$token_before_expires_3h);

        $response3->assertViewIs('reset_password.form')
                  ->assertViewHas('token', $token_before_expires_3h);
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=0, 有効期限内
     * key=パスワードリセット用トークンテーブルに存在しない文字列
     *
     * @return void
     */
    public function testURLParamIsKey2()
    {
        $token_before_expires_3h = str_random(50);

        factory(User::class)->create(['id' => 1, 'deleted' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_before_expires_3h,
            'expires' => Carbon::now()->addHours(3),
        ]);

        $response1 = $this->get('reset_password_form?key='.str_random(49));

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }

    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=0, 有効期限内
     * ユーザー存在しない
     *
     * @return void
     */
    public function testURLParamIsKey3()
    {
        $token_before_expires_3h = str_random(50);

        factory(User::class)->create(['id' => 1, 'deleted' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_before_expires_3h,
            'expires' => Carbon::now()->addHours(3),
        ]);

        $response1 = $this->get('reset_password_form?key='.$token_before_expires_3h);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=0, 有効期限外
     *
     * @return void
     */
    public function testURLParamIsKey4()
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

        
        // 境界値
        $response1 = $this->get('reset_password_form?key='.$token_after_expires_1s);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
        
        // 代表値
        $response2 = $this->get('reset_password_form?key='.$token_after_expires_3h);

        $response2->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=1, 有効期限内
     *
     * @return void
     */
    public function testURLParamIsKey5()
    {
        $token_before_expires_3h = str_random(50);
        
        factory(User::class)->create(['id' => 1, 'deleted' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_before_expires_3h,
            'expires' => Carbon::now()->addHours(3),
            'deleted' => 1,
        ]);

        $response1 = $this->get('reset_password_form?key='.$token_before_expires_3h);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
    
    /**
     * パスワードリセットの情報入力画面のテスト(無効同値クラス)
     * クエリパラメータあり(変数名key)
     * トークン：deleted=1, 有効期限外
     *
     * @return void
     */
    public function testURLParamIsKey6()
    {
        $token_after_expires_3h = str_random(50);
        
        factory(User::class)->create(['id' => 1, 'deleted' => 1]);
        factory(ResetPasswordToken::class)->create([
            'user_id' => 1,
            'token' => $token_after_expires_3h,
            'expires' => Carbon::now()->subHours(3),
            'deleted' => 1,
        ]);

        $response1 = $this->get('reset_password_form?key='.$token_after_expires_3h);

        $response1->assertStatus(200)
                  ->assertViewIs('common.invalid');
    }
}
