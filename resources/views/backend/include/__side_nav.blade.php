<div class="side-nav">
    <div class="side-nav-inside">
        <ul class="side-nav-menu">

            <li class="side-nav-item {{ isActive('admin.dashboard') }}">
                <a href="{{route('admin.dashboard')}}"><i
                        icon-name="layout-dashboard"></i><span>{{ __('Dashboard') }}</span></a>
            </li>

            {{-- *************************************************************  Customer Management *********************************************************--}}
            @canany(['customer-list','customer-login','customer-mail-send','customer-basic-manage','customer-balance-add-or-subtract','customer-change-password','all-type-status'])
                <li class="side-nav-item category-title">
                    <span>{{ __('Customer Management') }}</span>
                </li>
                <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.user*','admin.notification*']) }}">
                    <a href="javascript:void(0);" class="dropdown-link">
                        <i icon-name="users"></i><span>{{ __('Customers') }}</span>
                        <span class="right-arrow"><i icon-name="chevron-down"></i></span></a>
                    <ul class="dropdown-items">
                        @canany(['customer-list','customer-login','customer-mail-send','customer-basic-manage','customer-balance-add-or-subtract','customer-change-password','all-type-status'])
                            <li class="{{ isActive('admin.user.index') }}">
                                <a href="{{route('admin.user.index')}}"><i
                                        icon-name="users"></i>{{ __('All Customers') }}</a>
                            </li>
                            <li class="{{ isActive('admin.user.active') }}">
                                <a href="{{ route('admin.user.active') }}"><i
                                        icon-name="user-check"></i>{{ __('Active Customers') }}</a>
                            </li>
                            <li class="{{ isActive('admin.user.disabled') }}">
                                <a href="{{ route('admin.user.disabled') }}"><i
                                        icon-name="user-x"></i>{{ __('Disabled Customers') }}</a>
                            </li>

                        @endcanany
                        <li class="{{ isActive('admin.notification.all') }}">
                            <a href="{{ route('admin.notification.all') }}"><i
                                    icon-name="megaphone"></i>{{ __('Notifications') }}</a>
                        </li>
                        @can('customer-mail-send')
                            <li class="{{ isActive('admin.user.mail-send.all') }}">
                                <a href="{{ route('admin.user.mail-send.all') }}"><i
                                        icon-name="send"></i>{{ __('Send Email to all') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcanany

            @canany(['kyc-list','kyc-action','kyc-form-manage'])
                <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.kyc*']) }}">
                    <a href="javascript:void(0);" class="dropdown-link"><i
                            icon-name="check-square"></i><span>{{ __('KYC Management') }}</span><span
                            class="right-arrow"><i icon-name="chevron-down"></i></span></a>
                    <ul class="dropdown-items">
                        @canany(['kyc-list','kyc-action'])
                            <li class="{{ isActive('admin.kyc.pending') }}">
                                <a href="{{ route('admin.kyc.pending') }}"><i
                                        icon-name="airplay"></i>{{ __('Pending KYC') }}</a>
                            </li>
                            <li class="{{ isActive('admin.kyc.verified') }}">
                                <a href="{{ route('admin.kyc.verified') }}"><i
                                        icon-name="receipt"></i>{{ __('Verified KYC') }}</a>
                            </li>
                            <li class="{{ isActive('admin.kyc.rejected') }}">
                                <a href="{{ route('admin.kyc.rejected') }}"><i
                                        icon-name="file-warning"></i>{{ __('Rejected KYC') }}</a>
                            </li>
                            <li class="{{ isActive('admin.kyc.all') }}">
                                <a href="{{ route('admin.kyc.all') }}"><i
                                        icon-name="contact"></i>{{ __('All KYC Logs') }}</a>
                            </li>
                        @endcanany
                    </ul>
                </li>
            @endcanany


            {{-- *************************************************************  Staff Management *********************************************************--}}
            @canany(['role-list','role-create','role-edit','staff-list','staff-create','staff-edit'])
                <li class="side-nav-item category-title">
                    <span>{{ __('Staff Management') }}</span>
                </li>
                @canany(['role-list','role-create','role-edit'])
                    <li class="side-nav-item {{ isActive('admin.roles*') }}">
                        <a href="{{route('admin.roles.index')}}"><i
                                icon-name="contact"></i><span>{{ __('Manage Roles') }}</span></a>
                    </li>
                @endcanany
                @canany(['staff-list','staff-create','staff-edit'])
                    <li class="side-nav-item {{ isActive('admin.staff*') }}">
                        <a href="{{route('admin.staff.index')}}"><i
                                icon-name="user-cog"></i><span>{{ __('Manage Staffs') }}</span></a>
                    </li>
                @endcanany
            @endcanany

            {{-- *************************************************************  Plan Management *********************************************************--}}
            @canany(['schedule-manage','schema-list','schema-create','schema-edit'])
                <li class="side-nav-item category-title">
                    <span>{{ __('Plans') }}</span>
                </li>
                <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.schedule*','admin.schema*']) }}">
                    <a href="javascript:void(0);" class="dropdown-link"><i
                            icon-name="album"></i><span>{{ __('Manage Investments') }}</span>
                        <span class="right-arrow"><i icon-name="chevron-down"></i></span></a>
                    <ul class="dropdown-items">
                        @canany(['schema-list','schema-create','schema-edit'])
                            <li class="side-nav-item {{ isActive('admin.schedule*') }}">
                                <a href="{{route('admin.schedule.index')}}"><i
                                        icon-name="alarm-check"></i><span>{{ __('Schedule') }}</span></a>
                            </li>
                        @endcanany
                        @can('schema-edit')
                            <li class="side-nav-item {{ isActive('admin.schema*') }}">
                                <a href="{{route('admin.schema.index')}}"><i
                                        icon-name="airplay"></i><span>{{ __('Manage Investments') }}</span></a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcanany

            {{-- *************************************************************  Transactions *********************************************************--}}
            @canany(['transaction-list','investment-list','profit-list', 'profit-wallet-manage'])
                <li class="side-nav-item category-title">
                    <span>{{ __('Transactions') }}</span>
                </li>
                @can('transaction-list')
                    <li class="side-nav-item {{ isActive('admin.transactions') }}">
                        <a href="{{route('admin.transactions')}}"><i
                                icon-name="cast"></i><span>{{ __('Transactions') }}</span></a>
                    </li>
                @endcan
                @can('investment-list')
                    <li class="side-nav-item {{ isActive('admin.investments') }}">
                        <a href="{{route('admin.investments')}}"><i
                                icon-name="anchor"></i><span>{{ __('Investments') }}</span></a>
                    </li>
                @endcan
                @can('profit-list')
                    <li class="side-nav-item {{ isActive('admin.all-profits') }}">
                        <a href="{{route('admin.all-profits')}}"><i
                                icon-name="credit-card"></i><span>{{ __('User Profits') }}</span></a>
                    </li>
                @endcan
                @can('customer-commission-manage')
                    <li class="side-nav-item {{ isActive('admin.commission.list') }}">
                        <a href="{{ route('admin.commission.list') }}"><i
                            icon-name="door-open"></i><span>{{ __('Commission') }}</span></a>
                    </li>
                @endcan

                @canany(['profit-wallet-manage']) 
                    <li class="side-nav-item {{ isActive('admin.profit.list') }}">
                        <a href="{{ route('admin.profit.list') }}">
                            <i icon-name="compass"></i><span>{{ __('Profit Wallet Master History') }}</span>
                        </a>
                    </li>
                @endcanany
            @endcanany

            {{-- *************************************************************  Essentials *********************************************************--}}
            @canany(['automatic-gateway-manage','manual-gateway-manage','deposit-list','deposit-action',
            'withdraw-list','withdraw-method-manage','withdraw-action','target-manage','referral-create',
            'referral-list','referral-edit','referral-delete','ranking-list','ranking-create','ranking-edit','profit-wallet-manage'])

                <li class="side-nav-item category-title">
                    <span>{{ __('Essentials') }}</span>
                </li>

                @canany(['automatic-gateway-manage','manual-gateway-manage','deposit-list','deposit-action'])
                    @can('automatic-gateway-manage')
                        <li class="side-nav-item {{ isActive('admin.gateway*') }}">
                            <a href="{{ route('admin.gateway.automatic') }}"><i
                                    icon-name="door-open"></i><span>{{ __('Automatic Gateways') }}</span></a>
                        </li>
                    @endcan

                    <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.deposit*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link"><i
                                icon-name="arrow-down-circle"></i><span>{{ __('Deposits') }}</span><span
                                class="right-arrow"><i icon-name="chevron-down"></i></span></a>
                        <ul class="dropdown-items">

                            @can('automatic-gateway-manage')
                                <li class="{{ isActive('admin.deposit.method.list','auto') }}"><a
                                        href="{{ route('admin.deposit.method.list','auto') }}"><i
                                            icon-name="workflow"></i>{{ __('Automatic Methods') }}</a></li>
                            @endcan

                            @can('manual-gateway-manage')
                                <li class="{{ isActive('admin.deposit.method.list','manual') }}"><a
                                        href="{{route('admin.deposit.method.list','manual')}}"><i
                                            icon-name="compass"></i>{{ __('Manual Methods') }}</a></li>
                            @endcan

                            @canany(['deposit-list','deposit-action'])
                                <li class="{{ isActive('admin.deposit.manual.pending') }}"><a
                                        href="{{ route('admin.deposit.manual.pending') }}"><i
                                            icon-name="columns"></i>{{ __('Pending Manual Deposits') }}</a></li>
                                <li class="{{ isActive('admin.deposit.history') }}"><a
                                        href="{{ route('admin.deposit.history') }}"><i
                                            icon-name="clipboard-check"></i>{{ __('Deposit History') }}</a></li>
                            @endcanany
                            
                        </ul>
                    </li>
                @endcanany

                @canany(['withdraw-list','withdraw-method-manage','withdraw-action','withdraw-schedule'])
                    <li class="side-nav-item side-nav-dropdown  {{ isActive(['admin.withdraw*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link"><i
                                icon-name="landmark"></i><span>{{ __('Withdraw') }}</span><span class="right-arrow"><i
                                    icon-name="chevron-down"></i></span></a>
                        <ul class="dropdown-items">
                            @can('withdraw-method-manage')
                                <li class="{{ isActive('admin.withdraw.method.list','auto')  }}">
                                    <a
                                        href="{{ route('admin.withdraw.method.list','auto') }}"><i
                                            icon-name="workflow"></i>{{ __('Automatic Methods') }}</a></li>
                                <li class="{{ isActive('admin.withdraw.method.list','manual') }}">
                                    <a
                                        href="{{route('admin.withdraw.method.list','manual')}}"><i
                                            icon-name="compass"></i>{{ __('Manual Methods') }}</a></li>

                            @endcan
                            @canany(['withdraw-list','withdraw-action'])
                                <li class="{{ isActive('admin.withdraw.pending')  }}"><a
                                        href="{{ route('admin.withdraw.pending') }}"><i
                                            icon-name="wallet"></i>{{ __('Pending Withdraws') }}</a></li>
                            @endcanany
                            @can('withdraw-schedule')
                                <li class="{{ isActive('admin.withdraw.schedule') }}"><a
                                        href="{{ route('admin.withdraw.schedule') }}"><i
                                            icon-name="alarm-clock"></i>{{ __('Withdraw Schedule') }}</a></li>
                            @endcan
                            @can('withdraw-list')
                                <li class="{{ isActive('admin.withdraw.history') }}"><a
                                        href="{{ route('admin.withdraw.history') }}"><i
                                            icon-name="piggy-bank"></i>{{ __('Withdraw History') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                @canany(['internal-transfer-manage'])
                    @can('internal-transfer-manage')
                        <li class="side-nav-item {{ isActive('admin.exchange.list') }}">
                            <a href="{{ route('admin.exchange.list') }}"><i
                                    icon-name="door-open"></i><span>{{ __('Internal Transfer') }}</span></a>
                        </li>
                    @endcan
                @endcanany

                @canany(['target-manage','referral-create','referral-list','referral-edit','referral-delete'])
                    <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.referral*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link"><i
                                icon-name="settings-2"></i><span>{{ __('Manage Referral') }}</span><span
                                class="right-arrow"><i icon-name="chevron-down"></i></span></a>
                        <ul class="dropdown-items">

                            @canany(['referral-create','referral-list','referral-edit','referral-delete'])
                                <li class="{{ isActive('admin.referral.level*') }}">
                                    <a href="{{ route('admin.referral.level.index') }}"><i
                                            icon-name="align-end-horizontal"></i>{{ __('Multi Level Referral') }}</a>
                                </li>
                                <li class="{{ isActive('admin.referral.index') }}">
                                    <a href="{{ route('admin.referral.index') }}"><i
                                            icon-name="expand"></i>{{ __('Targets Referral') }}</a>
                                </li>
                            @endcanany

                        </ul>
                    </li>
                @endcanany

                @canany(['ranking-list','ranking-create','ranking-edit'])
                    <li class="side-nav-item {{ isActive('admin.ranking*') }}">
                        <a href="{{ route('admin.ranking.index') }}"><i
                                icon-name="medal"></i><span>{{ __('User Rankings') }}</span></a>
                    </li>
                @endcan

                @canany(['profit-wallet-manage']) 
                    <li class="side-nav-item {{ isActive('admin.profit.index') }}">
                        <a href="{{ route('admin.profit.index') }}">
                            <i icon-name="compass"></i><span>{{ __('Profit Wallet Admin') }}</span>
                        </a>
                    </li>
                @endcanany

            @endcanany



            {{-- ************************************************************* Site  Settings *********************************************************--}}
            @canany(['site-setting','email-setting','plugin-setting','page-manage'])
                <li class="side-nav-item category-title">
                    <span>{{ __('Site Settings') }}</span>
                </li>
                @canany(['site-setting','email-setting','plugin-setting'])
                    <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.settings*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link"><i icon-name="settings"></i>
                            <span>{{ __('Settings') }}</span><span class="right-arrow"><i icon-name="chevron-down"></i></span></a>
                        <ul class="dropdown-items">
                            @can('site-setting')
                                <li class="{{ isActive('admin.settings.site') }}">
                                    <a href="{{route('admin.settings.site')}}"><i
                                            icon-name="settings-2"></i>{{ __('Site Settings') }}</a>
                                </li>
                            @endcan
                            @can('email-setting')
                                <li class="{{ isActive('admin.settings.mail') }}">
                                    <a href="{{ route('admin.settings.mail') }}"><i
                                            icon-name="inbox"></i>{{ __('Email Settings') }}</a>
                                </li>
                            @endcan
                            @can('plugin-setting')
                                <li class="{{ isActive('admin.settings.plugin','system') }}">
                                    <a href="{{ route('admin.settings.plugin','system') }}"><i
                                            icon-name="toy-brick"></i>{{ __('Plugin Settings') }}</a>
                                </li>

                                <li class="{{ isActive('admin.settings.plugin','sms') }}">
                                    <a href="{{ route('admin.settings.plugin','sms') }}"><i
                                            icon-name="message-circle"></i>{{ __('SMS Settings') }}</a>
                                </li>

                                <li class="{{ isActive('admin.settings.plugin','notification') }}">
                                    <a href="{{ route('admin.settings.plugin','notification') }}"><i
                                            icon-name="bell-ring"></i>{{ __('Push Notification') }}</a>
                                </li>
                                <li class="{{ isActive('admin.settings.notification.tune') }}">
                                    <a href="{{ route('admin.settings.notification.tune') }}"><i
                                            icon-name="volume-2"></i>{{ __('Notification Tune') }}</a>
                                </li>
                            @endcan

                        </ul>
                    </li>
                @endcanany

                @can('language-setting')
                    <li class="side-nav-item {{ isActive('admin.language*') }}">
                        <a href="{{ route('admin.language.index') }}"><i
                                icon-name="languages"></i><span>{{ __('Language Settings') }}</span></a>
                    </li>
                @endcan
                @can('page-manage')
                    <li class="side-nav-item {{ isActive('admin.page.setting') }}">
                        <a href="{{ route('admin.page.setting') }}"><i
                                icon-name="layout"></i><span>{{ __('Page Settings') }}</span></a>
                    </li>
                @endcan
            @endcanany


            {{-- ************************************************************* Site  Essentials *********************************************************--}}
            @canany(['landing-page-manage','page-manage','footer-manage','navigation-manage'])
                <li class="side-nav-item category-title">
                    <span>{{ __('Site Essentials') }}</span>
                </li>
                @can('landing-page-manage')

                    <!-- {{-- site theme Management--}}
                    <li class="side-nav-item side-nav-dropdown  {{ isActive(['admin.theme*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link"><i
                                icon-name="palette"></i><span>{{ __('Theme Manage') }}</span><span
                                class="right-arrow"><i icon-name="chevron-down"></i></span></a>
                        <ul class="dropdown-items">
                            <li class="{{ isActive('admin.theme.site') }}">
                                <a href="{{ route('admin.theme.site') }}"><i
                                        icon-name="roller-coaster"></i>{{ __('Site Theme') }}</a>
                            </li>
                            <li class="{{ isActive('admin.theme.dynamic-landing') }}">
                                <a href="{{ route('admin.theme.dynamic-landing') }}"><i
                                        icon-name="warehouse"></i>{{ __('Dynamic Landing Theme') }}</a>
                            </li>

                        </ul>
                    </li>
                    {{-- end site theme Management--}}-->

                    <li class="side-nav-item side-nav-dropdown  {{ isActive(['admin.page.section.section*']) }}">
                        <a href="javascript:void(0);" class="dropdown-link"><i
                                icon-name="home"></i><span>{{ __('Landing Page') }}</span><span class="right-arrow"><i
                                    icon-name="chevron-down"></i></span></a>
                        <ul class="dropdown-items">
                            @foreach($landingSections as $section)
                                <li class="@if(request()->is('admin/page/section/'.$section->code)) active @endif">
                                    <a href="{{ route('admin.page.section.section',$section->code) }}"><i
                                            icon-name="egg"></i>{{ $section->name }}</a>
                                </li>
                            @endforeach

                        </ul>
                    </li>
                @endcan
                @can('page-manage')
                    <li class="side-nav-item side-nav-dropdown {{ isActive(['admin.page.edit*','admin.page.create']) }}">
                        <a href="javascript:void(0);" class="dropdown-link"><i
                                icon-name="layout-grid"></i><span>{{ __('Pages') }}</span><span class="right-arrow"><i
                                    icon-name="chevron-down"></i></span></a>
                        <ul class="dropdown-items">
                            @foreach($pages as $page)
                                <li class="@if(request()->is('admin/page/edit/'.$page->code)) active @endif">
                                    <a href="{{ route('admin.page.edit',$page->code) }}"><i
                                            icon-name="egg"></i>{{ $page->title }}</a>
                                </li>
                            @endforeach
                            <li class="{{ isActive('admin.page.create') }}">
                                <a href="{{ route('admin.page.create') }}"><i
                                        icon-name="egg"></i>{{ __('Add New Page') }}</a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('navigation-manage')
                    <li class="side-nav-item {{ isActive('admin.navigation*') }}">
                        <a href="{{ route('admin.navigation.menu') }}"><i
                                icon-name="menu"></i><span>{{ __('Site Navigations') }}</span></a>
                    </li>
                @endcan
                @can('footer-manage')
                    <li class="side-nav-item {{ isActive('admin.footer-content') }}">
                        <a href="{{ route('admin.footer-content') }}"><i
                                icon-name="list-end"></i><span>{{ __('Footer Contents') }}</span></a>
                    </li>
                @endcan
            @endcanany


            {{-- **************************************************** Newslatter Templates  *************************************************************** --}}

            <li class="side-nav-item category-title">
                <span>{{ __('Templates') }}</span>
            </li>
            @can('email-template')
                <li class="side-nav-item {{ isActive('admin.email-template') }}">
                    <a href="{{ route('admin.email-template') }}"><i
                            icon-name="mail"></i><span>{{ __('Email Template') }}</span></a>
                </li>

                <li class="side-nav-item {{ isActive('admin.template.sms.index') }}">
                    <a href="{{ route('admin.template.sms.index') }}"><i
                            icon-name="message-square"></i><span>{{ __('SMS Template') }}</span></a>
                </li>

                <li class="side-nav-item {{ isActive('admin.template.notification.index') }}">
                    <a href="{{ route('admin.template.notification.index') }}"><i
                            icon-name="bell-ring"></i><span>{{ __('Push Notification Template') }}</span></a>
                </li>
            @endcan


            {{-- ************************************************************* Others *********************************************************--}}
            <li class="side-nav-item category-title">
                <span>{{ __('Others') }}</span>
            </li>
            @canany(['subscriber-list','subscriber-mail-send'])
                <li class="side-nav-item {{ isActive('admin.subscriber') }}">
                    <a href="{{ route('admin.subscriber') }}"><i
                            icon-name="mail-open"></i><span>{{ __('All Subscriber') }}</span></a>
                </li>
            @endcanany
            @canany(['support-ticket-list','support-ticket-action'])
                <li class="side-nav-item {{ isActive('admin.ticket*') }}">
                    <a href="{{ route('admin.ticket.index') }}"><i
                            icon-name="wrench"></i><span>{{ __('Support Tickets') }}</span></a>
                </li>
            @endcanany


            @can('custom-css')
                <li class="side-nav-item {{ isActive('admin.custom-css') }}">
                    <a href="{{ route('admin.custom-css') }}"><i
                            icon-name="braces"></i><span>{{ __('Custom CSS') }}</span></a>
                </li>
            @endcan

            <li class="side-nav-item {{ isActive('admin.clear-cache') }}">
                <a href="{{ route('admin.clear-cache') }}"><i
                        icon-name="trash-2"></i><span>{{ __('Clear Cache') }}</span></a>
            </li>
            <li class="side-nav-item  {{ isActive('admin.application-info') }}">
                <a href="{{ route('admin.application-info') }}"><i
                        icon-name="indent"></i><span>{{ __('Application Details') }}</span><span
                        class="badge yellow-color">2.4</span></a>
            </li>
        </ul>
    </div>
</div>
