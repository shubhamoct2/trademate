<div class="modal fade" id="joinTelegramModal" tabindex="-1" aria-labelledby="joinTelegramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content site-table-modal">
            <div class="modal-body popup-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="popup-body-text">
                    <h3 class="title mb-2 text-dark"> {{ __('Telegram Channel') }}</h3>
                </div>
                <div class="popup-body-text">
                    <p class="mb-0 text-secondary"> {{ __('Join our telegram group for updates & more.') }}</p>
                </div>
                <div class="popup-body-text">
                    <a target="_blank" href="https://t.me/TradeMate_official" class="site-btn-sm grad-btn d-flex p-2 w-100 justify-content-center align-items-sm-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="send" icon-name="send" class="lucide lucide-send">
                            <line x1="22" x2="11" y1="2" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        <span>{{ __('Join TradeMate') }} </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modal Popup */
    .popup-body .btn-close {
        position: absolute;
        right: 15px;
    }

    .popup-body .popup-body-text {
        padding: 10px;
    }
    .popup-body .popup-body-text .title {
        font-size: 20px;
        margin-bottom: 20px;
    }
</style>