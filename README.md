# laravel-docker-template

〜環境構築〜

⑴　リポジトリ設定
    $ git clone git@github.com:Estra-Coachtech/laravel-docker-template.git
    $ mv laravel-docker-template mock-market

⑵　dockerの設定
    $ docker-compose up -d --build

⑶　laravelパッケージのインストール
    $ docker-compose exec php bash
    $ composer install

⑷　.envファイル作成　（phpコンテナ内）
    $ cp .env.example .env
    
    〜.envの中身変更〜
        DB_CONNECTION=mysql
        DB_HOST=mysql
        DB_PORT=3306
        DB_DATABASE=laravel_db
        DB_USERNAME=laravel_user
        DB_PASSWORD=laravel_pass

⑸　アプリケーション実行用（phpコンテナ内）
　  $ php artisan key:generate

　　※ 使用するurl :  http://localhost/

⑹　テーブル ($ php artisan make:migration create_⚪︎⚪︎_table　で作成済み。)
　　
⑺　マイグレーション実行
    $php artisan migrate

    ※ 使用するurl : http://localhost:8080/

⑻　シーディング実行
    $php artisan db:seed


〜メール認証〜
    MailHog 使用
    ※ 使用するurl :  http://localhost:8025/

〜stripe機能〜
⑴ Stripe アカウント作成
    ① Stripeにサインアップ

    ②「テストモード」で動作確認用の APIキー を取得
        公開用（STRIPE_KEY）
        秘密用（STRIPE_SECRET）
⑵　APIキーを.envに設定

⑶　Stripe PHP ライブラリをインストール

⑷　config/services.php（必要な場合）
        'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        ],

⑸　購入する際(カード選択)
　カード番号　4242 4242 4242 4242












