<?php

namespace Tests\Browser;

use App\User;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ResetPasswordMailTest extends DuskTestCase
{
    use DatabaseMigrations;
    
    /**
     * パスワードリセット用メールのフォームから送信完了までのテスト
     *
     * @param array フォーム項目名 => 値
     * @param string 値
     * @dataProvider dataproviderExample
     * @return void
     */
    public function testResetPasswordMail($email, $error_msg)
    {
        factory(User::class)->create(['email' => 'aaa@bbb.ccc']);
        
        if (empty($error_msg)) {
            $this->browse(function (Browser $browser) use ($email){
                $browser->visit('reset_password_mail_form')
                        ->type('email', $email)
                        ->press('送信する')
                        ->assertPathIs('/reset_password_mail_do')
                        ->assertTitle('パスワードリセット用メールの送信完了');
            });
        }
        else {
            $this->browse(function (Browser $browser) use ($email, $error_msg){
                $browser->visit('reset_password_mail_form')
                        ->type('email', $email)
                        ->press('送信する')
                        ->assertTitle('パスワードリセット用メールの情報入力')
                        ->assertSee($error_msg);
            });
        }
    }
    
    public function dataproviderExample () {
        return [
                [
                 '', 
                 'メールアドレスは必須項目です。',
                ],
                [
                 'AAA@BBB.CCC', 
                 '',
                ],
                [
                 'ppp@qqq.rrr', 
                 '',
                ],
                [
                 'xxx@yyy.zzz', 
                 '',
                ],
                [
                 'aaa@bbb.ccc', 
                 '',
                ],
               ];
    }
}
