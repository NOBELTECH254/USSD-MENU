<x-dashboard-tile :position="$position" :refresh-interval="$refreshInterval">
        <a href="{{ route('payments.c-payments.index') }}" class="card bg-body hoverable card-xl-stretch mb-xl-8"  style="background-color: #F2BD00;">
            <!--begin::Body-->
            <div class="card-body">
                <!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
                <span class="svg-icon svg-icon-white svg-icon-3x ms-n1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path opacity="0.3"
                            d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z"
                            fill="black"></path>
                        <path
                            d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z"
                            fill="black"></path>
                    </svg>
                </span>
                <!--end::Svg Icon-->
                <div class="text-gray-900 fw-bolder fs-2 mb-2 mt-5" id="payments_today">
                    {{ number_format($data['thisMonthsPayments']) }}
                </div>

                <div class="fw-bold text-primary"> {{ $data['month'] }} Payments</div>
            </div>
            <!--end::Body-->
        </a>
    </x-dashboard-tile>