<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', '取引画面')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/transaction.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    @yield('css')
</head>
<body>
    <header class="transaction-header">
        <a class="header__logo" href="{{ route('items.index') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="header__logo-img">
        </a>
    </header>

    <div class="transaction-layout">

        {{-- サイドバー --}}
        <aside class="transaction-sidebar">
            <div class="sidebar-title-box">
                <p class="sidebar-title">その他の取引</p>
            </div>
        </aside>

        {{-- メイン --}}
        <main class="transaction-main">
            @yield('content')
        </main>

    </div>
</body>
</html>
