<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionMessageController;
use App\Http\Controllers\EvaluationController;


// 登録画面表示
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware(['guest'])
    ->name('register');

// 登録処理
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest']);

// ログイン処理（POST）
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware(['guest'])
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');


// ログアウト（POST）
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

// メール認証
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        $user = $request->user();
        return $user->profile
            ? redirect()->route('items.index', ['tab' => 'mylist'])
            : redirect()->route('profile.create');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->name('verification.send');
});

// プロフィール新規作成（初回登録時のみ使う）
Route::get('/profile/create', [ProfileController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('profile.create');

Route::post('/profile', [ProfileController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('profile.store');

// トップ画面（商品一覧・マイリスト共通）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

// いいね機能（ログイン必須）
Route::post('/item/{item}/like', [LikeController::class, 'store'])
    ->middleware('auth')->name('items.like');
Route::delete('/item/{item}/like', [LikeController::class, 'destroy'])
    ->middleware('auth')->name('items.unlike');

// コメント投稿（ログイン必須）
Route::post('/item/{item}/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

// 商品購入画面
Route::get('/purchase/{item}', [PurchaseController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('purchase.create');

// 支払い方法の一時反映（POSTまたはPUT）
Route::post('/purchase/{item}/payment-method', [PurchaseController::class, 'updatePaymentMethod'])
    ->middleware(['auth', 'verified'])
    ->name('purchase.updatePaymentMethod');

// 購入処理
Route::post('/purchase/{item}', [PurchaseController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('purchase.store');

Route::get('/purchase/{item}/success', [PurchaseController::class, 'success'])
    ->middleware(['auth', 'verified'])
    ->name('purchase.success');

    Route::get('/purchase/{item}/cancel', [PurchaseController::class, 'cancel'])
    ->middleware(['auth', 'verified'])
    ->name('purchase.cancel');



// 住所変更画面
Route::get('/purchase/{item}/address/edit', [AddressController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('address.edit');

// 住所更新処理（セッションに保存）
Route::post('/purchase/{item}/address/update', [AddressController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('address.update');

// マイページ（プロフィール表示）
Route::get('/mypage', [ProfileController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('mypage.show');

// プロフィール編集（登録済みユーザー用）
Route::get('/mypage/edit', [ProfileController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('mypage.edit');

Route::put('/mypage/update', [ProfileController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('mypage.update');

// 出品画面
Route::get('/items/create', [ItemController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('items.create');

// 出品処理
Route::post('/items', [ItemController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('items.store');

// 取引チャット表示
Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.show');

// 取引チャット メッセージ送信
Route::post('/transactions/{transaction}/messages', [TransactionMessageController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.messages.store');

Route::post('/transactions/{transaction}/complete',[TransactionController::class, 'complete'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.complete');

Route::post('/transactions/{transaction}/draft-and-redirect', [TransactionController::class, 'draftAndRedirect'])
    ->name('transactions.draft-and-redirect');

//メッセージ編集
Route::post('/transactions/{transaction}/evaluate',[EvaluationController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.evaluate');

Route::put('/transactions/{transaction}/messages/{message}',[TransactionMessageController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.messages.update');

//メッセージ削除
Route::delete('/transactions/{transaction}/messages/{message}',[TransactionMessageController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.messages.destroy');

