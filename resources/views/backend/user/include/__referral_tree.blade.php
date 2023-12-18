<div
    class="tab-pane fade"
    id="pills-tree"
    role="tabpanel"
    aria-labelledby="pills-transactions-tab"
>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="site-card">
                <div class="site-card-header">
                    <h4 class="title">{{ __('Referral Tree') }}</h4>
                </div>
                <div class="site-card-body table-responsive">

                    {{-- level referral tree --}}
                    @if(setting('site_referral','global') == 'level' && $user->referrals->count() > 0)
                        <section class="management-hierarchy">
                            <div class="hv-container">
                                <div class="hv-wrapper">
                                    <!-- tree component -->
                                    @include('frontend::referral.include.__tree',['levelUser' => $user,'level' => $level,'depth' => 1, 'me' => true])
                                </div>
                            </div>
                        </section>
                    @else
                        <p>{{ __('No Referral user found') }}</p>
                    @endif


                    <div id="referral_tree" class="referral_tree"></div>                    
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 referral-btn-container">
                            <button 
                                id="save_referral_tree"
                                type="button" 
                                class="site-btn-sm primary-btn w-100 centered save-referral">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('single-script')
    <script>
        (function ($) {
            "use strict";

            var id = <?php echo $user->id ?>;
            var url = '{{ route("admin.user.referral-tree", ":id") }}';
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'POST',
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                success: function(json) {
                    if(Object.keys(json).length > 0) {
                        createJSTree(json);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });

            function createJSTree(jsonData) {
                $('#referral_tree').jstree({
                    "core" : {
                        'data': jsonData,
                        "check_callback" : true,
                        "animation" : 0,
                        "themes" : { "variant" : "large" },
                    },                    
                    "plugins" : [
                        "dnd", "state", "types", "wholerow"
                    ]
                });
            }

            $('#save_referral_tree').on("click", function () {
                var data = $('#referral_tree').data().jstree.get_json();
                
                var url = '{{ route("admin.user.save-referral-tree", ":id") }}';
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'POST',
                    headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                    data: { 'data': data },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush



