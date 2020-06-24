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
        $articles = Article::with('article_mail_timing')
                           ->orderBy('updated_at', 'desc')
                           ->paginate(\PaginationConst::ITEMS_PER_PAGE);
        $articles->setPath(\DocumentRootConst::DOCUMENT_ROOT.'articles');

        return view('article.articles', ['num_of_articles' => $num_of_articles, 'articles' => $articles]);
    }

    public function search_results (Request $request) {

        // 検索結果をページングで表示するためgetメソッドにも対応できるようにしたい
        // そこで、検索条件をセッション変数に格納する
        if ($request->isMethod('post')) {
            Article::search_cond_into_session($request);
            
            return redirect()->secure('search_results');
        }

        return view('article.search_article.results', Article::search_articles($request));
    }

    public function favorites () {
        $num_of_favorites = Article::where('favorite', 1)->count();

        $favorites = Article::with('article_mail_timing')
                           ->where('favorite', 1)
                           ->orderBy('updated_at', 'desc')
                           ->paginate(\PaginationConst::ITEMS_PER_PAGE);
        
        $favorites->setPath(\DocumentRootConst::DOCUMENT_ROOT.'favorites');

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

        return redirect()->secure('articles');
    }

    public function show_article ($article_id, Request $request) {
        if (Article::check_existense_of_article($article_id) === 'not_exists') {
            return view('common.invalid');
        }

        $article = Article::where('id', $article_id)->first();
        $article_mail_timing = ArticleMailTiming::where('article_id', $article_id)->first();

        $book_info = Article::make_show_book_info($article, $article_mail_timing);

        return view('article.show', ['book_info' => $book_info]);
    }

    public function add_favorite($article_id) {
        if (Article::check_existense_of_article($article_id) === 'not_exists') {
            return json_encode(['is_success' => false]);
        }

        $is_success = Article::where('id', $article_id)
                             ->where('favorite', 0)
                             ->first()
                             ->update(['favorite' => 1]);

        if ($is_success === true) {
            return json_encode(['is_success' => true]);
        }
        else {
            return json_encode(['is_success' => false]);
        }
    }
    
    public function delete_favorite($article_id) {
        if (Article::check_existense_of_article($article_id) === 'not_exists') {
            return json_encode(['is_success' => false]);
        }

        $is_success = Article::where('id', $article_id)
                             ->where('favorite', 1)
                             ->first()
                             ->update(['favorite' => 0]);

        if ($is_success === true) {
            return json_encode(['is_success' => true]);
        }
        else {
            return json_encode(['is_success' => false]);
        }
    }
    
    public function edit_article_form ($article_id) {
        if (Article::check_existense_of_article($article_id) === 'not_exists') {
            return view('common.invalid');
        }
        
        $article = Article::where('id', $article_id)->first();

        $edit_info = [
                        'article_info' => Article::make_editforminfo_of_article($article),
                        'mail_info'    => Article::make_editforminfo_of_mail($article),
                     ];

        return view('article.edit_article.form', $edit_info);
    }

    public function edit_article_do ($article_id, ArticleRequest $request) {
        if (Article::check_existense_of_article($article_id) === 'not_exists') {
            return view('common.invalid');
        }

        if (Article::edit_article($article_id, $request) === false) {
            return view('common.fail');
        }

        return redirect()->secure('articles');
    }

    public function delete_article_do ($article_id, Request $request) {
        if (Article::check_existense_of_article($article_id) === 'not_exists') {
            return json_encode(['is_success' => false]);
        }

        if (Article::delete_article($article_id) === false) {
            return json_encode(['is_success' => false]);
        }
        
        return json_encode(['is_success' => true]);
    }
}
