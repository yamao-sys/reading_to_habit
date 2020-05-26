<?php

namespace Tests\Feature;

use App\User;
use App\DefaultMailTiming;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\ArticleRequest;

use Illuminate\Http\Response;
// use Illuminate\Support\Collection;

class AddArticleFormTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * add_article_formテスト
     * クエリパラメータ(bookimg) クエリパラメータ(bookname) クエリパラメータ(author) ユーザーに相当するdefault_mail_timingsの存在
     * N                         Y                          Y                        -
     * 
     * @return void
     */
    public function testAddArticleForm1()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $response = $this->withSession(['user_id' => User::first()['id']])
                         ->get('add_article_form?bookname=Readingtohabit開発&author=山内敬太');

        $response->assertViewIs('common.invalid');
    }
    
    /**
     * add_article_formテスト
     * クエリパラメータ(bookimg) クエリパラメータ(bookname) クエリパラメータ(author) ユーザーに相当するdefault_mail_timingsの存在
     * Y                         N                          Y                        -
     * 
     * @return void
     */
    public function testAddArticleForm2()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $response = $this->withSession(['user_id' => User::first()['id']])
                         ->get('add_article_form?bookimg=/img/noimage.png&author=山内敬太');

        $response->assertViewIs('common.invalid');
    }
    
    /**
     * add_article_formテスト
     * クエリパラメータ(bookimg) クエリパラメータ(bookname) クエリパラメータ(author) ユーザーに相当するdefault_mail_timingsの存在
     * Y                         Y                          N                        -
     * 
     * @return void
     */
    public function testAddArticleForm3()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $response = $this->withSession(['user_id' => User::first()['id']])
                         ->get('add_article_form?bookimg=/img/noimage.png&bookname=Readingtohabit開発');

        $response->assertViewIs('common.invalid');
    }
    
    /*
     * add_article_formテスト
     * クエリパラメータ(bookimg) クエリパラメータ(bookname) クエリパラメータ(author) ユーザーに相当するdefault_mail_timingsの存在
     * Y                         Y                          Y                        N
     * 
     * @return void
     */
    public function testAddArticleForm4()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        DefaultMailTiming::where('user_id', User::first()['id'])->update(['deleted' => 1]);

        $response = $this->withoutExceptionHandling()
                         ->withSession(['user_id' => User::first()['id']])
                         ->get('add_article_form?bookimg='.\ImgPathConst::NOIMG_PATH.'&bookname=Readingtohabit開発&author=山内敬太');

        $response->assertViewIs('common.invalid');
    }
    
    /**
     * add_article_formテスト
     * クエリパラメータ(bookimg) クエリパラメータ(bookname) クエリパラメータ(author) ユーザーに相当するdefault_mail_timingsの存在
     * Y                         Y                          Y                        Y
     * 
     * @return void
     */
    public function testAddArticleForm5()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $response = $this->withoutExceptionHandling()
                         ->withSession(['user_id' => User::first()['id']])
                         ->get('add_article_form?bookimg='.\ImgPathConst::NOIMG_PATH.'&bookname=Readingtohabit開発&author=山内敬太');

        $book_info    = [
                         'bookimg'   => \ImgPathConst::NOIMG_PATH,
                         'bookname'  => 'Readingtohabit開発',
                         'author'    => '山内敬太',
                        ];

        $response->assertViewIs('article.add_article.form')
                 ->assertViewHasAll(['book_info' => $book_info, 'default_data' => $this->make_default_data(),]);
    }

    public function make_default_data() {
        $default_mail_timing_master = DefaultMailTimingMaster::first();

        if (DefaultMailTimingSelectMaster::first()['by_day'] === 1) {
            return [
                    'by_day'   => $default_mail_timing_master['by_day'],
                    'by_week'  => $default_mail_timing_master['by_week'],
                    'by_month' => $default_mail_timing_master['by_month'],
                    'default_mail_timing_select' => 'by_day',
                   ];
        }
        elseif (DefaultMailTimingSelectMaster::first()['by_week'] === 1) {
            return [
                    'by_day'   => $default_mail_timing_master['by_day'],
                    'by_week'  => $default_mail_timing_master['by_week'],
                    'by_month' => $default_mail_timing_master['by_month'],
                    'default_mail_timing_select' => 'by_week',
                   ];
        }
        elseif (DefaultMailTimingSelectMaster::first()['by_month'] === 1) {
            return [
                    'by_day'   => $default_mail_timing_master['by_day'],
                    'by_week'  => $default_mail_timing_master['by_week'],
                    'by_month' => $default_mail_timing_master['by_month'],
                    'default_mail_timing_select' => 'by_month',
                   ];
        }
    }
}
