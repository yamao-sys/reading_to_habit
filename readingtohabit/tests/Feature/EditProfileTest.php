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
use Illuminate\Http\UploadedFile;

use Carbon\Carbon;
use Storage;

use Illuminate\Support\Collection;

class EditProfileTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * profile_img: 画像ファイルでないファイル
     * name: 1文字以上20文字以下
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile1()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->create('test.txt'),
                            'name'  => str_random(rand(1, 20)),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasErrors('profile_img', '画像ファイルをご選択ください。');
    }
    
    /**
     * profile_img: 画像ファイルだが、2MB超え
     * name: 1文字以上20文字以下
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile2()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.jpg')->size(2050),
                            'name'  => str_random(rand(1, 20)),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasErrors('profile_img', '2MB以内のサイズの画像をご選択ください。');
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 1文字以上20文字以下
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile3()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.jpg'),
                            'name'  => str_random(rand(1, 20)),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(png)
     * name: 1文字以上20文字以下
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile4()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.png'),
                            'name'  => str_random(rand(1, 20)),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(bmp)
     * name: 1文字以上20文字以下
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile5()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.bmp'),
                            'name'  => str_random(rand(1, 20)),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(gif)
     * name: 1文字以上20文字以下
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile6()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.gif'),
                            'name'  => str_random(rand(1, 20)),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(svg)
     * name: 1文字以上20文字以下
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile7()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.svg'),
                            'name'  => str_random(rand(1, 20)),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(webp)
     * name: 1文字以上20文字以下
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile8()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => str_random(rand(1, 20)),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 空
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile9()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => '',
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasErrors('name', 'ユーザー名は必須項目です。');
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角+全角で10文字
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile10()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => $this->makeAlphaStringIncludingKana(1, 10),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasErrors('name', 'ユーザー名は半角英数字20文字以内でご登録ください。');
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角+記号で10文字
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile11()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => $this->makeAlphaStringIncludingSign(1, 10),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasErrors('name', 'ユーザー名は半角英数字20文字以内でご登録ください。');
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角21文字
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile12()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => str_random(21),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasErrors('name', 'ユーザー名は半角英数字20文字以内でご登録ください。');
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角1文字
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile13()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => str_random(1),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角20文字
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile14()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => str_random(20),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角10文字
     * email: 現在のメールアドレス
     *
     * @return void
     */
    public function testEditProfile15()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => str_random(10),
                            'email' => $user['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角10文字
     * email: 空
     *
     * @return void
     */
    public function testEditProfile16()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        User::first()->update(['email' => 'aaa@bbb.ccc']);
        $user = User::first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => str_random(10),
                            'email' => ''
                         ]);

        $response->assertSessionHasErrors('email', 'メールアドレスは必須項目です。');
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角10文字
     * email: 既に登録済み
     *
     * @return void
     */
    public function testEditProfile17()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class, 2)->create();
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => 1]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => 2]);
        $user = User::where('id', 1)->first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => str_random(10),
                            'email' => User::where('id', 2)->first()['email']
                         ]);

        $response->assertSessionHasErrors('email', '既に登録済みのメールアドレスです。他のメールアドレスでご登録ください。');
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角10文字
     * email: 既に登録済み
     *
     * @return void
     */
    public function testEditProfile18()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class, 2)->create();
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => 1]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => 2, 'deleted' => 1]);
        User::where('id', 2)->first()->update(['deleted' => 1]);
        DefaultMailTiming::where('id', 2)->first()->update(['deleted' => 1]);
        DefaultMailTimingMaster::where('id', 2)->first()->update(['deleted' => 1]);
        $user = User::where('id', 1)->first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => str_random(10),
                            'email' => User::withoutGlobalScopes()->where('id', 2)->first()['email']
                         ]);

        $response->assertSessionHasNoErrors();
    }
    
    /**
     * profile_img: 画像ファイル(jpg)
     * name: 半角10文字
     * email: まだ登録なし
     *
     * @return void
     */
    public function testEditProfile19()
    {
        Storage::fake('test');

        factory(DefaultMailTimingMaster::class, 2)->create();
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => 1]);
        factory(DefaultMailTimingSelectMaster::class)->create(['default_mail_timing_id' => 2]);
        User::where('id', 1)->first()->update(['email' => 'aaa@bbb.ccc']);
        User::where('id', 2)->first()->update(['email' => 'xxx@yyy.zzz']);
        $user = User::where('id', 1)->first();

        $response = $this->withSession(['user_id' => $user['id'], 'profile_img' => $user['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('edit_profile', [
                            'profile_img' => UploadedFile::fake()->image('test.webp'),
                            'name'  => str_random(10),
                            'email' => 'sss@ttt.uuu'
                         ]);

        $response->assertSessionHasNoErrors();
    }
}
