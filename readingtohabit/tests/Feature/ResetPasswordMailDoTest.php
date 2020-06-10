<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ResetPasswordMailRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use Carbon\Carbon;

class ResetPasswordMailDoTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * ResetPasswordMailRequestのバリデーションテスト
     * └　未入力：data1
     * └　入力値に相当すユーザーがいない：data2～data4
     * └ 入力値に相当するユーザーがいる：data5
     * 
     * @param array フォーム項目名 => 値
     * @param string 値
     * @dataProvider dataproviderExample
     * @return void
     */

    public function testResendMailValidation($req, $error_msg)
    {
        factory(User::class)->create(['email' => 'aaa@bbb.ccc']);
        factory(User::class)->create(['email' => 'xxx@yyy.zzz', 'deleted' => 1]);

        $request = new ResetPasswordMailRequest();
        $rules = $request->rules();
        $messages = $request->messages();
        $validator   = Validator::make($req, $rules, $messages);
        $errors = $validator->errors();

        $this->assertEquals($error_msg, $errors->first('email'));
    }
    
    /**
     * ResetPasswordMailRequestのバリデーションチェック結果のレスポンスのテスト
     *
     * @param array フォーム項目名 => 値
     * @param string 値
     * @dataProvider dataproviderExample
     */
    public function testResetPasswordMailDo($req, $error_msg)
    {
        Mail::fake();

        factory(User::class)->create(['email' => 'aaa@bbb.ccc']);
        factory(User::class)->create(['email' => 'xxx@yyy.zzz', 'deleted' => 1]);
        
        $response = $this->post('reset_password_mail_do', $req);

        $is_none_error = empty($error_msg) ? true : false;

        if ($is_none_error == false) {
            $response->assertRedirect('')
                     ->assertSessionHasErrors(['email' => $error_msg]);
        }
        else {
            $email = $req['email'];

            $user = User::where('email', $email)->first();
            if (empty($user)) {
                $this->assertDatabaseMissing('reset_password_tokens', [
                    'expires'    => Carbon::now()->addHours(\ResetPasswordTokenConst::EXPIRES_HOURS),
                    'deleted'    => 0,
                    'deleted_at' => null,
                ]);
                Mail::assertNotSent(ResetPassword::class);
            }
            else {
                $this->assertDatabaseHas('reset_password_tokens', [
                    'user_id'    => $user['id'],
                    'expires'    => Carbon::now()->addHours(\ResetPasswordTokenConst::EXPIRES_HOURS),
                    'deleted'    => 0,
                    'deleted_at' => null,
                ]);
                Mail::assertSent(
                        ResetPassword::class,
                        function ($mail) use ($email){
                         return $mail->hasTo($email);
                });
            }
            
            $response->assertStatus(200)
                     ->assertViewIs('reset_password_mail.finish');
        }
    }
    
    /**
     * ResetPasswordMailDoへのアクセステスト
     *
     * @return void
     */
    public function testResetPasswordMailDoAccess () {
        $response_get = $this->get('reset_password_mail_do');
        $response_get->assertStatus(405);

        $response_put = $this->put('reset_password_mail_do');
        $response_put->assertStatus(405);
        
        $response_patch = $this->patch('reset_password_mail_do');
        $response_patch->assertStatus(405);
        
        $response_delete = $this->delete('reset_password_mail_do');
        $response_delete->assertStatus(405);
        
        $response_options = $this->options('reset_password_mail_do');
        $response_options->assertStatus(200);
    }

    public function dataproviderExample () {
        return [
                [
                 ['email' => ''], 
                 'メールアドレスは必須項目です。',
                ],
                [
                 ['email' => 'AAA@BBB.CCC'], 
                 '',
                ],
                [
                 ['email' => 'ppp@qqq.rrr'], 
                 '',
                ],
                [
                 ['email' => 'xxx@yyy.zzz'], 
                 '',
                ],
                [
                 ['email' => 'aaa@bbb.ccc'], 
                 '',
                ],
               ];
    }
}
