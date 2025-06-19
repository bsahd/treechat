# 概要
ツリー構造をチャットに使用することで話題並列性などを重視した設計にできないかという実験です。
動作確認環境: CGI/FastCGI PHP 8.3.15 on Apache 2.4.62-1
# 依存関係
htmxを使用しています。 `curl https://unpkg.com/htmx.org@^2/dist/htmx.min.js -Lo htmx.js` コマンドをリポジトリ内で実行し、htmx.jsをダウンロードしてください。