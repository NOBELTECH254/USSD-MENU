<x-default-layout>

@section('title')
        Dashboard
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    <div class="row g-5 g-xl-8">
    <!--begin::Col-->
    <div class="col-xl-4" id ="today_tiles">
        
<!--begin::Mixed Widget 1-->
<div class="card card-xl-stretch mb-xl-8">
    <!--begin::Body-->
    <div class="card-body p-0">
        <!--begin::Header-->
        <div class="px-9 pt-7 card-rounded h-275px w-100 bg-primary">
            <!--begin::Heading-->
            <div class="d-flex flex-stack">
                <h3 class="m-0 text-white fw-bold fs-3">{{ date("Y-M-d") }} (Today's) Summary</h3>
             </div>
            <!--end::Heading-->

          
        </div>
        <!--end::Header-->

        <!--begin::Items-->
        <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1" style="margin-top: -100px">
                            <!--begin::Item-->
                <div class="d-flex align-items-center mb-6">
                    <!--begin::Symbol-->
                    <div class="symbol symbol-45px w-40px me-5">
                        <span class="symbol-label bg-lighten">                             
                            <i class="ki-duotone ki-compass fs-1"><span class="path1"></span><span class="path2"></span></i>                          
                        </span>
                    </div>
                    <!--end::Symbol-->

                    <!--begin::Description-->
                    <div class="d-flex align-items-center flex-wrap w-100">
                        <!--begin::Title-->
                        <div class="mb-1 pe-3 flex-grow-1">
                            <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">ussd dials</a>
                        </div>
                        <!--end::Title-->

                        <!--begin::Label-->
                        <div class="d-flex align-items-center ">
                            <div class="fw-bold fs-1 text-gray-800 pe-1" id ="ussd_today">0</div> 
                            
                                                    </div>
                        <!--end::Label-->
                    </div>
                    <!--end::Description-->                     
                </div>
                <!--end::Item-->
                            <!--begin::Item-->
                <div class="d-flex align-items-center mb-6">
                    <!--begin::Symbol-->
                    <div class="symbol symbol-45px w-40px me-5">
                        <span class="symbol-label bg-lighten">                             
                            <i class="ki-duotone ki-element-11 fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>                          
                        </span>
                    </div>
                    <!--end::Symbol-->

                    <!--begin::Description-->
                    <div class="d-flex align-items-center flex-wrap w-100">
                        <!--begin::Title-->
                        <div class="mb-1 pe-3 flex-grow-1">
                            <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">Registrations</a>

                        </div>
                        <!--end::Title-->

                        <!--begin::Label-->
                        <div class="d-flex align-items-center">
                            <div class="fw-bold fs-1 text-gray-800 pe-1" id ="registrations_today" >0</div>
                         </div>
                        <!--end::Label-->
                    </div>
                    <!--end::Description-->           

                    
                </div>
                <!--end::Item-->
                            <!--begin::Item-->
                <div class="d-flex align-items-center mb-6">
                    <!--begin::Symbol-->
                    <div class="symbol symbol-45px w-40px me-5">
                        <span class="symbol-label bg-lighten">                             
                            <i class="ki-duotone ki-graph-up fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>                          
                        </span>
                    </div>
                    <!--end::Symbol-->

                    <!--begin::Description-->
                    <div class="d-flex align-items-center flex-wrap w-100">
                        <!--begin::Title-->
                        <div class="mb-1 pe-3 flex-grow-1">
                            <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">loans </a>

                        </div>
                        <!--end::Title-->

                        <!--begin::Label-->
                        <div class="d-flex align-items-center">
                            <div class="fw-bold fs-1 text-gray-800 pe-1" id ="loans_today">0</div>  
                                                    </div>
                        <!--end::Label-->
                    </div>
                    <!--end::Description-->                     
                </div>
                


                <!--end::Item-->
                   

               


                          <!--begin::Item-->
                          <div class="d-flex align-items-center ">
                    <!--begin::Symbol-->
                    <div class="symbol symbol-45px w-40px me-5">
                        <span class="symbol-label bg-lighten">                             
                            <i class="ki-duotone ki-document fs-1"><span class="path1"></span><span class="path2"></span></i>                          
                        </span>
                    </div>
                    <!--end::Symbol-->

                    <!--begin::Description-->
                    <div class="d-flex align-items-center flex-wrap w-100">
                        <!--begin::Title-->
                        <div class="mb-1 pe-3 flex-grow-1">
                            <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">Payments </a>

                        </div>
                        <!--end::Title-->

                        <!--begin::Label-->
                        <div class="d-flex align-items-center">
                            <div class="fw-bold fs-1 text-gray-800 pe-1" id ="payments_today">0</div>   
                                                    </div>
                        <!--end::Label-->
                    </div>
                    <!--end::Description-->                     
                </div>
                <!--end::Item-->
              
        </div>   
        <!--end::Items-->
    </div>
    <!--end::Body-->
</div>
<!--end::Mixed Widget 1-->
    </div>
    <!--end::Col-->



     <!--begin::Col-->
     <div class="col-xl-4" id ="week_tiles">
        
        <!--begin::Mixed Widget 1-->
        <div class="card card-xl-stretch mb-xl-8">
            <!--begin::Body-->
            <div class="card-body p-0">
                <!--begin::Header-->
                <div class="px-9 pt-7 card-rounded h-275px w-100 bg-danger">
                    <!--begin::Heading-->
                    <div class="d-flex flex-stack">
                        <h3 class="m-0 text-white fw-bold fs-3">{{ date("Y-M-d") }} (Week's) summary</h3>
                     </div>
                    <!--end::Heading-->
        
                </div>
                <!--end::Header-->
        
                <!--begin::Items-->
                <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1" style="margin-top: -100px">
                                    <!--begin::Item-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">                             
                                    <i class="ki-duotone ki-compass fs-1"><span class="path1"></span><span class="path2"></span></i>                          
                                </span>
                            </div>
                            <!--end::Symbol-->
        
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">ussd</a>
                                </div>
                                <!--end::Title-->
        
                                <!--begin::Label-->
                                <div class="d-flex align-items-center ">
                                    <div class="fw-bold fs-1 text-gray-800 pe-1" id ="ussd_week">0</div> 
        
                                    
                                                            </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->                     
                        </div>
                        <!--end::Item-->
                                    <!--begin::Item-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">                             
                                    <i class="ki-duotone ki-element-11 fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>                          
                                </span>
                            </div>
                            <!--end::Symbol-->
        
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">registrations</a>
        
                                </div>
                                <!--end::Title-->
        
                                <!--begin::Label-->
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold fs-1 text-gray-800 pe-1" id ="registrations_week" >0</div>
                                 </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->                     
                        </div>
                        <!--end::Item-->
                                    <!--begin::Item-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">                             
                                    <i class="ki-duotone ki-graph-up fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>                          
                                </span>
                            </div>
                            <!--end::Symbol-->
        
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">loans </a>
        
                                </div>
                                <!--end::Title-->
        
                                <!--begin::Label-->
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold fs-1 text-gray-800 pe-1" id ="loans_week">0</div>  
                                                            </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->                     
                        </div>
                        <!--end::Item-->

                                  <!--begin::Item-->
                                  <div class="d-flex align-items-center ">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">                             
                                    <i class="ki-duotone ki-document fs-1"><span class="path1"></span><span class="path2"></span></i>                          
                                </span>
                            </div>
                            <!--end::Symbol-->
        
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">Payments</a>
        
                                </div>
                                <!--end::Title-->
        
                                <!--begin::Label-->
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold fs-1 text-gray-800 pe-1" id ="payments_week">0</div>   
                                                            </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->                     
                        </div>
                        <!--end::Item-->
                                 
                </div>   
                <!--end::Items-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Mixed Widget 1-->
            </div>
            <!--end::Col-->



             <!--begin::Col-->
    <div class="col-xl-4" id ="month_tiles">
        
        <!--begin::Mixed Widget 1-->
        <div class="card card-xl-stretch mb-xl-8">
            <!--begin::Body-->
            <div class="card-body p-0">
                <!--begin::Header-->
                <div class="px-9 pt-7 card-rounded h-275px w-100 bg-success">
                    <!--begin::Heading-->
                    <div class="d-flex flex-stack">
                        <h3 class="m-0 text-white fw-bold fs-3">{{ date("Y-M") }} (Month's) Summary</h3>
                     </div>
                    <!--end::Heading-->
        
                  
                </div>
                <!--end::Header-->
        
                <!--begin::Items-->
                <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1" style="margin-top: -100px">
                                    <!--begin::Item-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">                             
                                    <i class="ki-duotone ki-compass fs-1"><span class="path1"></span><span class="path2"></span></i>                          
                                </span>
                            </div>
                            <!--end::Symbol-->
        
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">ussd</a>
                                </div>
                                <!--end::Title-->
        
                                <!--begin::Label-->
                                <div class="d-flex align-items-center ">
                                    <div class="fw-bold fs-1 text-gray-800 pe-1" id ="ussd_month">0</div> 
        
                                    
                                                            </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->                     
                        </div>
                        <!--end::Item-->
                                    <!--begin::Item-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">                             
                                    <i class="ki-duotone ki-element-11 fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>                          
                                </span>
                            </div>
                            <!--end::Symbol-->
        
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">registrations</a>
        
                                </div>
                                <!--end::Title-->
        
                                <!--begin::Label-->
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold fs-1 text-gray-800 pe-1" id ="registrations_month" >0</div>
                                 </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->                     
                        </div>
                        <!--end::Item-->
                                    <!--begin::Item-->
                        <div class="d-flex align-items-center mb-6">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">                             
                                    <i class="ki-duotone ki-graph-up fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>                          
                                </span>
                            </div>
                            <!--end::Symbol-->
        
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">loans </a>
        
                                </div>
                                <!--end::Title-->
        
                                <!--begin::Label-->
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold fs-1 text-gray-800 pe-1" id ="loans_month">0</div>  
                                                            </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->                     
                        </div>
                        <!--end::Item-->
                    
                                  <!--begin::Item-->
                                  <div class="d-flex align-items-center ">
                            <!--begin::Symbol-->
                            <div class="symbol symbol-45px w-40px me-5">
                                <span class="symbol-label bg-lighten">                             
                                    <i class="ki-duotone ki-document fs-1"><span class="path1"></span><span class="path2"></span></i>                          
                                </span>
                            </div>
                            <!--end::Symbol-->
        
                            <!--begin::Description-->
                            <div class="d-flex align-items-center flex-wrap w-100">
                                <!--begin::Title-->
                                <div class="mb-1 pe-3 flex-grow-1">
                                    <a href="#" class="fs-4 text-gray-800 text-hover-primary fw-bold">Payments</a>
        
                                </div>
                                <!--end::Title-->
        
                                <!--begin::Label-->
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold fs-1 text-gray-800 pe-1" id ="payments_month">0</div>   
                                                            </div>
                                <!--end::Label-->
                            </div>
                            <!--end::Description-->                     
                        </div>
                        <!--end::Item-->
                                 
                </div>   
                <!--end::Items-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Mixed Widget 1-->
            </div>
            <!--end::Col-->
   
</div>

         <div class="row g-5 g-xl-8 mt-2" >

        <div class="card card-flush h-md-100" id ="chart">
            <!--begin::Header-->
            <div class="card-header pt-7">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Statistics</span>
                </h3>
                <!--end::Title-->

               
            </div>
            <!--end::Header-->

            <!--begin::Body-->
            <div class="card-body pt-5">
                <div id="chartdiv" style="height: 500px;"></div>
            </div>
        </div>
    </div>
    </div>
        <!--end::Menu-->
</x-default-layout>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
    today_dashboard();
    week_dashboard();
    month_dashboard();
    update_graph();
        function update_graph() {
            // let url = 'get_data';
            var target = document.querySelector("#chart");
        var blockUI = new KTBlockUI(target, {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> Loading...</div>',
        });
            url = "{{ route('get_chart') }}";
            $.ajax({
                url: url,
                type: 'GET',
               
                dataType: 'json',
                beforeSend: function() {
                    blockUI.block();
                },
                success: function(response) {
                    blockUI.release();
                    if (response.success) {

                        var data = response.data;
                        var columns = data.map(item => `${item.day}`);
                        // [ line-basic-chart ] Start
                        Highcharts.chart('chartdiv', {
                            chart: {
                                type: "line",
                            },
                            colors: ['#7267EF'],
                            title: {
                                text: 'USSD REQUESTS '
                            },
                            subtitle: {
                                text: ''
                            },
                            yAxis: {
                                title: {
                                    text: 'requests'
                                }
                            },
                            xAxis: {
                                categories: columns,
                                name: 'transactions',
                                accessibility: {
                                    description: 'days'
                                }
                            },
                            series: [{
                                name: 'requests',
                                data: data.map(item => parseInt(item.requests))
                            }],
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            layout: 'horizontal',
                                            align: 'center',
                                            verticalAlign: 'bottom'
                                        }
                                    }
                                }]
                            }
                        });

                    } else if (response.error) {
                        console.log("jjk")
                    }
                }
            });
        }


        function today_dashboard()
        {
            var target = document.querySelector("#today_tiles");
        var blockUI = new KTBlockUI(target, {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> Loading...</div>',
        });
            url = "{{ route('get_daily_dashboard') }}";
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    blockUI.block();
                },
                success: function(response) {
                    blockUI.release();
                    if (response.success) {
                        var dashboard_tiles = response.dashboard;
                        $("#balance_today").text(dashboard_tiles.balance_today);
                        $("#ussd_today").text(dashboard_tiles.ussd_today);
                        $("#ussd_count_today").text(dashboard_tiles.ussd_count_today);
                        $("#registrations_today").text(dashboard_tiles.registrations_today);


                        $("#registrations_count_today").text(dashboard_tiles.registrations_count_today);
                        $("#loans_today").text(dashboard_tiles.loans_today);
                        $("#loans_count_today").text(dashboard_tiles.loans_count_today);
                        $("#loans_wins_count_today").text(dashboard_tiles.loans_wins_count_today);
                        $("#stake_wins_today").text(dashboard_tiles.stake_wins_today);
                        $("#payments_today").text(dashboard_tiles.payments_today);
                        $("#payments_count_today").text(dashboard_tiles.payments_count_today);

                    } else if (response.error) {
                        console.log("jjk")
                    }
                }
            });
        }
        function week_dashboard()
        {
            var target = document.querySelector("#week_tiles");
        var blockUI = new KTBlockUI(target, {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> Loading...</div>',
        });
            url = "{{ route('get_week_dashboard') }}";
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    blockUI.block();
                },
                success: function(response) {
                    blockUI.release();
                    if (response.success) {
                        var dashboard_tiles = response.dashboard;
                        $("#balance_week").text(dashboard_tiles.balance_week);
                        $("#ussd_week").text(dashboard_tiles.ussd_week);
                        $("#ussd_count_week").text(dashboard_tiles.ussd_count_week);
                        $("#registrations_week").text(dashboard_tiles.registrations_week);


                        $("#registrations_count_week").text(dashboard_tiles.registrations_count_week);
                        $("#loans_week").text(dashboard_tiles.loans_week);
                        $("#loans_count_week").text(dashboard_tiles.loans_count_week);
                        $("#loans_wins_count_week").text(dashboard_tiles.loans_wins_count_week);
                        $("#stake_wins_week").text(dashboard_tiles.stake_wins_week);
                        $("#payments_week").text(dashboard_tiles.payments_week);
                        $("#payments_count_week").text(dashboard_tiles.payments_count_week);

                    } else if (response.error) {
                        console.log("jjk")
                    }
                }
            });
        }

        function month_dashboard()
        {
            var target = document.querySelector("#month_tiles");
        var blockUI = new KTBlockUI(target, {
            message: '<div class="blockui-message"><span class="spinner-border text-primary"></span> Loading...</div>',
        });
            url = "{{ route('get_month_dashboard') }}";
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    blockUI.block();
                },
                success: function(response) {
                    blockUI.release();
                    if (response.success) {
                        var dashboard_tiles = response.dashboard;
                        $("#balance_month").text(dashboard_tiles.balance_month);
                        $("#ussd_month").text(dashboard_tiles.ussd_month);
                        $("#ussd_count_month").text(dashboard_tiles.ussd_count_month);
                        $("#registrations_month").text(dashboard_tiles.registrations_month);


                        $("#registrations_count_month").text(dashboard_tiles.registrations_count_month);
                        $("#loans_month").text(dashboard_tiles.loans_month);
                        $("#loans_count_month").text(dashboard_tiles.loans_count_month);
                        $("#loans_wins_count_month").text(dashboard_tiles.loans_wins_count_month);
                        $("#stake_wins_month").text(dashboard_tiles.stake_wins_month);
                        $("#payments_month").text(dashboard_tiles.payments_month);
                        $("#payments_count_month").text(dashboard_tiles.payments_count_month);

                    } else if (response.error) {
                        console.log("jjk")
                    }
                }
            });
        }

    });
</script>

