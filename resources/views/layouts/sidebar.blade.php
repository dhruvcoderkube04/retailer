<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Logo-->
    <div class="app-sidebar-logo px-6 ms-2" id="kt_app_sidebar_logo">
        <!--begin::Logo image-->
        <a href="{{ route('retailer.dashboard') }}">
            <img src="{{ asset('assets/media/logos/big_mart_nepal_cover.jpg') }}" class="h-55px text-center" alt="{{Auth::user()->firstname}}" style="border-radius: 10px;" />
            <!-- <img src="{{ Auth::user()->userDetail && Auth::user()->userDetail->company_logo
                ? asset('uploads/company_profile/' . Auth::user()->userDetail->company_logo)
                : asset('assets/media/avatars/blank.png') }}" class="h-25px app-sidebar-logo-default" alt="{{Auth::user()->firstname}}" /> -->
            <!-- <br /> -->
            <!-- {{Auth::user()->firstname}} -->
        </a>

        <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
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
            <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
                <!--begin::Menu-->
                <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{request()->is('dashboard') ? 'active':''}}" href="{{route('retailer.dashboard')}}">
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
                        <a class="menu-link {{request()->is(['wholesaler-list','wholesaler-list/*']) ? 'active':''}}" href="{{route('retailer.wholesaler.list')}}">
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
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{request()->is(['retailer-product', 'add-product/*', 'remove-product/*']) ? 'active':''}}" href="{{route('retailer.product')}}">
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

                    <!--begin:Menu item-->
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{request()->is(['orders-list/*','orders-list','orders-list/action']) ? 'active':''}}" href="{{route('retailer.order.list')}}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-delivery-3 fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">Orders</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->

                     <!--begin:Menu item-->
                     <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is(['shipping-page','direct-shipping','create-own-order','ndr','label-setting','pick-address-list','rto-address','report-page','shipping-charges']) ? 'show' : '' }}">
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
                                <a class="menu-link {{request()->is('pick-address-list') ? 'active':''}}" href="{{route('retailer.pickaddress.list')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Add PickUp Address</span>
                                </a>
                                <!--end:Menu link-->
                            </div>
                            <!--end:Menu item-->

                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('rto-address') ? 'active':''}}" href="{{route('retailer.rto.address')}}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">Add RTO Address</span>
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
                    <a class="menu-link {{request()->is(['coupon-page']) ? 'active':''}}" href="{{route('retailer.coupon.index')}}">
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
                     <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->is(['setting-page','retailer-web-setting']) ? 'show' : '' }}">
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
                            <span class="menu-title">Setting</span>
                            <span class="menu-arrow"></span>
                        </span>
                        <!--end:Menu link-->
                        <!--begin:Menu sub-->
                        <div class="menu-sub menu-sub-accordion">
                            <!--begin:Menu item-->
                            <div class="menu-item">
                                <!--begin:Menu link-->
                                <a class="menu-link {{request()->is('retailer-web-setting') ? 'active':''}}" href="{{route('retailer.web.setting')}}">
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
                                <a class="menu-link {{request()->is('setting-page') ? 'active':''}}" href="{{route('retailer.setting.index')}}">
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
                    <div data-kt-menu-trigger="click" class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link {{request()->is(['prohibited-item']) ? 'active':''}}" href="{{route('retailer.prohibited.item')}}">
                            <span class="menu-icon fs-1">
                                <i class="ki-solid ki-basket-ok"></i>
                            </span>
                            <span class="menu-title">Prohibited Item</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Scroll wrapper-->
        </div>
        <!--end::Menu wrapper-->
    </div>
    <!--end::sidebar menu-->
</div>
