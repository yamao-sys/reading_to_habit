<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Illuminate\Http\Response;
// use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class RegisterUserFmTest extends TestCase
{
    /**
     * RegisterUserFormテスト
     *
     * @param array 項目名 => 値
     * @dataProvider dataproviderExample 
     */

    public function testRegisterUserForm($session_register_user_info, $register_user_info)
    {
        $response_get_not_has_session = $this->get('register_user_form');
        $response_get_not_has_session->assertStatus(200)
                                     ->assertViewIs('register_user.form');

        $response_get_has_session = $this->withSession($session_register_user_info)
                                         ->get('register_user_form');
        $response_get_has_session->assertStatus(200)
                                 ->assertViewIs('register_user.form')
                                 ->assertSessionHas($session_register_user_info);


        $response_post = $this->post('register_user_form');
        $response_post->assertStatus(405);

        $response_put = $this->put('register_user_form');
        $response_put->assertStatus(405);
        
        $response_patch = $this->patch('register_user_form');
        $response_patch->assertStatus(405);
        
        $response_delete = $this->delete('register_user_form');
        $response_delete->assertStatus(405);
        
        $response_options = $this->options('register_user_form');
        $response_options->assertStatus(200);
    }

    public function dataproviderExample() {
        $name = str_random(rand(1, 20));
        $email = 'AAA@BBB.CCC';

        $session_register_user_info = [
                                        'register_user_info_name' => $name,
                                        'register_user_info_email' => $email,
                                      ];
        $register_user_info = [
                                'name' => $name,
                                'email' => $email,
                              ];
        return [
                [
                    'session_register_user_info' => $session_register_user_info,
                    'register_user_info' => $register_user_info,
                ]
               ];
    }
}
