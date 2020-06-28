<?php

namespace Tests\Feature;

use App\User;
use App\DefaultMailTiming;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;
use App\Article;
use App\ArticleMailTiming;
use App\ArticleMailTimingMaster;
use App\ArticleMailTimingSelectMaster;
use App\AutoLoginToken;
use App\ResetPasswordToken;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Response;

use Carbon\Carbon;

class DeleteUserTest extends TestCase
{
    use DatabaseMigrations;    

    /**
     * ログインユーザーのもののみ削除されることを確認する
     *
     * @return void
     */
    public function testDeleteUser()
    {
        factory(DefaultMailTimingMaster::class, 2)->create();
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => 1]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => 2]);
        factory(AutoLoginToken::class, 5)->create(['user_id' => 1]);
        factory(AutoLoginToken::class, 5)->create(['user_id' => 2]);
        factory(ResetPasswordToken::class, 5)->create(['user_id' => 1]);
        factory(ResetPasswordToken::class, 5)->create(['user_id' => 2]);
        factory(ArticleMailTimingMaster::class, 20)->create();
        
        for ($i = 1; $i <= 10; $i++) {
            Article::withoutGlobalScopes()->where('id', $i)->first()->update(['user_id' => 1]);
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        for ($i = 11; $i <= 20; $i++) {
            Article::withoutGlobalScopes()->where('id', $i)->first()->update(['user_id' => 2]);
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }

        $user1 = User::where('id', 1)->first();
        $user2 = User::where('id', 2)->first();

        $response = $this->withSession(['user_id' => $user1['id'], 'profile_img' => $user1['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('delete_user');

        $this->assertDatabaseHas('users', ['id' => 1, 'deleted' => 1]);
        $this->assertDatabaseHas('users', ['id' => 2, 'deleted' => 0]);
        $this->assertDatabaseHas('default_mail_timings', ['user_id' => 1, 'deleted' => 1]);
        $this->assertDatabaseHas('default_mail_timings', ['user_id' => 2, 'deleted' => 0]);
        $this->assertDatabaseHas('default_mail_timing_masters', ['default_mail_timing_id' => 1, 'deleted' => 1]);
        $this->assertDatabaseHas('default_mail_timing_masters', ['default_mail_timing_id' => 2, 'deleted' => 0]);
        $this->assertDatabaseHas('default_mail_timing_select_masters', ['default_mail_timing_id' => 1, 'deleted' => 1]);
        $this->assertDatabaseHas('default_mail_timing_select_masters', ['default_mail_timing_id' => 2, 'deleted' => 0]);
        for ($i = 1; $i <= 5; $i++) {
            $this->assertDatabaseHas('auto_login_tokens', ['id' => $i, 'user_id' => 1, 'deleted' => 1]);
            $this->assertDatabaseHas('reset_password_tokens', ['id' => $i, 'user_id' => 1, 'deleted' => 1]);
        }

        for ($i = 6; $i <= 10; $i++) {
            $this->assertDatabaseHas('auto_login_tokens', ['id' => $i, 'user_id' => 2, 'deleted' => 0]);
            $this->assertDatabaseHas('reset_password_tokens', ['id' => $i, 'user_id' => 2, 'deleted' => 0]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $this->assertDatabaseHas('articles', ['id' => $i, 'user_id' => 1, 'deleted' => 1]);
            $this->assertDatabaseHas('article_mail_timings', ['id' => $i, 'article_id' => $i, 'deleted' => 1]);
            $this->assertDatabaseHas('article_mail_timing_masters', ['id' => $i, 'article_mail_timing_id' => $i, 'deleted' => 1]);
            $this->assertDatabaseHas('article_mail_timing_select_masters', ['id' => $i, 'article_mail_timing_id' => $i, 'deleted' => 1]);
        }

        for ($i = 11; $i <= 20; $i++) {
            $this->assertDatabaseHas('articles', ['id' => $i, 'user_id' => 2, 'deleted' => 0]);
            $this->assertDatabaseHas('article_mail_timings', ['id' => $i, 'article_id' => $i, 'deleted' => 0]);
            $this->assertDatabaseHas('article_mail_timing_masters', ['id' => $i, 'article_mail_timing_id' => $i, 'deleted' => 0]);
            $this->assertDatabaseHas('article_mail_timing_select_masters', ['id' => $i, 'article_mail_timing_id' => $i, 'deleted' => 0]);
        }

        $response->assertJson(['is_success' => true]);
    }
}
