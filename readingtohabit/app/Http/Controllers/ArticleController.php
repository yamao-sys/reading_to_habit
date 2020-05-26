<?php

namespace App\Http\Controllers;

use App\User;
use App\Article;
use App\DefaultMailTiming;
use App\DefaultMailTimingMaster;
use App\DefaultMailTimingSelectMaster;
use App\ArticleMailTiming;
use App\ArticleMailTimingMaster;
use App\ArticleMailTimingSelectMaster;

use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArticleController extends Controller
{
    public function articles () {
        $num_of_articles = Article::count();
        $articles = Article::with('article_mail_timing')->orderBy('updated_at', 'desc')->paginate(\PaginationConst::ITEMS_PER_PAGE);
        return view('article.articles', ['num_of_articles' => $num_of_articles, 'articles' => $articles]);
    }

    public function search_results (Request $request) {

        // 検索結果をページングで表示するためgetメソッドにも対応できるようにしたい
        // そこで、検索条件をセッション変数に格納する
        if ($request->isMethod('post')) {
            Article::search_cond_into_session($request);
        }

        return view('article.search_article.results', Article::search_articles($request));
    }

    public function favorites () {
        $num_of_favorites = Article::where('favorite', 1)->count();

        $favorites = Article::with('article_mail_timing')
                           ->where('favorite', 1)
                           ->orderBy('updated_at', 'desc')
                           ->paginate(\PaginationConst::ITEMS_PER_PAGE);

        return view('article.favorites', ['num_of_favorites' => $num_of_favorites, 'favorites' => $favorites]);
    }

    public function add_article_form (Request $request) {
        if (empty($request->input('bookimg')) || empty($request->input('bookname')) || empty($request->input('author'))) {
            return view('common.invalid');
        }

        $book_info    = Article::make_book_info($request);
        $default_mail_timing_info = Article::make_default_mail_timing_info($request);

        if (empty($default_mail_timing_info['by_day'])) {
            return view('common.invalid');
        }
        
        return view('article.add_article.form', ['book_info' => $book_info, 'default_mail_timing_info' => $default_mail_timing_info]);
    }

    public function add_article_do (ArticleRequest $request) {
        $created_article = Article::create_article($request);
        if (empty($created_article)) {
            return view('common.fail');
        }

        if (Article::store_bookimg($request->bookimg, $created_article['id']) === false) {
            return view('common.fail');
        }

        return redirect('articles');
    }

    public function show_article ($article_id, Request $request) {
        $article = Article::where('id', $article_id)->first();
        if (empty($article)) {
            return view('common.invalid');
        }

        $article_mail_timing = ArticleMailTiming::where('article_id', $article['id'])->first();
        if (empty($article_mail_timing)) {
            return view('common.invalid');
        }

        $book_info = Article::make_show_book_info($article, $article_mail_timing);

        return view('article.show', ['book_info' => $book_info]);
    }

    public function add_favorite($article_id) {
        // 投稿IDに相当する投稿の存在確認をする
        if (empty(Article::where('id', $article_id)->where('favorite', 0)->first())) {
            return json_encode(['is_success' => false]);
        }

        // 投稿IDに相当する投稿のお気に入りフラグを0→1にする
        $is_success = Article::where('id', $article_id)
                             ->where('favorite', 0)
                             ->first()
                             ->update(['favorite' => 1]);

        // 成功したら、is_success: trueのjsonデータを返す
        if ($is_success === true) {
            return json_encode(['is_success' => true]);
        }
        else {
            return json_encode(['is_success' => false]);
        }
    }
    
    public function delete_favorite($article_id) {
        // 投稿IDに相当する投稿の存在確認をする
        if (empty(Article::where('id', $article_id)->where('favorite', 1)->first())) {
            return json_encode(['is_success' => false]);
        }

        // 投稿IDに相当する投稿のお気に入りフラグを1→0にする
        $is_success = Article::where('id', $article_id)
                             ->where('favorite', 1)
                             ->first()
                             ->update(['favorite' => 0]);

        // 成功したら、is_success: trueのjsonデータを返す
        if ($is_success === true) {
            return json_encode(['is_success' => true]);
        }
        else {
            return json_encode(['is_success' => false]);
        }
    }
    
    public function edit_article_form ($article_id, Request $request) {
        $article = Article::where('id', $article_id)
                          ->where('user_id', $request->session()->get('user_id'))
                          ->first();

        if (empty($article)) {
            return view('common.invalid');
        }
        
        $edit_article_info = Article::make_edit_article_form_info($article);
        if (empty($edit_article_info['bookname'])) {
            return view('common.invalid');
        }

        $edit_article_mail_info = Article::make_edit_article_mail_info($article);
        if (empty($edit_article_mail_info)) {
            return view('common.invalid');
        }

        $edit_info = [
                        'edit_article_info' => $edit_article_info,
                        'edit_article_mail_info' => $edit_article_mail_info,
                     ];

        return view('article.edit_article.form', $edit_info);
    }

    public function edit_article_do ($article_id, ArticleRequest $request) {
        $article = Article::where('id', $article_id)
                          ->where('user_id', $request->session()->get('user_id'))
                          ->first();
        if (empty($article)) {
            return view('common.invalid');
        }

        $article_mail_timing = ArticleMailTiming::where('article_id', $article_id)->first();
        if (empty($article_mail_timing)) {
            return view('common.invalid');
        }

        $article_mail_timing_master = ArticleMailTimingMaster::where('article_mail_timing_id', $article_mail_timing['id'])->first();
        if (empty($article_mail_timing_master)) {
            return view('common.invalid');
        }
        
        $article_mail_timing_select_master = ArticleMailTimingSelectMaster::where('article_mail_timing_id', $article_mail_timing['id'])->first();
        if (empty($article_mail_timing_select_master)) {
            return view('common.invalid');
        }

        // 保存するデータの準備
        $edit_article_info = Article::make_edit_article_info($request);

        $edit_article_mail_timing_info = Article::make_edit_article_mail_timing_info($request);
        
        $edit_article_mail_timing_master_info = Article::make_edit_article_mail_timing_master_info($request);
        
        $edit_article_mail_timing_select_master_info = Article::make_edit_article_mail_timing_select_master_info($request);

        // 保存する
        DB::beginTransaction();

        try {
            Article::where('id', $article_id)->update($edit_article_info);
            ArticleMailTiming::where('id', $article_mail_timing['id'])->update($edit_article_mail_timing_info);
            ArticleMailTimingMaster::where('article_mail_timing_id', $article_mail_timing['id'])->update($edit_article_mail_timing_master_info);
            ArticleMailTimingSelectMaster::where('article_mail_timing_id', $article_mail_timing['id'])->update($edit_article_mail_timing_select_master_info);
        }
        catch (Exception $e) {
            DB::rollback();
            return back()->withInput();
        }

        DB::commit();

        return redirect('articles');
    }

    public function delete_article_do ($article_id, Request $request) {
        $article = Article::where('id', $article_id)
                          ->where('user_id', $request->session()->get('user_id'))
                          ->first();

        if (empty($article)) {
            return json_encode(['is_success' => false]);
        }
        
        // 投稿IDに相当する投稿関連の削除フラグを1→0にする
        $is_success = Article::where('id', $article_id)
                             ->first()
                             ->update(['deleted' => 1, 'deleted_at' => Carbon::now()]);
        if ($is_success === false) {
            return json_encode(['is_success' => false]);
        }

        $article_mail_timing = ArticleMailTiming::where('article_id', $article_id)->first();
        if (empty($article_mail_timing)) {
            return json_encode(['is_success' => false]);
        }

        $is_success = ArticleMailTiming::where('id', $article_mail_timing['id'])
                                       ->first()
                                       ->update(['deleted' => 1, 'deleted_at' => Carbon::now()]);
        if ($is_success === false) {
            return json_encode(['is_success' => false]);
        }
        
        $is_success = ArticleMailTimingMaster::where('article_mail_timing_id', $article_mail_timing['id'])
                                       ->first()
                                       ->update(['deleted' => 1, 'deleted_at' => Carbon::now()]);
        if ($is_success === false) {
            return json_encode(['is_success' => false]);
        }
        
        $is_success = ArticleMailTimingSelectMaster::where('article_mail_timing_id', $article_mail_timing['id'])
                                                   ->first()
                                                   ->update(['deleted' => 1, 'deleted_at' => Carbon::now()]);
        if ($is_success === false) {
            return json_encode(['is_success' => false]);
        }

        // 成功したら、is_success: trueのjsonデータを返す
        return json_encode(['is_success' => true]);
    }
}
