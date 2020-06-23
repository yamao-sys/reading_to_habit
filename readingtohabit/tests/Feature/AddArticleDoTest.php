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

class AddArticleDoTest extends TestCase
{
    use DatabaseMigrations;
    
    /**
     * add_article_doのバリデーションテスト
     *
     * @dataProvider dataproviderValidationError
     *
     * @return void
     */
    public function testAddArticleValidation($data, $error_msg)
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $response = $this->withSession(['user_id' => User::first()['id']])
                         ->post('add_article_do', $data);
        
        if (empty($error_msg['learning']) && !empty($error_msg['action'])) {
            $response->assertRedirect('')
                     ->assertSessionHasErrors([
                        'action' => $error_msg['action'],
                    ]);
        }
        elseif (!empty($error_msg['learning']) && empty($error_msg['action'])) {
            $response->assertRedirect('')
                     ->assertSessionHasErrors([
                        'learning' => $error_msg['learning'],
                    ]);
        }
        elseif (!empty($error_msg['learning']) && !empty($error_msg['action'])) {
            $response->assertRedirect('')
                     ->assertSessionHasErrors([
                        'learning' => $error_msg['learning'],
                        'action'   => $error_msg['action'],
                    ]);
        }
    }
    
    public function dataproviderValidationError () {
        $error = [
                    'learning.required' => '学んだことは必須項目です。',
                    'learning.max'      => '学んだことは21845文字以内でご入力ください。',
                    'action.required'   => '学びをどのように行動に活かすかは必須項目です。',
                    'action.max'        => '学びをどのように行動に活かすかは21845文字以内でご入力ください。',
                 ];

        $data['test1'] = [
                            'learning' => '',
                            'action'   => '',
                         ];
        $error_msg['test1'] = [
                                'learning' => $error['learning.required'],
                                'action'   => $error['action.required'],
                              ];

        $data['test2'] = [
                            'learning' => '',
                            'action'   => $this->makeAlphaStringIncludingKana(1, 21845),
                         ];
        $error_msg['test2'] = [
                                'learning' => $error['learning.required'],
                                'action'   => '',
                              ];

        $data['test3'] = [
                            'learning' => $this->makeAlphaStringIncludingKana(1, 21845),
                            'action'   => '',
                         ];
        $error_msg['test3'] = [
                                'learning' => '',
                                'action'   => $error['action.required'],
                              ];

        $data['test4'] = [
                            'learning' => $this->makeAlphaStringIncludingKana(1, 21846),
                            'action'   => $this->makeAlphaStringIncludingKana(1, 21845),
                         ];
        $error_msg['test4'] = [
                                'learning' => $error['learning.max'],
                                'action'   => '',
                              ];
        
        $data['test5'] = [
                            'learning' => $this->makeAlphaStringIncludingKana(1, 21845),
                            'action'   => $this->makeAlphaStringIncludingKana(1, 21846),
                         ];
        $error_msg['test5'] = [
                                'learning' => '',
                                'action'   => $error['action.max'],
                              ];
        
        $data['test6'] = [
                            'learning' => $this->makeAlphaStringIncludingKana(1, 21846),
                            'action'   => $this->makeAlphaStringIncludingKana(1, 21846),
                         ];
        $error_msg['test6'] = [
                                'learning' => $error['learning.max'],
                                'action'   => $error['action.max'],
                              ];
        $keys = ['test1', 'test2', 'test3', 'test4', 'test5', 'test6',];

        foreach($keys as $key) {
            $test_data[] = [$data[$key], $error_msg[$key]];
        }

        return $test_data;
    }
    
    /**
     * add_article_doのテスト
     *
     * @dataProvider dataproviderValid
     *
     * @return void
     */
    public function testAddArticleDo($data, $inserted)
    {
        factory(DefaultMailTimingMaster::class)->create();
        factory(DefaultMailTimingSelectMaster::class)->create();
        
        $response = $this->withoutExceptionHandling()
                         ->withSession(['user_id' => User::first()['id']])
                         ->post('add_article_do', $data);

        $this->assertDatabaseHas('articles', $inserted['articles']);
        $this->assertDatabaseHas('article_mail_timings', $inserted['article_mail_timings']);
        $this->assertDatabaseHas('article_mail_timing_masters', $inserted['article_mail_timing_masters']);
        $this->assertDatabaseHas('article_mail_timing_select_masters', $inserted['article_mail_timing_select_masters']);

        $this->assertDatabaseHas('articles', ['bookimg' => 'img/'.Article::first()['id'].'.jpg']);
        // $response->assertRedirect('articles');
    }
    
    public function dataproviderValid () {
        $bookimg  = 'https://www.photolibrary.jp/mhd3/img675/450-20190305105900243712.jpg';
        $bookname = 'Readingtohabit開発';
        $author   = '山内敬太';
        $learning = '学んだこと';
        $action   = 'どのように行動に活かすか';
        $mail_on  = '1';
        $mail_off = '0';
        $by_day   = 'by_day';
        $by_week  = 'by_week';
        $by_month = 'by_month';
        $mail_timing_by_day   = '3';
        $mail_timing_by_week  = '1';
        $mail_timing_by_month = '1';
        $next_send_date_by_day   = Carbon::today()->addDays(intval($mail_timing_by_day))->toDateString();
        $next_send_date_by_week  = Carbon::today()->addWeeks(intval($mail_timing_by_week))->toDateString();
        $next_send_date_by_month = Carbon::today()->addMonths(intval($mail_timing_by_month))->toDateString();
        
        $data['test1'] = [
                            'bookimg'   => $bookimg,
                            'bookname'  => $bookname,
                            'author'    => $author,
                            'learning'  => $learning,
                            'action'    => $action,
                            'mail_flag' => $mail_off,
                            'mail_timing_select'   => $by_day,
                            'mail_timing_by_day'   => $mail_timing_by_day,
                            'mail_timing_by_week'  => $mail_timing_by_week,
                            'mail_timing_by_month' => $mail_timing_by_month,
                         ];
        $inserted['test1'] = [
                                'articles' => [
                                                'bookname' => $bookname,
                                                'author'   => $author,
                                                'learning' => $learning,
                                                'action'   => $action,
                                                'mail'     => intval($mail_off),
                                              ],

                                'article_mail_timings' => [
                                                            'next_send_date' => null,
                                                          ],

                                'article_mail_timing_masters' => [
                                                                    'by_day'   => intval($mail_timing_by_day),
                                                                    'by_week'  => intval($mail_timing_by_week),
                                                                    'by_month' => intval($mail_timing_by_month),
                                                                 ],

                                'article_mail_timing_select_masters' => [
                                                                         'by_day'   => intval($mail_on),
                                                                         'by_week'  => intval($mail_off),
                                                                         'by_month' => intval($mail_off),
                                                                        ],
                             ];
        
        $data['test2'] = [
                            'bookimg'   => $bookimg,
                            'bookname'  => $bookname,
                            'author'    => $author,
                            'learning'  => $learning,
                            'action'    => $action,
                            'mail_flag' => $mail_off,
                            'mail_timing_select'   => $by_week,
                            'mail_timing_by_day'   => $mail_timing_by_day,
                            'mail_timing_by_week'  => $mail_timing_by_week,
                            'mail_timing_by_month' => $mail_timing_by_month,
                         ];
        $inserted['test2'] = [
                                'articles' => [
                                                'bookname' => $bookname,
                                                'author'   => $author,
                                                'learning' => $learning,
                                                'action'   => $action,
                                                'mail'     => intval($mail_off),
                                              ],

                                'article_mail_timings' => [
                                                            'next_send_date' => null,
                                                          ],

                                'article_mail_timing_masters' => [
                                                                    'by_day'   => intval($mail_timing_by_day),
                                                                    'by_week'  => intval($mail_timing_by_week),
                                                                    'by_month' => intval($mail_timing_by_month),
                                                                 ],

                                'article_mail_timing_select_masters' => [
                                                                         'by_day'   => intval($mail_off),
                                                                         'by_week'  => intval($mail_on),
                                                                         'by_month' => intval($mail_off),
                                                                        ],
                             ];
        
        $data['test3'] = [
                            'bookimg'   => $bookimg,
                            'bookname'  => $bookname,
                            'author'    => $author,
                            'learning'  => $learning,
                            'action'    => $action,
                            'mail_flag' => $mail_off,
                            'mail_timing_select'   => $by_month,
                            'mail_timing_by_day'   => $mail_timing_by_day,
                            'mail_timing_by_week'  => $mail_timing_by_week,
                            'mail_timing_by_month' => $mail_timing_by_month,
                         ];
        $inserted['test3'] = [
                                'articles' => [
                                                'bookname' => $bookname,
                                                'author'   => $author,
                                                'learning' => $learning,
                                                'action'   => $action,
                                                'mail'     => intval($mail_off),
                                              ],

                                'article_mail_timings' => [
                                                            'next_send_date' => null,
                                                          ],

                                'article_mail_timing_masters' => [
                                                                    'by_day'   => intval($mail_timing_by_day),
                                                                    'by_week'  => intval($mail_timing_by_week),
                                                                    'by_month' => intval($mail_timing_by_month),
                                                                 ],

                                'article_mail_timing_select_masters' => [
                                                                         'by_day'   => intval($mail_off),
                                                                         'by_week'  => intval($mail_off),
                                                                         'by_month' => intval($mail_on),
                                                                        ],
                             ];
        
        $data['test4'] = [
                            'bookimg'   => $bookimg,
                            'bookname'  => $bookname,
                            'author'    => $author,
                            'learning'  => $learning,
                            'action'    => $action,
                            'mail_flag' => $mail_on,
                            'mail_timing_select'   => $by_day,
                            'mail_timing_by_day'   => $mail_timing_by_day,
                            'mail_timing_by_week'  => $mail_timing_by_week,
                            'mail_timing_by_month' => $mail_timing_by_month,
                         ];
        $inserted['test4'] = [
                                'articles' => [
                                                'bookname' => $bookname,
                                                'author'   => $author,
                                                'learning' => $learning,
                                                'action'   => $action,
                                                'mail'     => intval($mail_on),
                                              ],

                                'article_mail_timings' => [
                                                            'next_send_date' => $next_send_date_by_day,
                                                          ],

                                'article_mail_timing_masters' => [
                                                                    'by_day'   => intval($mail_timing_by_day),
                                                                    'by_week'  => intval($mail_timing_by_week),
                                                                    'by_month' => intval($mail_timing_by_month),
                                                                 ],

                                'article_mail_timing_select_masters' => [
                                                                         'by_day'   => intval($mail_on),
                                                                         'by_week'  => intval($mail_off),
                                                                         'by_month' => intval($mail_off),
                                                                        ],
                             ];
        
        $data['test5'] = [
                            'bookimg'   => $bookimg,
                            'bookname'  => $bookname,
                            'author'    => $author,
                            'learning'  => $learning,
                            'action'    => $action,
                            'mail_flag' => $mail_on,
                            'mail_timing_select'   => $by_week,
                            'mail_timing_by_day'   => $mail_timing_by_day,
                            'mail_timing_by_week'  => $mail_timing_by_week,
                            'mail_timing_by_month' => $mail_timing_by_month,
                         ];
        $inserted['test5'] = [
                                'articles' => [
                                                'bookname' => $bookname,
                                                'author'   => $author,
                                                'learning' => $learning,
                                                'action'   => $action,
                                                'mail'     => intval($mail_on),
                                              ],

                                'article_mail_timings' => [
                                                            'next_send_date' => $next_send_date_by_week,
                                                          ],

                                'article_mail_timing_masters' => [
                                                                    'by_day'   => intval($mail_timing_by_day),
                                                                    'by_week'  => intval($mail_timing_by_week),
                                                                    'by_month' => intval($mail_timing_by_month),
                                                                 ],

                                'article_mail_timing_select_masters' => [
                                                                         'by_day'   => intval($mail_off),
                                                                         'by_week'  => intval($mail_on),
                                                                         'by_month' => intval($mail_off),
                                                                        ],
                             ];
        
        $data['test6'] = [
                            'bookimg'   => $bookimg,
                            'bookname'  => $bookname,
                            'author'    => $author,
                            'learning'  => $learning,
                            'action'    => $action,
                            'mail_flag' => $mail_on,
                            'mail_timing_select'   => $by_month,
                            'mail_timing_by_day'   => $mail_timing_by_day,
                            'mail_timing_by_week'  => $mail_timing_by_week,
                            'mail_timing_by_month' => $mail_timing_by_month,
                         ];
        $inserted['test6'] = [
                                'articles' => [
                                                'bookname' => $bookname,
                                                'author'   => $author,
                                                'learning' => $learning,
                                                'action'   => $action,
                                                'mail'     => intval($mail_on),
                                              ],

                                'article_mail_timings' => [
                                                            'next_send_date' => $next_send_date_by_month,
                                                          ],

                                'article_mail_timing_masters' => [
                                                                    'by_day'   => intval($mail_timing_by_day),
                                                                    'by_week'  => intval($mail_timing_by_week),
                                                                    'by_month' => intval($mail_timing_by_month),
                                                                 ],

                                'article_mail_timing_select_masters' => [
                                                                         'by_day'   => intval($mail_off),
                                                                         'by_week'  => intval($mail_off),
                                                                         'by_month' => intval($mail_on),
                                                                        ],
                             ];

        $keys = ['test1', 'test2', 'test3', 'test4', 'test5', 'test6',];

        foreach($keys as $key) {
            $test_data[] = [$data[$key], $inserted[$key]];
        }

        return $test_data;
    }
}
