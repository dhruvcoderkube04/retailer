<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6 ms-2" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        {{-- <a href="{{ route('retailer.dashboard') }}">
            <img src="{{ Auth::user()->userDetail && Auth::user()->userDetail->company_logo
                ? Auth::user()->userDetail->company_logo // Use URL directly from Spaces
                : asset('assets/media/logos/big_mart_nepal_cover.jpg') }}"
                class="h-55px text-center app-sidebar-logo-default" alt="{{ Auth::user()->firstname }}"
                style="border-radius: 10px; justify-content: space-evenly;" />

            <img src="{{ Auth::user()->userDetail && Auth::user()->userDetail->company_logo
                ? Auth::user()->userDetail->company_logo // Use URL directly from Spaces
                : asset('assets/media/logos/bigmart.jpg') }}"
                class="h-25px app-sidebar-logo-minimize" alt="{{ Auth::user()->firstname }}"
                style="border-radius: 5px; justify-content: space-evenly;" />
        </a> --}}

        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="ki-duotone ki-black-left-line fs-3 rotate-180">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <!--end::Sidebar toggle-->
    </div>
    <!--end::Logo-->
    <!--begin::sidebar menu-->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
        <!--begin::Menu wrapper-->
        <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
            <!--begin::Scroll wrapper-->
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true"
                data-kt-scroll-activate="true" data-kt-scroll-height="auto"
                data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
                data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px"
                data-kt-scroll-save-state="true">
                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu"
                    data-kt-menu="true" data-kt-menu-expand="false">
                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->is('dashboard') ? 'active' : '' }}"
                            href="{{ route('retailer.dashboard') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-element-11 fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->is(['wholesaler-list', 'wholesaler-list/*', 'wholesaler/*']) ? 'active' : '' }}"
                            href="{{ route('retailer.wholesaler.list') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-shop fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                            <span class="menu-title">Wholesaler</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->is(['category-list', 'my-category-list', 'category-suggestion']) ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-abstract-26 fs-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                            </span>
                            <span class="menu-title">Category</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->is('category-list') ? 'active' : '' }}"
                                    href="{{ route('retailer.category.list') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Category List</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->is('my-category-list') ? 'active' : '' }}"
                                    href="{{ route('retailer.mycategory.list') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">My Category</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->is('category-suggestion') ? 'active' : '' }}"
                                    href="{{ route('retailer.category-suggestion') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Category Suggestion</span>
                                </a>
                            </div>
                            <!--end:Menu item-->
                        </div>
                        <!--end:Menu sub-->
                    </div>
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->is(['retailer-product', 'add-product/*', 'remove-product/*', 'retailer-add-product', 'retailer-store-product', 'retailer-edit-product/*', 'retailer-update-product/*']) ? 'active' : '' }}"
                            href="{{ route('retailer.product') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-cube-2 fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">Products</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    {{-- <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{request()->is('place-order') ? 'active':''}}" href="{{route('retailer.place-order-view')}}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-handcart fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Place Order</span>
                        </a>
                        <!--end:Menu link-->
                    </div> --}}
                    <!--end:Menu item-->

                    <div data-kt-menu-trigger="click" class="menu-item">
                        <a class="menu-link {{ request()->is(['customers/*', 'customers']) ? 'active' : '' }}"
                            href="{{ route('retailer.customers.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-delivery-3 fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">Customer List</span>
                        </a>
                    </div>

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->is(['orders-list/*', 'orders-list', 'orders-list/action']) ? 'active' : '' }}"
                            href="{{ route('retailer.order.list') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-delivery-3 fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">Customer Orders</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->is(['my-orders/*', 'my-orders']) ? 'active' : '' }}"
                            href="{{ route('retailer.my-order.list') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-delivery-3 fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">Punch Orders</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->is(['shipping-page', 'direct-shipping', 'create-own-order', 'ndr', 'label-setting', 'pick-address-list', 'rto-address', 'report-page', 'shipping-charges', 'pincode-serviceable', 'track-order']) ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-delivery-time fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                            <span class="menu-title">Shipping</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            {{-- <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('direct-shipping') ? 'active':''}}" href="{{route('retailer.direct.shipping')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Direct Shipping</span>
                                </a>
                                <!--end:Menu link-->
                            </div> --}}
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            {{-- <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('create-own-order') ? 'active':''}}" href="{{route('retailer.ownorder')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Create Your Own Order</span>
                                </a>
                                <!--end:Menu link-->
                            </div> --}}
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            {{-- <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('ndr') ? 'active':''}}" href="{{route('retailer.ndr')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">NDR</span>
                                </a>
                                <!--end:Menu link-->
                            </div> --}}
                            <!--end:Menu item-->

                            <!--begin:Menu item-->
                            {{-- <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('label-setting') ? 'active':''}}" href="{{route('retailer.labelsetting')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Label Setting</span>
                                </a>
                                <!--end:Menu link-->
                            </div> --}}
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->is('pick-address-list') ? 'active' : '' }}"
                                    href="{{ route('retailer.pickaddress.list') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Add Warehouse </span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->

                            <!--begin:Menu item-->
                            {{-- <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->is('rto-address') ? 'active' : '' }}"
                                    href="{{ route('retailer.rto.address') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Add RTO Address</span>
                                </a>
                                <!--end:Menu link-->
                            </div> --}}
                            <!--end:Menu item-->

                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->is('pincode-serviceable') ? 'active' : '' }}"
                                    href="{{ route('retailer.pincode.serviceable') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Check Service Available</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->is('track-order') ? 'active' : '' }}"
                                    href="{{ route('retailer.track.order') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Track Order </span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->

                            <!--begin:Menu item-->
                            {{-- <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('report-page') ? 'active':''}}" href="{{route('retailer.report.page')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Report</span>
                                </a>
                                <!--end:Menu link-->
                            </div> --}}
                            <!--end:Menu item-->

                            <!--begin:Menu item-->
                            {{-- <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('shipping-charges') ? 'active':''}}" href="{{route('retailer.shipping.charges')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Shipping Charges</span>
                                </a>
                                <!--end:Menu link-->
                            </div> --}}
                            <!--end:Menu item-->
                        </div>
                        <!--end:Menu sub-->
                    </div>
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    {{-- <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is(['automation','automation-campaign']) ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-shop fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                            <span class="menu-title">Automation</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('automation') ? 'active':''}}" href="{{route('retailer.automation.index')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Broadcast</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link  {{request()->is('automation-campaign') ? 'active':''}}" href="{{route('retailer.automation.campaign')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Campaign </span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                        </div>
                        <!--end:Menu sub-->
                    </div> --}}
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->is(['coupon-page']) ? 'active' : '' }}"
                            href="{{ route('retailer.coupon.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-flag fs-1">
                                    <span class="path1"></span>
                                </i>
                            </span>
                            <span class="menu-title">Coupon</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    {{-- <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{request()->is(['abondard-page']) ? 'active':''}}" href="{{route('retailer.abandonard.index')}}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-purchase fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title"> Abandoned Card </span>
                        </a>
                        <!--end:Menu link-->
                    </div> --}}
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    {{-- <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{request()->is(['cms-page']) ? 'active':''}}" href="{{route('retailer.cms.index')}}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-underlining fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title"> CMS </span>
                        </a>
                        <!--end:Menu link-->
                    </div> --}}
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->is(['setting-page', 'retailer-web-setting']) ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-solid ki-setting-2 fs-1"></i>
                            </span>
                            <span class="menu-title">Setting</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->is('retailer-web-setting') ? 'active' : '' }}"
                                    href="{{ route('retailer.web.setting') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Create Your Store</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->is('setting-page') ? 'active' : '' }}"
                                    href="{{ route('retailer.setting.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Setup Store</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                        </div>
                        <!--end:Menu sub-->
                    </div>
                    <!--end:Menu item-->


                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->is(['ticket-list']) ? 'show' : '' }}">
                        <!--begin:Menu link-->
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-solid ki-support-24 fs-1"></i>
                            </span>
                            <span class="menu-title">Support</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{ request()->is('ticket-list') ? 'active' : '' }}"
                                    href="{{ route('retailer.ticket.list') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Ticket List</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->
                        </div>
                        <!--end:Menu sub-->
                    </div>
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->is(['prohibited-item']) ? 'active' : '' }}"
                            href="{{ route('retailer.prohibited.item') }}">
                            <span class="menu-icon fs-1">
                                <i class="ki-solid ki-basket-ok fs-1"></i>
                            </span>
                            <span class="menu-title">Prohibited Item</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{ request()->is(['rate-calculation']) ? 'active' : '' }}"
                            href="{{ route('retailer.rate.calculation') }}">
                            <span class="menu-icon fs-1">
                                <i class="ki-solid ki-finance-calculator fs-1"></i>
                            </span>
                            <span class="menu-title">Rate Calculation</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->


                    <!-- Wallet Main Menu -->
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->is('accounts*') ? 'show' : '' }}">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-wallet fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">Wallet</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion">
                            <!-- Success Wallet Transactions -->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->is('accounts/transactions/success-wallet') ? 'active' : '' }}"
                                    href="{{ route('retailer.accounts.transactions.success-wallet') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Success Wallet</span>
                                </a>
                            </div>

                            <!-- Pending Wallet Transactions -->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->is('accounts/transactions/pending-wallet') ? 'active' : '' }}"
                                    href="{{ route('retailer.accounts.transactions.pending-wallet') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Pending Wallet</span>
                                </a>
                            </div>

                            <!-- Withdrawal Request -->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->is('accounts/withdrawal-request') ? 'active' : '' }}"
                                    href="{{ route('retailer.accounts.withdrawal-request') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Withdrawal Request</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">Layouts</span>
                        </div>
                    </div>
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion {{ request()->is(['themes', 'themes/*']) ? 'show' : '' }}">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-shop fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">Online Store</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion">
                            <!-- Themes -->
                            <div class="menu-item">
                                <a class="menu-link {{ request()->is(['themes', 'themes/*']) ? 'active' : '' }}"
                                    href="{{ route('retailer.themes.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Themes</span>
                                </a>
                            </div>
                        </div>
                    </div>



                </div>
                <!--end::Menu-->
            </div>
            <!--end::Scroll wrapper-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
</div>
