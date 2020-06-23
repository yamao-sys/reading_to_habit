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
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Response;

use Illuminate\Support\Collection;

class RegisterUserCheckTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * RegisterUserRequestのバリデーションテスト
     *
     * @param array フォーム項目名 => 値
     * @param array フォーム項目名 => 値
     * @dataProvider dataproviderExample
     */

    public function testRegisterUserValidation($data, $error_msg)
    {
        factory(User::class)->create(['email' => 'AAA@BBB.CCC']);
        factory(User::class)->create(['email' => 'ddd@eee.fff', 'deleted' => 1]);
        $request = new RegisterUserRequest();
        $rules = $request->rules();
        $messages = $request->messages();
        $validator = Validator::make($data, $rules, $messages);
        $errors = $validator->errors();
        
        $this->assertEquals($error_msg['name'], $errors->first('name'));
        $this->assertEquals($error_msg['email'], $errors->first('email'));
        $this->assertEquals($error_msg['password'], $errors->first('password'));
        $this->assertEquals($error_msg['password_to_check'], $errors->first('password_to_check'));
    }

    /**
     * RegisterUserRequestのバリデーションチェック結果のレスポンスのテスト
     *
     * @param array フォーム項目名 => 値
     * @param array フォーム項目名 => 値
     * @dataProvider dataproviderExample
     */
    public function testRegisterUserView($data, $error_msg)
    {
        $response = $this->post('register_user_check', $data);

        $is_existing_error = false;
        foreach ($error_msg as $msg) {
            if (!empty($msg)) {
                $is_existing_error = true;
            }
        }

        if ($is_existing_error == true) {
            $response->assertRedirect('');
            $response->assertSessionMissing('register_user_info_name');
            $response->assertSessionMissing('register_user_info_email');
            $response->assertSessionMissing('register_user_info_password');
        }
        else {
            $response->assertSessionHas('register_user_info_name', $data['name']);
            $response->assertSessionHas('register_user_info_email', $data['email']);
            $response->assertSessionHas('register_user_info_password', $data['password']);
            $password = '';
            for ($i=0; $i < strlen($data['password']); $i++) {
                $password .= '*';
            }
            $register_user_info = array(
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
            );
            $response->assertStatus(200)
                     ->assertViewIs('register_user.check')
                     ->assertViewHas('register_user_info', $register_user_info);
        }
    }

    /**
     * dataprovider
     *
     * @return array [[formdata1, error_msg1], [formdata2, error_msg2], ...]
     */

    public function dataproviderExample()
    {
        // テストデータの準備
        $name = $this->makeTestCaseOfName();
        
        // メールアドレスはtestRegisterUserCheck()にてテスト用にレコードを追加する
        // そのレコードのメールアドレスと同一かどうかを確認するため、ここではランダム文字列ではなく、固定的な文字列とする
        $email['unique'] = 'AAA@BBB.CCC';
        $email['valid1'] = 'aaa@bbb.ccc';
        $email['valid2'] = 'BBB@CCC.DDD';
        $email['valid3'] = 'ddd@eee.fff';

        $password = $this->makeTestCaseOfPassword();
        $password_to_check_different_from_password = $this->makeTestCaseOfPasswordToCheckDifferentFromPassword($password);
        
        $error_msg_arr = [
            'name_required' => 'ユーザー名は必須項目です。',
            'name_regex' => 'ユーザー名は半角英数字20文字以内でご登録ください。',
            'email_required' => 'メールアドレスは必須項目です。',
            'email_unique' => '既に登録済みのメールアドレスです。他のメールアドレスでご登録ください。',
            'password_required' => 'パスワードは必須項目です。',
            'password_regex' => 'パスワードは半角英数字6文字以上12文字以内でご登録ください。',
            'password_to_check_required' => 'パスワード確認用は必須項目です。',
            'password_to_check_same' => 'パスワードで入力したものと同じものをご入力ください。',
        
        ];

        $cases = ['case1',  'case2', 'case3', 'case4', 'case5', 'case6', 'case7', 'case8', 'case9'];

        // returnするarray([[formdata1, error_msg1], [formdata2, error_msg2], ...])を作る
        $formdata  = $this->makeFormData($cases, $name, $email, $password, $password_to_check_different_from_password);
        $error_msg = $this->makeErrorMsg($cases, $error_msg_arr);
        
        $min_level = 5;
        foreach ($cases as $case) {
            for ($i = 0; $i < $min_level; $i++) {
                $testcase[] = [$formdata[$case][$i], $error_msg[$case][$i]];
            }
        }

        return $testcase;
    }

    public function makeTestCaseOfName() {
        $name['case1'] = '';
        $name['case2'] = $this->makeAlphaStringIncludingSign(1, 20);
        $name['case3'] = $this->makeAlphaStringIncludingKana(1, 20);
        $name['case4'] = str_random(21);
        $name['case5'] = str_random(1);
        $name['case6'] = str_random(10);
        $name['case7'] = str_random(19);
        $name['case8'] = str_random(20);
        $name['case9'] = str_random(20);

        return $name;
    }
    
    public function makeTestCaseOfPassword() {
        $password['case1'] = '';
        $password['case2'] = str_random(5);
        $password['case3'] = $this->makeAlphaStringIncludingSign(1, 12);
        $password['case4'] = str_random(13);
        $password['case5'] = str_random(6);
        $password['case6'] = str_random(7);
        $password['case7'] = str_random(11);
        $password['case8'] = str_random(12);
        $password['case9'] = str_random(12);

        return $password;
   }

   public function makeTestCaseOfPasswordToCheckDifferentFromPassword($password) {
       foreach ($password as $case => $pwd) {
           if ($case == 'case1') {
               $password_to_check_different_from_password[$case] = str_random(rand(6,12));
           }
           else {
               $password_to_check_different_from_password[$case] = str_shuffle($pwd);
               while ($pwd == $password_to_check_different_from_password[$case]) {
                   $password_to_check_different_from_password[$case] = str_shuffle($pwd);
               }
           }
       }

       return $password_to_check_different_from_password;
   }

    public function makeFormData ($cases, $name, $email, $password, $password_to_check_different_from_password) {
        foreach ($cases as $case) {
            $formdata[$case][0] = [
                                    'name' => $name[$case],
                                    'email' => '',
                                    'password' => $password[$case],
                                    'password_to_check' => '',
                                  ];
            $formdata[$case][1] = [
                                    'name' => $name[$case],
                                    'email' => $email['unique'],
                                    'password' => $password[$case],
                                    'password_to_check' => $password_to_check_different_from_password[$case],
                                  ];
            $formdata[$case][2] = [
                                    'name' => $name[$case],
                                    'email' => $email['valid1'],
                                    'password' => $password[$case],
                                    'password_to_check' => $password[$case],
                                  ];
            $formdata[$case][3] = [
                                    'name' => $name[$case],
                                    'email' => $email['valid2'],
                                    'password' => $password[$case],
                                    'password_to_check' => $password[$case],
                                  ];
            $formdata[$case][4] = [
                                    'name' => $name[$case],
                                    'email' => $email['valid3'],
                                    'password' => $password[$case],
                                    'password_to_check' => $password[$case],
                                  ];

        }

        return $formdata;
    }

    public function makeErrorMsg ($cases, $error_msg_arr) {
        foreach ($cases as $case) {
            if ($case == 'case1') {
                $error_msg[$case][0] = [
                                         'name' => $error_msg_arr['name_required'],
                                         'email' => $error_msg_arr['email_required'],
                                         'password' => $error_msg_arr['password_required'],
                                         'password_to_check' => $error_msg_arr['password_to_check_required'],
                                       ];
                $error_msg[$case][1] = [
                                         'name' => $error_msg_arr['name_required'],
                                         'email' => $error_msg_arr['email_unique'],
                                         'password' => $error_msg_arr['password_required'],
                                         'password_to_check' => $error_msg_arr['password_to_check_same'],
                                       ];
                $error_msg[$case][2] = [
                                         'name' => $error_msg_arr['name_required'],
                                         'email' => '',
                                         'password' => $error_msg_arr['password_required'],
                                         'password_to_check' => $error_msg_arr['password_to_check_required'],
                                       ];
                $error_msg[$case][3] = [
                                         'name' => $error_msg_arr['name_required'],
                                         'email' => '',
                                         'password' => $error_msg_arr['password_required'],
                                         'password_to_check' => $error_msg_arr['password_to_check_required'],
                                       ];
                $error_msg[$case][4] = [
                                         'name' => $error_msg_arr['name_required'],
                                         'email' => '',
                                         'password' => $error_msg_arr['password_required'],
                                         'password_to_check' => $error_msg_arr['password_to_check_required'],
                                       ];
            }
            elseif ($case == 'case2' || $case == 'case3' || $case == 'case4') {
                $error_msg[$case][0] = [
                                         'name' => $error_msg_arr['name_regex'],
                                         'email' => $error_msg_arr['email_required'],
                                         'password' => $error_msg_arr['password_regex'],
                                         'password_to_check' => $error_msg_arr['password_to_check_required'],
                                       ];
                $error_msg[$case][1] = [
                                         'name' => $error_msg_arr['name_regex'],
                                         'email' => $error_msg_arr['email_unique'],
                                         'password' => $error_msg_arr['password_regex'],
                                         'password_to_check' => $error_msg_arr['password_to_check_same'],
                                       ];
                $error_msg[$case][2] = [
                                         'name' => $error_msg_arr['name_regex'],
                                         'email' => '',
                                         'password' => $error_msg_arr['password_regex'],
                                         'password_to_check' => '',
                                       ];
                $error_msg[$case][3] = [
                                         'name' => $error_msg_arr['name_regex'],
                                         'email' => '',
                                         'password' => $error_msg_arr['password_regex'],
                                         'password_to_check' => '',
                                       ];
                $error_msg[$case][4] = [
                                         'name' => $error_msg_arr['name_regex'],
                                         'email' => '',
                                         'password' => $error_msg_arr['password_regex'],
                                         'password_to_check' => '',
                                       ];
            }
            else {
                $error_msg[$case][0] = [
                                         'name' => '',
                                         'email' => $error_msg_arr['email_required'],
                                         'password' => '',
                                         'password_to_check' => $error_msg_arr['password_to_check_required'],
                                       ];
                $error_msg[$case][1] = [
                                         'name' => '',
                                         'email' => $error_msg_arr['email_unique'],
                                         'password' => '',
                                         'password_to_check' => $error_msg_arr['password_to_check_same'],
                                       ];
                $error_msg[$case][2] = [
                                         'name' => '',
                                         'email' => '',
                                         'password' => '',
                                         'password_to_check' => '',
                                       ];
                $error_msg[$case][3] = [
                                         'name' => '',
                                         'email' => '',
                                         'password' => '',
                                         'password_to_check' => '',
                                       ];
                $error_msg[$case][4] = [
                                         'name' => '',
                                         'email' => '',
                                         'password' => '',
                                         'password_to_check' => '',
                                       ];
            }
        }

        return $error_msg;
    }
}
