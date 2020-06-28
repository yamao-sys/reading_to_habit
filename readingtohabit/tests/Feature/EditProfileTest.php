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
     * profile_img: $B2hA|%U%!%$%k$G$J$$%U%!%$%k(B
     * name: 1$BJ8;z0J>e(B20$BJ8;z0J2<(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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

        $response->assertSessionHasErrors('profile_img', '$B2hA|%U%!%$%k$r$4A*Br$/$@$5$$!#(B');
    }
    
    /**
     * profile_img: $B2hA|%U%!%$%k$@$,!"(B2MB$BD6$((B
     * name: 1$BJ8;z0J>e(B20$BJ8;z0J2<(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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

        $response->assertSessionHasErrors('profile_img', '2MB$B0JFb$N%5%$%:$N2hA|$r$4A*Br$/$@$5$$!#(B');
    }
    
    /**
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: 1$BJ8;z0J>e(B20$BJ8;z0J2<(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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
     * profile_img: $B2hA|%U%!%$%k(B(png)
     * name: 1$BJ8;z0J>e(B20$BJ8;z0J2<(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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
     * profile_img: $B2hA|%U%!%$%k(B(bmp)
     * name: 1$BJ8;z0J>e(B20$BJ8;z0J2<(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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
     * profile_img: $B2hA|%U%!%$%k(B(gif)
     * name: 1$BJ8;z0J>e(B20$BJ8;z0J2<(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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
     * profile_img: $B2hA|%U%!%$%k(B(svg)
     * name: 1$BJ8;z0J>e(B20$BJ8;z0J2<(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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
     * profile_img: $B2hA|%U%!%$%k(B(webp)
     * name: 1$BJ8;z0J>e(B20$BJ8;z0J2<(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $B6u(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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

        $response->assertSessionHasErrors('name', '$B%f!<%6!<L>$OI,?\9`L\$G$9!#(B');
    }
    
    /**
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B+$BA43Q$G(B10$BJ8;z(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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

        $response->assertSessionHasErrors('name', '$B%f!<%6!<L>$OH>3Q1Q?t;z(B20$BJ8;z0JFb$G$4EPO?$/$@$5$$!#(B');
    }
    
    /**
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B+$B5-9f$G(B10$BJ8;z(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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

        $response->assertSessionHasErrors('name', '$B%f!<%6!<L>$OH>3Q1Q?t;z(B20$BJ8;z0JFb$G$4EPO?$/$@$5$$!#(B');
    }
    
    /**
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B21$BJ8;z(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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

        $response->assertSessionHasErrors('name', '$B%f!<%6!<L>$OH>3Q1Q?t;z(B20$BJ8;z0JFb$G$4EPO?$/$@$5$$!#(B');
    }
    
    /**
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B1$BJ8;z(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B20$BJ8;z(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B10$BJ8;z(B
     * email: $B8=:_$N%a!<%k%"%I%l%9(B
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
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B10$BJ8;z(B
     * email: $B6u(B
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

        $response->assertSessionHasErrors('email', '$B%a!<%k%"%I%l%9$OI,?\9`L\$G$9!#(B');
    }
    
    /**
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B10$BJ8;z(B
     * email: $B4{$KEPO?:Q$_(B
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

        $response->assertSessionHasErrors('email', '$B4{$KEPO?:Q$_$N%a!<%k%"%I%l%9$G$9!#B>$N%a!<%k%"%I%l%9$G$4EPO?$/$@$5$$!#(B');
    }
    
    /**
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B10$BJ8;z(B
     * email: $B4{$KEPO?:Q$_(B
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
     * profile_img: $B2hA|%U%!%$%k(B(jpg)
     * name: $BH>3Q(B10$BJ8;z(B
     * email: $B$^$@EPO?$J$7(B
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
