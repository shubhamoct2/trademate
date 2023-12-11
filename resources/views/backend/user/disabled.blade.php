@extends('backend.layouts.datatable')
@section('title')
    {{ __('All Customers') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Disabled Customers') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-card">
                        <div class="site-card-body table-responsive">
                            <div class="site-datatable">
                                {!! $dataTable->table(['class' => 'data-table']) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Send Email -->
                    @can('customer-mail-send')
                        @include('backend.user.include.__mail_send')
                    @endcan
                    <!-- Modal for Send Email-->
                </div>
            </div>
        </div>
    </div>    
@endsection

@section('script')

    <script>
        (function ($) {
            "use strict";

            //send mail modal form open
            $('body').on('click', '.send-mail', function () {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#name').html(name);
                var url = '{{ route("admin.user.mail-send", ":id") }}';
                url = url.replace(':id', id);
                $('#send-mail-form').attr('action', url);
                $('#sendEmail').modal('toggle')

            })

        })(jQuery);
    </script>
@endsection