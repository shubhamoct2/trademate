<label for="crypto_currency" class="form-label">{{ __('Crypto Currency:') }}</label>
<div class="input-group">
    <select name="crypto_currency" id="crypto_currency_select" class="crypto_currency_select nice-select site-nice-select" style="color: #fff;" required>
        <option selected disabled>--{{ __('Select Currency') }}--</option>
        @foreach($alphapoSetting['currencies'] as $currency)
            <option value="{{ $currency['currency'] }}">{{ $currency['currency'] }}</option>
        @endforeach
    </select>
</div>