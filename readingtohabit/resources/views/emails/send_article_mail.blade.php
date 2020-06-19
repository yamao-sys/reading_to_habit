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
<br>
<div>
----------------------<br>
◆書籍名<br>
&emsp;{{ $bookname }}<br><br>

◆学んだこと<br>
&emsp;{{ $learning }}<br><br>

◆どのように行動に活かすか<br>
&emsp;{{ $action }}<br><br>
----------------------
</div>
<br>
<div>
ログインは下記のURLから<br>
<a href="{{\DocumentRootConst::DOCUMENT_ROOT}}login">{{\DocumentRootConst::DOCUMENT_ROOT}}login</a>
</div>
</body>
</html>
