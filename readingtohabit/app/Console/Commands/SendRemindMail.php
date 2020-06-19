<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;
use App\Article;
use App\ArticleMailTiming;
use App\ArticleMailTimingMaster;
use App\ArticleMailTimingSelectMaster;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendArticleMail;
use App\Mail\SuccessRegisterUser;
use App\Mail\ResendMail;

use Carbon\Carbon;

class SendRemindMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_remind_mail:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send remind mails to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // articlesテーブルからdeleted=0のレコードを取得
        $articles = Article::withoutGlobalScopes()
                           ->where('deleted', 0)
                           ->with('user')
                           ->with('article_mail_timing')
                           ->get();
        
        foreach($articles as $article) {
            if (Article::judge_send_remind_mail($article) === 'not_send') {
                continue;
            }

            // それぞれの記録のユーザーへメール送信
            Mail::to($article->user->email)
                ->send(new SendArticleMail($article->user->name, $article->bookname, $article->learning, $article->action));
            
            // それぞれの記録の次回のリマインド日を更新
            Article::update_send_date($article);
        }
    }
}
