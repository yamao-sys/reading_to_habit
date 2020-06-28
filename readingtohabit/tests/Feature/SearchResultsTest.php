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

class SearchResultsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * 検索条件のセッション変数への格納
     * bookname: 'test'
     * last_update: '2'
     * mail: [0, 1]
     *
     * @return void
     */
    public function testSearchCondIntoSession1()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => 'test', 'last_update' => '2', 'mail[0]' => '1', 'mail[1]' => '0']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => true,
                    'is_search_for_last_update' => true,
                    'is_search_for_mail' => true,
                    'bookinfo' => 'test',
                    'last_update' => '2',
                    'mail' => ['1', '0']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: 'test'
     * last_update: '2'
     * mail: [0, 1]
     *
     * @return void
     */
    public function testSearchResults1()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 5)->create();
        for ($i = 1; $i <= 5; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(30)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => true,
                        'is_search_for_last_update' => true,
                        'is_search_for_mail' => true,
                        'bookinfo' => 'test',
                        'last_update' => '2',
                        'mail' => ['1', '0']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 5)
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                    'book4'
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: 'test'
     * last_update: '2'
     * mail: [1]
     *
     * @return void
     */
    public function testSearchCondIntoSession2()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => 'test', 'last_update' => '2', 'mail[0]' => '1']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => true,
                    'is_search_for_last_update' => true,
                    'is_search_for_mail' => true,
                    'bookinfo' => 'test',
                    'last_update' => '2',
                    'mail' => ['1']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: 'test'
     * last_update: '2'
     * mail: [0, 1]
     *
     * @return void
     */
    public function testSearchResults2()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 5)->create();
        for ($i = 1; $i <= 5; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(30)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => true,
                        'is_search_for_last_update' => true,
                        'is_search_for_mail' => true,
                        'bookinfo' => 'test',
                        'last_update' => '2',
                        'mail' => ['1']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 4)
                 ->assertDontSee('book5')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book3',
                    'book4'
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: 'test'
     * last_update: '2'
     * mail: [0]
     *
     * @return void
     */
    public function testSearchCondIntoSession3()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => 'test', 'last_update' => '2', 'mail[0]' => '1']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => true,
                    'is_search_for_last_update' => true,
                    'is_search_for_mail' => true,
                    'bookinfo' => 'test',
                    'last_update' => '2',
                    'mail' => ['0']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: 'test'
     * last_update: '2'
     * mail: [0]
     *
     * @return void
     */
    public function testSearchResults3()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 5)->create();
        for ($i = 1; $i <= 5; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => true,
                        'is_search_for_last_update' => true,
                        'is_search_for_mail' => true,
                        'bookinfo' => 'test',
                        'last_update' => '2',
                        'mail' => ['0']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 4)
                 ->assertDontSee('book4')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: 'test'
     * last_update: '2'
     * mail: ''
     *
     * @return void
     */
    public function testSearchCondIntoSession4()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => 'test', 'last_update' => '2']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => true,
                    'is_search_for_last_update' => true,
                    'is_search_for_mail' => false,
                    'bookinfo' => 'test',
                    'last_update' => '2',
                    'mail' => ''
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: 'test'
     * last_update: '2'
     * mail: ''
     *
     * @return void
     */
    public function testSearchResults4()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 5)->create();
        for ($i = 1; $i <= 5; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => true,
                        'is_search_for_last_update' => true,
                        'is_search_for_mail' => false,
                        'bookinfo' => 'test',
                        'last_update' => '2',
                        'mail' => ''
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 4)
                 ->assertDontSee('book4')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: 'test'
     * last_update: '未選択'
     * mail: ['1', '0']
     *
     * @return void
     */
    public function testSearchCondIntoSession5()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => 'test', 'last_update' => '未選択', 'mail[0]' => '1', 'mail[1]' => '0']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => true,
                    'is_search_for_last_update' => false,
                    'is_search_for_mail' => true,
                    'bookinfo' => 'test',
                    'last_update' => 'not_selected',
                    'mail' => ['1', '0']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: 'test'
     * last_update: '未選択'
     * mail: ['1', '0']
     *
     * @return void
     */
    public function testSearchResults5()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 5)->create();
        for ($i = 1; $i <= 5; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => true,
                        'is_search_for_last_update' => false,
                        'is_search_for_mail' => true,
                        'bookinfo' => 'test',
                        'last_update' => 'not_selected',
                        'mail' => ['1', '0']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 5)
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                    'book4'
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: 'test'
     * last_update: '未選択'
     * mail: ['1']
     *
     * @return void
     */
    public function testSearchCondIntoSession6()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => 'test', 'last_update' => '未選択', 'mail[0]' => '1']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => true,
                    'is_search_for_last_update' => false,
                    'is_search_for_mail' => true,
                    'bookinfo' => 'test',
                    'last_update' => 'not_selected',
                    'mail' => ['1']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: 'test'
     * last_update: '未選択'
     * mail: ['1']
     *
     * @return void
     */
    public function testSearchResults6()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 5)->create();
        for ($i = 1; $i <= 5; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => true,
                        'is_search_for_last_update' => false,
                        'is_search_for_mail' => true,
                        'bookinfo' => 'test',
                        'last_update' => 'not_selected',
                        'mail' => ['1']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 4)
                 ->assertDontSee('book5')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book3',
                    'book4'
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: 'test'
     * last_update: '未選択'
     * mail: ['0']
     *
     * @return void
     */
    public function testSearchCondIntoSession7()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => 'test', 'last_update' => '未選択', 'mail[0]' => '0']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => true,
                    'is_search_for_last_update' => false,
                    'is_search_for_mail' => true,
                    'bookinfo' => 'test',
                    'last_update' => 'not_selected',
                    'mail' => ['0']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: 'test'
     * last_update: '未選択'
     * mail: ['0']
     *
     * @return void
     */
    public function testSearchResults7()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 5)->create();
        for ($i = 1; $i <= 5; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => true,
                        'is_search_for_last_update' => false,
                        'is_search_for_mail' => true,
                        'bookinfo' => 'test',
                        'last_update' => 'not_selected',
                        'mail' => ['0']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 3)
                 ->assertDontSee('book4')
                 ->assertDontSee('book3')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5'
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: 'test'
     * last_update: '未選択'
     * mail: ''
     *
     * @return void
     */
    public function testSearchCondIntoSession8()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => 'test', 'last_update' => '未選択']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => true,
                    'is_search_for_last_update' => false,
                    'is_search_for_mail' => false,
                    'bookinfo' => 'test',
                    'last_update' => 'not_selected',
                    'mail' => ''
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: 'test'
     * last_update: '未選択'
     * mail: ''
     *
     * @return void
     */
    public function testSearchResults8()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 5)->create();
        for ($i = 1; $i <= 5; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => true,
                        'is_search_for_last_update' => false,
                        'is_search_for_mail' => false,
                        'bookinfo' => 'test',
                        'last_update' => 'not_selected',
                        'mail' => ''
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 2)
                 ->assertDontSee('book5')
                 ->assertDontSee('book4')
                 ->assertDontSee('book3')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: ''
     * last_update: '2'
     * mail: ['1', '0']
     *
     * @return void
     */
    public function testSearchCondIntoSession9()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => '', 'last_update' => '2', 'mail[0]' => '1', 'mail[1]' => '0']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => false,
                    'is_search_for_last_update' => true,
                    'is_search_for_mail' => true,
                    'bookinfo' => '',
                    'last_update' => '2',
                    'mail' => ['1', '0']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: ''
     * last_update: '2'
     * mail: ['1', '0']
     *
     * @return void
     */
    public function testSearchResults9()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 5)->create();
        for ($i = 1; $i <= 5; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => false,
                        'is_search_for_last_update' => true,
                        'is_search_for_mail' => true,
                        'bookinfo' => '',
                        'last_update' => '2',
                        'mail' => ['1', '0']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 5)
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                    'book4',
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: ''
     * last_update: '2'
     * mail: ['0']
     *
     * @return void
     */
    public function testSearchCondIntoSession11()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => '', 'last_update' => '2', 'mail[0]' => '0']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => false,
                    'is_search_for_last_update' => true,
                    'is_search_for_mail' => true,
                    'bookinfo' => '',
                    'last_update' => '2',
                    'mail' => ['0']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: ''
     * last_update: '2'
     * mail: ['0']
     *
     * @return void
     */
    public function testSearchResults11()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 6)->create();
        for ($i = 1; $i <= 6; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        Article::withoutGlobalScopes()->where('id', 6)->first()->update(['bookname' => 'book6', 'author' => 'book6', 'mail' => 1, 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(80)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => false,
                        'is_search_for_last_update' => true,
                        'is_search_for_mail' => true,
                        'bookinfo' => '',
                        'last_update' => '2',
                        'mail' => ['0']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 4)
                 ->assertDontSee('book6')
                 ->assertDontSee('book4')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: ''
     * last_update: '2'
     * mail: ''
     *
     * @return void
     */
    public function testSearchCondIntoSession12()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => '', 'last_update' => '2']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => false,
                    'is_search_for_last_update' => true,
                    'is_search_for_mail' => false,
                    'bookinfo' => '',
                    'last_update' => '2',
                    'mail' => ''
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: ''
     * last_update: '2'
     * mail: ''
     *
     * @return void
     */
    public function testSearchResults12()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 6)->create();
        for ($i = 1; $i <= 6; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        Article::withoutGlobalScopes()->where('id', 6)->first()->update(['bookname' => 'book6', 'author' => 'book6', 'mail' => 1, 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(80)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => false,
                        'is_search_for_last_update' => true,
                        'is_search_for_mail' => false,
                        'bookinfo' => '',
                        'last_update' => '2',
                        'mail' => ''
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 4)
                 ->assertDontSee('book6')
                 ->assertDontSee('book4')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: ''
     * last_update: '未選択'
     * mail: ['1', '0']
     *
     * @return void
     */
    public function testSearchCondIntoSession13()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => '', 'last_update' => '未選択', 'mail[0]' => '1', 'mail[1]' => '0']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => false,
                    'is_search_for_last_update' => false,
                    'is_search_for_mail' => true,
                    'bookinfo' => '',
                    'last_update' => 'not_selected',
                    'mail' => ['1', '0']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: ''
     * last_update: '未選択'
     * mail: ['1', '0']
     *
     * @return void
     */
    public function testSearchResults13()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 6)->create();
        for ($i = 1; $i <= 6; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        Article::withoutGlobalScopes()->where('id', 6)->first()->update(['bookname' => 'book6', 'author' => 'book6', 'mail' => 1, 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(80)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => false,
                        'is_search_for_last_update' => false,
                        'is_search_for_mail' => true,
                        'bookinfo' => '',
                        'last_update' => 'not_selected',
                        'mail' => ['1', '0']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 6)
                 ->assertDontSee('book6')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                    'book4',
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: ''
     * last_update: '未選択'
     * mail: ['1']
     *
     * @return void
     */
    public function testSearchCondIntoSession14()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => '', 'last_update' => '未選択', 'mail[0]' => '1']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => false,
                    'is_search_for_last_update' => false,
                    'is_search_for_mail' => true,
                    'bookinfo' => '',
                    'last_update' => 'not_selected',
                    'mail' => ['1']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: ''
     * last_update: '未選択'
     * mail: ['1']
     *
     * @return void
     */
    public function testSearchResults14()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 6)->create();
        for ($i = 1; $i <= 6; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        Article::withoutGlobalScopes()->where('id', 6)->first()->update(['bookname' => 'book6', 'author' => 'book6', 'mail' => 1, 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(80)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => false,
                        'is_search_for_last_update' => false,
                        'is_search_for_mail' => true,
                        'bookinfo' => '',
                        'last_update' => 'not_selected',
                        'mail' => ['1']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 5)
                 ->assertDontSee('book5')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book3',
                    'book4',
                    'book6',
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: ''
     * last_update: '未選択'
     * mail: ['0']
     *
     * @return void
     */
    public function testSearchCondIntoSession15()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => '', 'last_update' => '未選択', 'mail[0]' => '0']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => false,
                    'is_search_for_last_update' => false,
                    'is_search_for_mail' => true,
                    'bookinfo' => '',
                    'last_update' => 'not_selected',
                    'mail' => ['0']
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: ''
     * last_update: '未選択'
     * mail: ['0']
     *
     * @return void
     */
    public function testSearchResults15()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 6)->create();
        for ($i = 1; $i <= 6; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        Article::withoutGlobalScopes()->where('id', 6)->first()->update(['bookname' => 'book6', 'author' => 'book6', 'mail' => 1, 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(80)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => false,
                        'is_search_for_last_update' => false,
                        'is_search_for_mail' => true,
                        'bookinfo' => '',
                        'last_update' => 'not_selected',
                        'mail' => ['0']
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 1)
                 ->assertDontSee('book6')
                 ->assertDontSee('book4')
                 ->assertDontSee('book3')
                 ->assertDontSee('book2')
                 ->assertDontSee('test book')
                 ->assertSeeInOrder([
                    'book5',
                 ]);
    }
    
    /**
     * 検索条件のセッション変数への格納
     * bookname: ''
     * last_update: '未選択'
     * mail: ''
     *
     * @return void
     */
    public function testSearchCondIntoSession16()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        $response = $this->withSession(['user_id' => 1, 'profile_img' => User::first()['profile_img'], 'current_date' => Carbon::now('Asia/Tokyo')])
                         ->post('search_results', ['bookname' => '', 'last_update' => '未選択']);

        $response->assertRedirect('https://localhost/search_results')
                 ->withSessionHasAll([
                    'is_search_for_bookinfo' => false,
                    'is_search_for_last_update' => false,
                    'is_search_for_mail' => false,
                    'bookinfo' => '',
                    'last_update' => 'not_selected',
                    'mail' => ''
                 ]);
    }
    
    /**
     * 検索結果の表示
     * bookname: ''
     * last_update: '未選択'
     * mail: ''
     *
     * @return void
     */
    public function testSearchResults16()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 6)->create();
        for ($i = 1; $i <= 6; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        Article::withoutGlobalScopes()->where('id', 6)->first()->update(['bookname' => 'book6', 'author' => 'book6', 'mail' => 1, 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => false,
                        'is_search_for_last_update' => false,
                        'is_search_for_mail' => false,
                        'bookinfo' => '',
                        'last_update' => 'not_selected',
                        'mail' => ''
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 6)
                 ->assertDontSee('book6')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                    'book4',
                 ]);
    }
    
    /**
     * 検索結果の表示(異常系)
     * bookname: ''
     * last_update: '未選択'なのに'is_search_for_last_update': true
     * mail: ''
     *
     * @return void
     */
    public function testSearchResults17()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 6)->create();
        for ($i = 1; $i <= 6; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        Article::withoutGlobalScopes()->where('id', 6)->first()->update(['bookname' => 'book6', 'author' => 'book6', 'mail' => 1, 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => false,
                        'is_search_for_last_update' => true,
                        'is_search_for_mail' => false,
                        'bookinfo' => '',
                        'last_update' => 'not_selected',
                        'mail' => ''
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 6)
                 ->assertDontSee('book6')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                    'book4',
                 ]);
    }
    
    /**
     * 検索結果の表示(異常系)
     * bookname: ''
     * last_update: '未選択'
     * mail: ''なのに'is_search_for_mail': true
     *
     * @return void
     */
    public function testSearchResults18()
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();

        factory(ArticleMailTimingMaster::class, 6)->create();
        for ($i = 1; $i <= 6; $i++) {
            factory(ArticleMailTimingSelectMaster::class)->create(['article_mail_timing_id' => $i]);
        }
        Article::withoutGlobalScopes()->where('id', 1)->first()->update(['bookname' => 'test book', 'author' => 'book1']);
        Article::withoutGlobalScopes()->where('id', 2)->first()->update(['bookname' => 'book2', 'author' => 'test software', 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(5)]);
        Article::withoutGlobalScopes()->where('id', 3)->first()->update(['bookname' => 'book3', 'author' => 'book3', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(45)]);
        Article::withoutGlobalScopes()->where('id', 4)->first()->update(['bookname' => 'book4', 'author' => 'book4', 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        Article::withoutGlobalScopes()->where('id', 5)->first()->update(['bookname' => 'book5', 'author' => 'book5', 'mail' => 0, 'updated_at' => Carbon::now('Asia/Tokyo')->subMinutes(10)]);
        Article::withoutGlobalScopes()->where('id', 6)->first()->update(['bookname' => 'book6', 'author' => 'book6', 'mail' => 1, 'updated_at' => Carbon::now('Asia/Tokyo')->subDays(75)]);
        
        $response = $this->withSession([
                        'user_id' => 1,
                        'profile_img' => User::first()['profile_img'],
                        'current_date' => Carbon::now('Asia/Tokyo'),
                        'is_search_for_bookinfo' => false,
                        'is_search_for_last_update' => false,
                        'is_search_for_mail' => true,
                        'bookinfo' => '',
                        'last_update' => 'not_selected',
                        'mail' => ''
                        ])
                         ->get('search_results');

        $response->assertViewIs('article.search_article.results')
                 ->assertViewHas('num_of_articles', 6)
                 ->assertDontSee('book6')
                 ->assertSeeInOrder([
                    'book1',
                    'book2',
                    'book5',
                    'book3',
                    'book4',
                 ]);
    }
}
