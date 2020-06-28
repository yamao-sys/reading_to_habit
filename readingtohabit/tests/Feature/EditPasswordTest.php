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

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Response;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EditPasswordTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * current_password: 空
     * new_password: 6文字以上12文字以内
     * new_password_to_check: new_passwordと同じ
     *
     * @return void
     */
    public function testEditPassword1()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $user = User::first();

        $new_password = str_random(rand(6, 12));
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => '',
                            'new_password'  => $new_password,
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasErrors('current_password', '現在のパスワードは必須項目です。');
    }
    
    /**
     * current_password: 現在のパスワードと異なる文字列
     * new_password: 6文字以上12文字以内
     * new_password_to_check: new_passwordと同じ
     *
     * @return void
     */
    public function testEditPassword2()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        do {
            $different_password = str_random(rand(6, 12));
        } while($current_password === $different_password);
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = str_random(rand(6, 12));
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $different_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasErrors('current_password', '現在のパスワードが間違っています。');
    }
    
    /**
     * current_password: 現在のパスワード
     * new_password: 空
     * new_password_to_check: 6文字以上12文字以内
     *
     * @return void
     */
    public function testEditPassword3()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = str_random(rand(6, 12));
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => '',
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasErrors('new_password', '新しいパスワードは必須項目です。');
        $response->assertSessionHasErrors('new_password_to_check', '新しいパスワードで入力したものと同じものをご入力ください。');
    }
    
    /**
     * current_password: 現在のパスワード
     * new_password: 半角+全角10文字
     * new_password_to_check: new_password
     *
     * @return void
     */
    public function testEditPassword4()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = $this->makeAlphaStringIncludingKana(1, 10);
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasErrors('new_password', '新しいパスワードは半角英数字6文字以上12文字以内でご登録ください。');
    }
    
    /**
     * current_password: 現在のパスワード
     * new_password: 半角+記号10文字
     * new_password_to_check: new_password
     *
     * @return void
     */
    public function testEditPassword5()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = $this->makeAlphaStringIncludingSign(1, 10);
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasErrors('new_password', '新しいパスワードは半角英数字6文字以上12文字以内でご登録ください。');
    }
    
    /**
     * current_password: 現在のパスワード
     * new_password: 半角5文字
     * new_password_to_check: new_password
     *
     * @return void
     */
    public function testEditPassword6()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = str_random(5);
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasErrors('new_password', '新しいパスワードは半角英数字6文字以上12文字以内でご登録ください。');
    }
    
    /**
     * current_password: 現在のパスワード
     * new_password: 半角13文字
     * new_password_to_check: new_password
     *
     * @return void
     */
    public function testEditPassword7()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = str_random(13);
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasErrors('new_password', '新しいパスワードは半角英数字6文字以上12文字以内でご登録ください。');
    }
    
    /**
     * current_password: 現在のパスワード
     * new_password: 半角6文字
     * new_password_to_check: new_password
     *
     * @return void
     */
    public function testEditPassword8()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = str_random(6);
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * current_password: 現在のパスワード
     * new_password: 半角10文字
     * new_password_to_check: new_password
     *
     * @return void
     */
    public function testEditPassword9()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = str_random(10);
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * current_password: 現在のパスワード
     * new_password: 半角12文字
     * new_password_to_check: new_password
     *
     * @return void
     */
    public function testEditPassword10()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = str_random(12);
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => $new_password
                         ]);

        $response->assertSessionHasNoErrors();
    }

    /**
     * current_password: 現在のパスワード
     * new_password: 半角10文字
     * new_password_to_check: 空
     *
     * @return void
     */
    public function testEditPassword11()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = str_random(10);
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => ''
                         ]);

        $response->assertSessionHasErrors('new_password_to_check');
    }
    
    /**
     * current_password: 現在のパスワード
     * new_password: 半角10文字
     * new_password_to_check: new_passwordと異なる
     *
     * @return void
     */
    public function testEditPassword12()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        $current_password = str_random(rand(6, 12));
        User::first()->update(['password' => Hash::make($current_password)]);
        $user = User::first();

        $new_password = str_random(10);
        do {
            $diff_new_password = str_random(10);
        } while($diff_new_password === $new_password);
        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_password', [
                            'current_password' => $current_password,
                            'new_password'  => $new_password,
                            'new_password_to_check' => $diff_new_password
                         ]);

        $response->assertSessionHasErrors('new_password_to_check', '新しいパスワードで入力したものと同じものをご入力ください。');
    }
}
