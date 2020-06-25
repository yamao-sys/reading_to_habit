<!DOCTYPE html>
<html lang="ja">
<body>
<div>
{{ $name }}さん<br>
おはようございます。Readingtohabit事務局です。
</div>
<br>
<div>
読書記録のリマインドメールをお送りいたします。<br>
読書で学んだことが自身の行動に活かされているかをチェックしましょう!
</div>
<pre>----------------------
◆書籍名
{{ $bookname }}

◆学んだこと
{{ $learning }}

◆どのように行動に活かすか
{{ $action }}
----------------------</pre>
<div>
ログインは下記のURLから<br>
<a href="{{\DocumentRootConst::DOCUMENT_ROOT}}login">{{\DocumentRootConst::DOCUMENT_ROOT}}login</a>
</div>
</body>
</html>
