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
     * current_password: $B6u(B
     * new_password: 6$BJ8;z0J>e(B12$BJ8;z0JFb(B
     * new_password_to_check: new_password$B$HF1$8(B
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

        $response->assertSessionHasErrors('current_password', '$B8=:_$N%Q%9%o!<%I$OI,?\9`L\$G$9!#(B');
    }
    
    /**
     * current_password: $B8=:_$N%Q%9%o!<%I$H0[$J$kJ8;zNs(B
     * new_password: 6$BJ8;z0J>e(B12$BJ8;z0JFb(B
     * new_password_to_check: new_password$B$HF1$8(B
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

        $response->assertSessionHasErrors('current_password', '$B8=:_$N%Q%9%o!<%I$,4V0c$C$F$$$^$9!#(B');
    }
    
    /**
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $B6u(B
     * new_password_to_check: 6$BJ8;z0J>e(B12$BJ8;z0JFb(B
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

        $response->assertSessionHasErrors('new_password', '$B?7$7$$%Q%9%o!<%I$OI,?\9`L\$G$9!#(B');
        $response->assertSessionHasErrors('new_password_to_check', '$B?7$7$$%Q%9%o!<%I$GF~NO$7$?$b$N$HF1$8$b$N$r$4F~NO$/$@$5$$!#(B');
    }
    
    /**
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $BH>3Q(B+$BA43Q(B10$BJ8;z(B
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

        $response->assertSessionHasErrors('new_password', '$B?7$7$$%Q%9%o!<%I$OH>3Q1Q?t;z(B6$BJ8;z0J>e(B12$BJ8;z0JFb$G$4EPO?$/$@$5$$!#(B');
    }
    
    /**
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $BH>3Q(B+$B5-9f(B10$BJ8;z(B
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

        $response->assertSessionHasErrors('new_password', '$B?7$7$$%Q%9%o!<%I$OH>3Q1Q?t;z(B6$BJ8;z0J>e(B12$BJ8;z0JFb$G$4EPO?$/$@$5$$!#(B');
    }
    
    /**
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $BH>3Q(B5$BJ8;z(B
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

        $response->assertSessionHasErrors('new_password', '$B?7$7$$%Q%9%o!<%I$OH>3Q1Q?t;z(B6$BJ8;z0J>e(B12$BJ8;z0JFb$G$4EPO?$/$@$5$$!#(B');
    }
    
    /**
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $BH>3Q(B13$BJ8;z(B
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

        $response->assertSessionHasErrors('new_password', '$B?7$7$$%Q%9%o!<%I$OH>3Q1Q?t;z(B6$BJ8;z0J>e(B12$BJ8;z0JFb$G$4EPO?$/$@$5$$!#(B');
    }
    
    /**
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $BH>3Q(B6$BJ8;z(B
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
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $BH>3Q(B10$BJ8;z(B
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
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $BH>3Q(B12$BJ8;z(B
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
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $BH>3Q(B10$BJ8;z(B
     * new_password_to_check: $B6u(B
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
     * current_password: $B8=:_$N%Q%9%o!<%I(B
     * new_password: $BH>3Q(B10$BJ8;z(B
     * new_password_to_check: new_password$B$H0[$J$k(B
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

        $response->assertSessionHasErrors('new_password_to_check', '$B?7$7$$%Q%9%o!<%I$GF~NO$7$?$b$N$HF1$8$b$N$r$4F~NO$/$@$5$$!#(B');
    }
}
