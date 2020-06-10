<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ResendMailFormTest extends TestCase
{
    /**
     * ResendMailFormのテスト
     *
     * @return void
     */
    public function testResendMailForm()
    {
        $response_get = $this->get('resend_mail_form');
        $response_get->assertStatus(200)
                     ->assertViewIs('resend_mail.form');
        
        $response_post = $this->post('resend_mail_form');
        $response_post->assertStatus(405);

        $response_put = $this->put('resend_mail_form');
        $response_put->assertStatus(405);
        
        $response_patch = $this->patch('resend_mail_form');
        $response_patch->assertStatus(405);
        
        $response_delete = $this->delete('resend_mail_form');
        $response_delete->assertStatus(405);
        
        $response_options = $this->options('resend_mail_form');
        $response_options->assertStatus(200);
    }
}
