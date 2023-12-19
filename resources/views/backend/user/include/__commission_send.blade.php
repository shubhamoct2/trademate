<div
    class="tab-pane fade"
    id="pills-commission"
    role="tabpanel"
    aria-labelledby="pills-commission-tab"
>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="site-card">
                <div class="site-card-header">
                    <h4 class="title">{{ __('Commission Wallet') }}</h4>
                </div>
                <div class="site-card-body table-responsive">
                    <form action="{{ route('admin.user.send-commission', $user->id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mx-auto">
                                <div class="site-input-groups">
                                    <label for="send_commission" class="box-input-label">{{ __('Send Commission:') }}</label>
                                    <input id="send_commission" name="commission" type="text" class="box-input" value="" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 mx-auto">
                                <button type="submit"
                                    class="site-btn-sm primary-btn w-100 centered mb-4">{{ __('Send') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('single-script')
    <script>
        (function ($) {
            "use strict";
        })(jQuery);
    </script>
@endpush



