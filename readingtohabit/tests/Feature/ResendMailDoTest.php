<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use App\User;
use App\DefaultMailTiming;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ResendMailRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuccessRegisterUser;

class ResendMailDoTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * ResendMailRequestのバリデーションテスト
     *
     * @param array フォーム項目名 => 値
     * @param array フォーム項目名 => 値
     * @dataProvider dataproviderExample
     */

    public function testResendMailValidation($req, $error_msg)
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'yamaopanku@gmail.com']);

        $request = new ResendMailRequest();
        $rules = $request->rules();
        $messages = $request->messages();
        $validator = Validator::make($req, $rules, $messages);
        $errors = $validator->errors();
        
        $this->assertEquals($error_msg, $errors->first('email'));
    }
    
    /**
     * ResendMailRequestのバリデーションチェック結果のレスポンスのテスト
     *
     * @param array フォーム項目名 => 値
     * @param array フォーム項目名 => 値
     * @dataProvider dataproviderExample
     */
    public function testResendMailDo($req, $error_msg)
    {
        Mail::fake();

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'yamaopanku@gmail.com']);
        
        $response = $this->post('resend_mail_do', $req);

        if ($req['email'] !== 'yamaopanku@gmail.com' && empty($req['email'])) {
            $response->assertSessionHasErrors('email');
        }
        elseif ($req['email'] !== 'yamaopanku@gmail.com' && !empty($req['email'])) {
            $response->assertViewIs('resend_mail.finish');
        }
        elseif ($req['email'] === 'yamaopanku@gmail.com') {
            $email = $req['email'];
            Mail::assertSent(
                    SuccessRegisterUser::class,
                    function ($mail) use ($email){
                        return $mail->to[0]['address'] === $email;
                    });
            
            $response->assertStatus(200)
                     ->assertViewIs('resend_mail.finish');
        }
    }
    
    /**
     * ResendMailDoへのアクセステスト
     *
     * @return void
     */
    public function testResendMailDoAccess () {
        $response_get = $this->get('resend_mail_do');
        $response_get->assertStatus(200)
                     ->assertViewIs('common.invalid');

        $response_put = $this->put('resend_mail_do');
        $response_put->assertStatus(405);
        
        $response_patch = $this->patch('resend_mail_do');
        $response_patch->assertStatus(405);
        
        $response_delete = $this->delete('resend_mail_do');
        $response_delete->assertStatus(405);
        
        $response_options = $this->options('resend_mail_do');
        $response_options->assertStatus(200);
    }

    public function dataproviderExample () {
        return [
                [
                    'req' => ['email' => ''],
                    'error_msg' => 'メールアドレスは必須項目です。',
                ],
                [
                    'req' => ['email' => 'xxx@yyy.zzz'],
                    'error_msg' => '',
                ],
                [
                    'req' => ['email' => 'aaa@bbb.ccc'],
                    'error_msg' => '',
                ],
                [
                    'req' => ['email' => 'yamaopanku@gmail.com'],
                    'error_msg' => '',
                ],
               ];
    }
}
