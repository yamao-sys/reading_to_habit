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
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuccessRegisterUser;

class RegisterUserDoTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * RegisterUserRequestのバリデーションテスト
     *
     * @param array 登録情報項目名 => 値
     * @dataProvider dataproviderExample
     */
    public function testRegisterUserDo($register_user_info)
    {
        Mail::fake();
        // assert数：2 * 1 + 10 * 1= 12
        $response = $this->withSession($register_user_info)
                         ->post('register_user_do');

        if (empty($register_user_info['register_user_info_name'])){
            $response->assertStatus(200)
                     ->assertViewIs('common.invalid');
        }
        else {
            $this->assertDatabaseHas('users', ['email' => $register_user_info['register_user_info_email']]);
            
            $added_user_id = User::where('email', $register_user_info['register_user_info_email'])->first()['id'];
            $this->assertDatabaseHas('default_mail_timings', ['user_id' => $added_user_id]);

            $added_default_mail_timing_id = DefaultMailTiming::where('user_id', $added_user_id)->first()['id'];
            $this->assertDatabaseHas('default_mail_timing_masters', ['default_mail_timing_id' => $added_default_mail_timing_id]);
            $this->assertDatabaseHas('default_mail_timing_select_masters', ['default_mail_timing_id' => $added_default_mail_timing_id]);

            $email = $register_user_info['register_user_info_email'];
            
            Mail::assertSent(
                    SuccessRegisterUser::class,
                    function ($mail) use ($email){
                        return $mail->hasTo($email);
                    });

            $response->assertSessionMissing('register_user_info_name');
            $response->assertSessionMissing('register_user_info_email');
            $response->assertSessionMissing('register_user_info_password');
            
            $response->assertStatus(200)
                     ->assertViewIs('register_user.finish');
        }
    }
    
    /**
     * RegisterUserDoへのアクセステスト
     *
     * @return void
     */
    public function testRegisterUserDoAccess () {
        $response_get = $this->get('register_user_do');
        $response_get->assertStatus(200)
                     ->assertViewIs('common.invalid');

        $response_put = $this->put('register_user_do');
        $response_put->assertStatus(405);
        
        $response_patch = $this->patch('register_user_do');
        $response_patch->assertStatus(405);
        
        $response_delete = $this->delete('register_user_do');
        $response_delete->assertStatus(405);
        
        $response_options = $this->options('register_user_do');
        $response_options->assertStatus(200);
    }

    /**
     * dataprovider
     *
     * @return array [[register_user_info1], [register_user_info2], ...]
     */

    public function dataproviderExample() {
        $register_user_info_invalid = [
                                        'register_user_info_name' => '',
                                        'register_user_info_email' => '',
                                        'register_user_info_password' => '',
                                      ];
        $register_user_info = [
                                'register_user_info_name' => str_random(rand(1, 20)),
                                'register_user_info_email' => 'AAA@BBB.CCC',
                                'register_user_info_password' => str_random(rand(6, 12)),
                              ];

        return [
                [
                    'register_user_info' => $register_user_info_invalid,
                ],
                [
                    'register_user_info' => $register_user_info,
                ],
               ];
    }
}
