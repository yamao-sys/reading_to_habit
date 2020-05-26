<?php

namespace Tests\Browser;

use App\User;
use App\ResetPasswordToken;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ResetPasswordTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    /**
     * パスワードリセット用メールのフォームから送信完了までのテスト
     *
     * @param string 値
     * @param string 値
     * @param string 値
     * @param string 値
     * @param string 値
     * @param string 値
     * @dataProvider dataproviderExample
     * @return void
     */
    public function testResetPassword($email, $error_msg, $password, $password_to_check, $password_error_msg, $password_to_check_error_msg)
    {
        factory(User::class)->create(['id' => 1, 'email' => 'aaa@bbb.ccc']);
        
        if (empty($error_msg)) {
            $this->browse(function (Browser $first, Browser $second) use ($email, $password, $password_to_check, $password_error_msg, $password_to_check_error_msg){
                $first->visit('reset_password_mail_form')
                        ->type('email', $email)
                        ->press('送信する')
                        ->assertPathIs('/reset_password_mail_do')
                        ->assertTitle('パスワードリセット用メールの送信完了');

                if ($email === 'aaa@bbb.ccc') {
                    $token = ResetPasswordToken::where('user_id', 1)->first();
                    $second->visit('reset_password_form?key='.$token['token'])
                           ->type('password', $password)
                           ->type('password_to_check', $password_to_check)
                           ->press('更新する');
                    if (empty($password_error_msg) && empty($password_to_check_error_msg)) {
                        $second->assertTitle('パスワードリセット完了');
                    }

                    if (empty($password_error_msg) && !empty($password_to_check_error_msg)) {
                        $second->assertTitle('パスワードリセット情報入力')
                               ->assertSee($password_to_check_error_msg);
                    }

                    if (!empty($password_error_msg) && empty($password_to_check_error_msg)) {
                        $second->assertTitle('パスワードリセット情報入力')
                               ->assertSee($password_error_msg);
                    }
                    
                    if (!empty($password_error_msg) && !empty($password_to_check_error_msg)) {
                        $second->assertTitle('パスワードリセット情報入力')
                               ->assertSee($password_error_msg)
                               ->assertSee($password_to_check_error_msg);
                    }
                }

            });
        }
        else {
            $this->browse(function (Browser $first, Browser $second) use ($email, $error_msg){
                $first->visit('reset_password_mail_form')
                        ->type('email', $email)
                        ->press('送信する')
                        ->assertTitle('パスワードリセット用メールの情報入力')
                        ->assertSee($error_msg);
            });
        }
    }
    
    public function dataproviderExample () {
        $password = str_random(rand(6, 12));
        
        $password_invalid = str_random(rand(13, 255));
        
        do {
            $password_different = str_random(rand(6, 12));
        } while($password_different === $password);

        $password_error = [
            'password.required' => '新しいパスワードは必須項目です。',
            'password.regex' => '新しいパスワードは半角英数字6文字以上12文字以内でご登録ください。',
            'password_to_check.required' => 'パスワード確認用は必須項目です。',
            'password_to_check.same' => '新しいパスワードで入力したものと同じものをご入力ください。',
        ];
        return [
                [
                 '', 
                 'メールアドレスは必須項目です。',
                 '',
                 '',
                 '',
                 ''
                ],
                [
                 'AAA@BBB.CCC', 
                 '',
                 '',
                 '',
                 '',
                 '',
                ],
                [
                 'ppp@qqq.rrr', 
                 '',
                 '',
                 '',
                 '',
                 '',
                ],
                [
                 'xxx@yyy.zzz', 
                 '',
                 '',
                 '',
                 '',
                 '',
                ],
                [
                 'aaa@bbb.ccc', 
                 '',
                 '',
                 '',
                 $password_error['password.required'],
                 $password_error['password_to_check.required'],
                ],
                [
                 'aaa@bbb.ccc', 
                 '',
                 '',
                 $password_different,
                 $password_error['password.required'],
                 $password_error['password_to_check.same'],
                ],
                [
                 'aaa@bbb.ccc', 
                 '',
                 $password_invalid,
                 '',
                 $password_error['password.regex'],
                 $password_error['password_to_check.required'],
                ],
                [
                 'aaa@bbb.ccc', 
                 '',
                 $password_invalid,
                 $password_different,
                 $password_error['password.regex'],
                 $password_error['password_to_check.same'],
                ],
                [
                 'aaa@bbb.ccc', 
                 '',
                 $password,
                 '',
                 '',
                 $password_error['password_to_check.required'],
                ],
                [
                 'aaa@bbb.ccc', 
                 '',
                 $password,
                 $password_different,
                 '',
                 $password_error['password_to_check.same'],
                ],
                [
                 'aaa@bbb.ccc', 
                 '',
                 $password_invalid,
                 $password_invalid,
                 $password_error['password.regex'],
                 '',
                ],
                [
                 'aaa@bbb.ccc', 
                 '',
                 $password,
                 $password,
                 '',
                 '',
                ],
               ];
    }
}
