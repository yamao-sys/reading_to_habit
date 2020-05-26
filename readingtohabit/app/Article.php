<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class Article extends Model
{
    protected $guarded = array('id');
    
    protected static function boot () {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->where('deleted', 0);
        });
        
        static::addGlobalScope('user_id', function (Builder $builder){
            $builder->where('user_id', session()->get('user_id'));
        });
    }
    
    public function article_mail_timing () {
        return $this->hasOne('App\ArticleMailTiming');
    }

    public function user () {
        return $this->belongsTo('App\User');
    }

    public static function make_book_info (Request $request) {
        if ($request->input('bookimg') === \ImgPathConst::NOIMG_PATH) {
            return  [
                     'bookimg'   => \ImgPathConst::NOIMG_PATH,
                     'bookname'  => $request->input('bookname'),
                     'author'    => $request->input('author'),
                    ];
        }

        return  [
                 'bookimg'   => str_replace('-', '&', $request->input('bookimg')),
                 'bookname'  => $request->input('bookname'),
                 'author'    => $request->input('author'),
                ];
        
    }

    public static function make_default_mail_timing_info (Request $request) {
        $def_timing = DefaultMailTiming::where('user_id', $request->session()->get('user_id'))->first();
        if (empty($def_timing)) {
            return [
                    'by_day'   => '',
                    'by_week'  => '',
                    'by_month' => '',
                    'default_mail_timing_select' => '',
                   ];
        }

        $def_timing_master = DefaultMailTimingMaster::where('default_mail_timing_id', $def_timing['id'])->first();
        $def_timing_select_master = DefaultMailTimingSelectMaster::where('default_mail_timing_id', $def_timing['id'])->first();
        if ($def_timing_select_master['by_day'] === 1) {
            $def_timing_select = 'by_day';
        }
        elseif ($def_timing_select_master['by_week'] === 1) {
            $def_timing_select = 'by_week';
        }
        elseif ($def_timing_select_master['by_month'] === 1) {
            $def_timing_select = 'by_month';
        }
        else {
            $def_timing_select = 'by_day';
        }
        
        return [
                'by_day'   => $def_timing_master['by_day'],
                'by_week'  => $def_timing_master['by_week'],
                'by_month' => $def_timing_master['by_month'],
                'default_mail_timing_select' => $def_timing_select,
               ];
    }

    public static function create_article (ArticleRequest $request) {
        // 保存するデータの準備
        $article_info     = Article::make_article_info($request);
        $mail_timing_info = Article::make_article_mail_timing_info($request);
        $mail_timing_master_info        = Article::make_article_mail_timing_master_info($request);
        $mail_timing_select_master_info = Article::make_article_mail_timing_select_master_info($request);

        // 保存する
        DB::beginTransaction();
        try {
            $article = Article::create($article_info);
            $article_mail_timing = $article->article_mail_timing()->create($mail_timing_info);
            $article_mail_timing->article_mail_timing_master()->create($mail_timing_master_info);
            $article_mail_timing->article_mail_timing_select_master()->create($mail_timing_select_master_info);
        }
        catch (Exception $e) {
            DB::rollback();
            return [];
        }
        DB::commit();
        
        return $article;
    }

    public static function make_article_info (ArticleRequest $request) {
        // bookimgはarticle_id.jpgの形式にするため、保存後に更新する
        return [
                'user_id'  => $request->session()->get('user_id'),
                'bookname' => $request->bookname,
                'author'   => $request->author,
                'learning' => $request->learning,
                'action'   => $request->action,
                'mail'     => intval($request->mail_flag),
               ];
    }

    public static function make_article_mail_timing_info (ArticleRequest $request) {
        if ($request->mail_flag === '1') {
            if ($request->mail_timing_select === 'by_day') {
                return [
                        'last_send_date' => null,
                        'next_send_date' => Carbon::today()->addDays(intval($request->mail_timing_by_day))->toDateString(),
                       ];
            }
            elseif ($request->mail_timing_select === 'by_week') {
                return [
                        'last_send_date' => null,
                        'next_send_date' => Carbon::today()->addWeeks(intval($request->mail_timing_by_week))->toDateString(),
                       ];
            }
            elseif ($request->mail_timing_select === 'by_month') {
                return [
                        'last_send_date' => null,
                        'next_send_date' => Carbon::today()->addMonths(intval($request->mail_timing_by_month))->toDateString(),
                       ];
            }
            else {
                return [
                        'last_send_date' => null,
                        'next_send_date' => null,
                    ];
            }
        }
        else {
            return [
                    'last_send_date' => null,
                    'next_send_date' => null,
                   ];
        }
    }

    public static function make_article_mail_timing_master_info (ArticleRequest $request) {
        return [
                'by_day'   => $request->mail_timing_by_day,
                'by_week'  => $request->mail_timing_by_week,
                'by_month' => $request->mail_timing_by_month,
               ];
    }

    public static function make_article_mail_timing_select_master_info (ArticleRequest $request) {
        if ($request->mail_timing_select === 'by_day') {
            return  [
                     'by_day'   => 1,
                     'by_week'  => 0,
                     'by_month' => 0,
                    ];
        }
        elseif ($request->mail_timing_select === 'by_week') {
            return [
                    'by_day'   => 0,
                    'by_week'  => 1,
                    'by_month' => 0,
                   ];
        }
        elseif ($request->mail_timing_select === 'by_month') {
            return [
                    'by_day'   => 0,
                    'by_week'  => 0,
                    'by_month' => 1,
                   ];
        }
        else {
            return [
                    'by_day'   => 1,
                    'by_week'  => 0,
                    'by_month' => 0,
                   ];
        }
    }

    public static function store_bookimg ($bookimg_url, $article_id) {
        if ($bookimg_url === \ImgPathConst::NOIMG_PATH) {
            try {
                Article::where('id', $article_id)->update(['bookimg' => \ImgPathConst::NOIMG_PATH]);
            }
            catch (Exception $e) {
                return false;
            }
        }
        else {
            $img = file_get_contents($bookimg_url);
            file_put_contents(\ImgPathConst::IMG_ABSOLUTE_PATH.$article_id.'.jpg', $img);

            try {
                Article::where('id', $article_id)->update(['bookimg' => \ImgPathConst::IMG_PATH.$article_id.'.jpg']);
            }
            catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    public static function make_show_book_info ($article, $article_mail_timing) {
        if ($article['mail'] === 1) {
            $book_info = [
                            'id' => $article['id'],
                            'bookimg' => $article['bookimg'],
                            'bookname' => $article['bookname'],
                            'author' => $article['author'],
                            'favorite' => $article['favorite'],
                            'learning' => $article['learning'],
                            'action' => $article['action'],
                            'mail_flag' => '有り',
                            'next_mail_date' => $article_mail_timing['next_send_date'],
                        ];
        }
        else {
            $book_info = [
                            'id' => $article['id'],
                            'bookimg' => $article['bookimg'],
                            'bookname' => $article['bookname'],
                            'author' => $article['author'],
                            'favorite' => $article['favorite'],
                            'learning' => $article['learning'],
                            'action' => $article['action'],
                            'mail_flag' => '無し',
                     ];
        }

        return $book_info;
    }

    public static function make_edit_article_form_info ($article) {
        if (empty($article['id'])) {
            return [];
        }

        return [
                'id' => $article['id'],
                'bookimg' => $article['bookimg'],
                'bookname' => $article['bookname'],
                'author' => $article['author'],
                'learning' => $article['learning'],
                'action' => $article['action'],
                'mail' => $article['mail'],
               ];
    }

    public static function make_edit_article_mail_info ($article) {
        $article_mail_timing = ArticleMailTiming::where('article_id', $article['id'])->first();
        if (empty($article_mail_timing)) {
            return [];
        }

        $article_mail_timing_master = ArticleMailTimingMaster::where('article_mail_timing_id', $article_mail_timing['id'])->first();
        if (empty($article_mail_timing_master)) {
            return [];
        }

        $article_mail_timing_select_master = ArticleMailTimingSelectMaster::where('article_mail_timing_id', $article_mail_timing['id'])->first();
        if (empty($article_mail_timing_select_master)) {
            return [];
        }
        
        if ($article_mail_timing_select_master['by_day'] === 1) {
            $article_mail_timing_select = 'by_day';
        }
        elseif ($article_mail_timing_select_master['by_week'] === 1) {
            $article_mail_timing_select = 'by_week';
        }
        elseif ($article_mail_timing_select_master['by_month'] === 1) {
            $article_mail_timing_select = 'by_month';
        }
        else {
            $article_mail_timing_select = 'by_day';
        }

        return [
                'by_day' => $article_mail_timing_master['by_day'],
                'by_week' => $article_mail_timing_master['by_week'],
                'by_month' => $article_mail_timing_master['by_month'],
                'article_mail_timing_select' => $article_mail_timing_select,
               ];
    }
    
    public static function make_edit_article_info (ArticleRequest $request) {
        return [
                'user_id'  => $request->session()->get('user_id'),
                'learning' => $request->learning,
                'action'   => $request->action,
                'mail'     => intval($request->mail_flag),
               ];
    }
    
    public static function make_edit_article_mail_timing_info (ArticleRequest $request) {
        if ($request->mail_flag === '1') {
            if ($request->mail_timing_select === 'by_day') {
                return [
                        'next_send_date' => Carbon::today()->addDays(intval($request->mail_timing_by_day))->toDateString(),
                       ];
            }
            elseif ($request->mail_timing_select === 'by_week') {
                return [
                        'next_send_date' => Carbon::today()->addWeeks(intval($request->mail_timing_by_week))->toDateString(),
                       ];
            }
            elseif ($request->mail_timing_select === 'by_month') {
                return [
                        'next_send_date' => Carbon::today()->addMonths(intval($request->mail_timing_by_month))->toDateString(),
                       ];
            }
            else {
                return [
                        'next_send_date' => null,
                       ];
            }
        }
        else {
            return [
                    'next_send_date' => null,
                   ];
        }
    }
    
    public static function make_edit_article_mail_timing_master_info (ArticleRequest $request) {
        return [
                'by_day'   => $request->mail_timing_by_day,
                'by_week'  => $request->mail_timing_by_week,
                'by_month' => $request->mail_timing_by_month,
               ];   
    }
    
    public static function make_edit_article_mail_timing_select_master_info (ArticleRequest $request) {
        if ($request->mail_timing_select === 'by_day') {
            return  [
                     'by_day'   => 1,
                     'by_week'  => 0,
                     'by_month' => 0,
                    ];
        }
        elseif ($request->mail_timing_select === 'by_week') {
            return [
                    'by_day'   => 0,
                    'by_week'  => 1,
                    'by_month' => 0,
                   ];
        }
        elseif ($request->mail_timing_select === 'by_month') {
            return [
                    'by_day'   => 0,
                    'by_week'  => 0,
                    'by_month' => 1,
                   ];
        }
        else {
            return [
                    'by_day'   => 1,
                    'by_week'  => 0,
                    'by_month' => 0,
                   ];
        }
    }

    public static function search_cond_into_session(Request $request) {
        $request->session()->put('is_search_for_bookinfo', !empty($request->bookinfo));
        $request->session()->put('bookinfo', $request->bookinfo);

        $request->session()->put('is_search_for_last_update', intval($request->last_update) !== 0);
        $request->session()->put('last_update', $request->last_update);
        
        // mailはチェックボックス(未選択可)のため、isset($request->mail)のみではNG
        if (isset($request->mail)) {
            $request->session()->put('is_search_for_mail', true);
            $request->session()->put('mail', $request->mail);
        }
        else {
            $request->session()->put('is_search_for_mail', false);
            $request->session()->put('mail', '');
        }

    }
    
    public static function search_articles (Request $request) {
        $is_search_for_bookinfo    = $request->session()->get('is_search_for_bookinfo');
        $is_search_for_last_update = $request->session()->get('is_search_for_last_update');
        $is_search_for_mail        = $request->session()->get('is_search_for_mail');
        $bookinfo    = $request->session()->get('bookinfo');
        $last_update = $request->session()->get('last_update');
        $mail        = $request->session()->get('mail');

        // 拡張性に課題あり！：検索条件が1つ増えると、if文の数が2のべき乗だけ増加してしまう
        if ($is_search_for_bookinfo && $is_search_for_last_update && $is_search_for_mail) {
            $num_of_articles =  Article::where('bookname', 'LIKE', "%$bookinfo%")
                                       ->orWhere('author', 'LIKE', "%$bookinfo%")
                                       ->orWhereBetween('updated_at', [Carbon::now()->subMonths($last_update), Carbon::now()])
                                       ->orWhereIn('mail', $mail)
                                       ->count();
            
            $articles =  Article::with('article_mail_timing')
                                ->where('bookname', 'LIKE', "%$bookinfo%")
                                ->orWhere('author', 'LIKE', "%$bookinfo%")
                                ->orWhereBetween('updated_at', [Carbon::now()->subMonths($last_update), Carbon::now()])
                                ->orWhereIn('mail', $mail)
                                ->orderBy('updated_at', 'desc')
                                ->paginate(5);
        }
        elseif ($is_search_for_bookinfo && $is_search_for_last_update && !$is_search_for_mail) {
            $num_of_articles =  Article::where('bookname', 'LIKE', "%$bookinfo%")
                                       ->orWhere('author', 'LIKE', "%$bookinfo%")
                                       ->orWhereBetween('updated_at', [Carbon::now()->subMonths($last_update), Carbon::now()])
                                       ->count();
            
            $articles =  Article::with('article_mail_timing')
                                ->where('bookname', 'LIKE', "%$bookinfo%")
                                ->orWhere('author', 'LIKE', "%$bookinfo%")
                                ->orWhereBetween('updated_at', [Carbon::now()->subMonths($last_update), Carbon::now()])
                                ->orderBy('updated_at', 'desc')
                                ->paginate(5);
        }
        elseif ($is_search_for_bookinfo && !$is_search_for_last_update && $is_search_for_mail) {
            $num_of_articles =  Article::where('bookname', 'LIKE', "%$bookinfo%")
                                       ->orWhere('author', 'LIKE', "%$bookinfo%")
                                       ->orWhereIn('mail', $mail)
                                       ->count();
            
            $articles =  Article::with('article_mail_timing')
                                ->where('bookname', 'LIKE', "%$bookinfo%")
                                ->orWhere('author', 'LIKE', "%$bookinfo%")
                                ->orWhereIn('mail', $mail)
                                ->orderBy('updated_at', 'desc')
                                ->paginate(5);
        }
        elseif ($is_search_for_bookinfo && !$is_search_for_last_update && !$is_search_for_mail) {
            $num_of_articles =  Article::where('bookname', 'LIKE', "%$bookinfo%")
                                       ->orWhere('author', 'LIKE', "%$bookinfo%")
                                       ->count();
            
            $articles =  Article::with('article_mail_timing')
                                ->where('bookname', 'LIKE', "%$bookinfo%")
                                ->orWhere('author', 'LIKE', "%$bookinfo%")
                                ->orderBy('updated_at', 'desc')
                                ->paginate(5);
        }
        elseif (!$is_search_for_bookinfo && $is_search_for_last_update && $is_search_for_mail) {
            $num_of_articles =  Article::whereBetween('updated_at', [Carbon::now()->subMonths($last_update), Carbon::now()])
                                       ->orWhereIn('mail', $mail)
                                       ->count();
            
            $articles =  Article::with('article_mail_timing')
                                ->whereBetween('updated_at', [Carbon::now()->subMonths($last_update), Carbon::now()])
                                ->orWhereIn('mail', $mail)
                                ->orderBy('updated_at', 'desc')
                                ->paginate(5);
        }
        elseif (!$is_search_for_bookinfo && $is_search_for_last_update && !$is_search_for_mail) {
            $num_of_articles =  Article::whereBetween('updated_at', [Carbon::now()->subMonths($last_update), Carbon::now()])
                                       ->count();

            $articles =  Article::with('article_mail_timing')
                                ->whereBetween('updated_at', [Carbon::now()->subMonths($last_update), Carbon::now()])
                                ->orderBy('updated_at', 'desc')
                                ->paginate(5);
        }
        elseif (!$is_search_for_bookinfo && !$is_search_for_last_update && $is_search_for_mail) {
            $num_of_articles =  Article::whereIn('mail', $mail)->count();
            
            $articles =  Article::with('article_mail_timing')
                                ->whereIn('mail', $mail)
                                ->orderBy('updated_at', 'desc')
                                ->paginate(5);
        }
        elseif (!$is_search_for_bookinfo && !$is_search_for_last_update && !$is_search_for_mail) {
            $num_of_articles =  Article::count();
            
            $articles =  Article::with('article_mail_timing')
                                ->orderBy('updated_at', 'desc')
                                ->paginate(5);
        }

        return ['num_of_articles' => $num_of_articles, 'articles' => $articles];
    }
}
