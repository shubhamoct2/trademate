<div
    class="tab-pane fade"
    id="pills-wallet"
    role="tabpanel"
    aria-labelledby="pills-wallet-tab"
>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="site-card">
                <div class="site-card-header">
                    <h4 class="title">{{ __('Wallet') }}</h4>
                </div>
                <div class="site-card-body table-responsive">     
                    <div class="site-datatable">               
                        <table id="user-wallet-dataTable" class="display data-table">
                            <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Currency') }}</th>
                                <th>{{ __('Address') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
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

            $('#user-wallet-dataTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: "{{ route('admin.user.wallet_list', $user->id) }}",
                columns: [
                    {
                        data: 'updated_at', 
                        name: 'updated_at',
                        render: function(data, type, row){
                            if(type === "sort" || type === "type"){
                                return data;
                            }
                            return moment(data).format("MM-DD-YYYY HH:mm");
                        }
                    },
                    {data: 'currency', name: 'currency'},
                    {data: 'address', name: 'address'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action'},
                ]
            });
        })(jQuery);
    </script>
@endpush



