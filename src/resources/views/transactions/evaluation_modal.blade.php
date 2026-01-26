<div class="modal-overlay">
    <div class="modal">
        <div class="modal-title">
            <div class="title-text">取引が完了しました。</div>
        </div>

        <form method="POST" action="{{ route('transactions.evaluate', $transaction) }}">
            @csrf
            <div class="modal-question">
                <div class="question-text">今回の取引相手はどうでしたか？</div>
            </div>
            <div class="stars">
                @for ($i = 5; $i >= 1; $i--)
                    <input
                        type="radio"
                        id="star{{ $i }}"
                        name="rating"
                        value="{{ $i }}"
                        required
                    >
                    <label class="star-mark" for="star{{ $i }}">★</label>
                @endfor
            </div>
            <div class="evaluation-btn">
                <button type="submit" class="submit-btn">送信する</button>
            </div>
        </form>
    </div>
</div>

