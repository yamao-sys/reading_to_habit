<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\User;
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
        // assert数 4 * 1 = 4

        factory(User::class)->create(['email' => 'AAA@BBB.CCC']);
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
        // assert数 3 * 1 + 1 * 3= 6
        Mail::fake();

        factory(User::class)->create(['email' => 'AAA@BBB.CCC']);
        $response = $this->post('resend_mail_do', $req);

        $is_none_error = empty($error_msg) ? true : false;

        if ($is_none_error == false) {
            $response->assertRedirect('');
        }
        else {
            $email = $req['email'];
            Mail::assertSent(
                    SuccessRegisterUser::class,
                    function ($mail) use ($email){
                        return $mail->hasTo($email);
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
        $response_get->assertStatus(405);

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
                    'error_msg' => 'Readingtohabitに登録されていないメールアドレスです。',
                ],
                [
                    'req' => ['email' => 'aaa@bbb.ccc'],
                    'error_msg' => 'Readingtohabitに登録されていないメールアドレスです。',
                ],
                [
                    'req' => ['email' => 'AAA@BBB.CCC'],
                    'error_msg' => '',
                ],
               ];
    }
}
