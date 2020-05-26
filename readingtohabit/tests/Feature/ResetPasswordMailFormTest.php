<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ResetPasswordMailFormTest extends TestCase
{
    /**
     * パスワードリセット用メールフォーム
     *
     * @return void
     */
    public function testResetPasswordMailForm()
    {
        $response_get = $this->get('reset_password_mail_form');
        $response_get->assertStatus(200)
                     ->assertViewIs('reset_password_mail.form');
        
        $response_post = $this->post('reset_password_mail_form');
        $response_post->assertStatus(405);

        $response_put = $this->put('reset_password_mail_form');
        $response_put->assertStatus(405);
        
        $response_patch = $this->patch('reset_password_mail_form');
        $response_patch->assertStatus(405);
        
        $response_delete = $this->delete('reset_password_mail_form');
        $response_delete->assertStatus(405);
        
        $response_options = $this->options('reset_password_mail_form');
        $response_options->assertStatus(200);
    }
}
