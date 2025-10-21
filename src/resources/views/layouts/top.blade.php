<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ config('app.name', 'MyApp') }}</title>
        <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
        <link rel="stylesheet" href="{{ asset('css/top.css') }}">
        @yield('css')
    </head>
    <body>
        {{-- ヘッダー --}}
        <header class="header">
            <div class="header__content">

                {{-- 左：ロゴ --}}
                <div class="header-title">
                    <a class="header__logo" href="{{ route('items.index') }}">
                        <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="header__logo-img">
                    </a>
                </div>

                {{-- 中央：検索フォーム --}}
                <div class="search">
                    <form action="{{ route('items.index') }}" method="GET" >
                        <input class="search__text" type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="なにをお探しですか？">
                    </form>
                </div>

                {{-- 右：ナビゲーション --}}
                <nav class="button">
                    @auth
                        <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="logout__button">
                            ログアウト
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>

                        <a href="{{ route('mypage.show') }}" class="mypage__button">マイページ</a>

                        <a href="{{ route('items.create') }}"
                        class="sell__button">
                            出品
                        </a>
                    @endauth

                    {{-- 未ログイン時の表示 --}}
                    @guest
                        <a href="{{ route('login') }}" class="login__button">
                            ログイン
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                        </form>

                        <a href="{{ route('mypage.show') }}" class="mypage__button">マイページ</a>

                        <a href="{{ route('items.create') }}" class="sell__button">
                            出品
                        </a>
                    @endguest
                </nav>
            </div>
        </header>


        {{-- メインコンテンツ --}}
        <main>
            @yield('content')
        </main>
    </body>
</html>
