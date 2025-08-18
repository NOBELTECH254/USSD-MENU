<x-default-layout>

    @section('title')
    Game Reports
    @endsection
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card header-->
            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                <form method="POST">
                    @csrf
                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <div class="input-group w-250px">
                            <input class="form-control form-control-solid" placeholder="Pick date rage"
                                id="kt_daterangepicker" />
                        </div>

                        {{-- <a type="button" href="" class="btn btn-primary btn-sm me-3  py-3" id="clear"> --}}
                        <a type="button" href="" class="btn btn-primary" id="clear">


                            <i class="ki-duotone ki-arrows-circle fs-1 warning">
                                <i class="path1"></i>
                                <i class="path2"></i>
                            </i>
                            Clear Filters
                        </a>


                    </div>
                </form>
            </div>

            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <!--begin::Table-->
            <div class="table-responsive">
                                <!--begin::Table-->
  <div id="kt_game_table" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer" id="game-table">
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
<th></th>
<th class="min-w-100px sorting" tabindex="0"
                                        aria-controls="kt_game_table" rowspan="1" colspan="1"
                                        aria-label="Total: activate to sort column ascending" style="width: 100px;">
                                        Date</th>
                           
                                    <th class="min-w-100px sorting" tabindex="0"
                                        aria-controls="kt_game_table" rowspan="1" colspan="1"
                                        aria-label="Total: activate to sort column ascending" style="width: 100px;">
                                        Stake amount</th>
                                    <th class="min-w-100px sorting" tabindex="0"
                                        aria-controls="kt_game_table" rowspan="1" colspan="1"
                                        aria-label="Total: activate to sort column ascending" style="width: 100px;">
                                        Stake Count</th>

                                        <th class="min-w-100px sorting" tabindex="0" aria-controls="kt_game_table"
                                        rowspan="1" colspan="1" aria-label="Total: activate to sort column ascending"
                                        style="width: 100px;">
                                        Amount Won </th>
  
                                        <th class="min-w-100px sorting" tabindex="0" aria-controls="kt_game_table"
                                        rowspan="1" colspan="1" aria-label="Total: activate to sort column ascending"
                                        style="width: 100px;">
                                        Profit</th>
                                        <th class="min-w-100px sorting" tabindex="0" aria-controls="kt_game_table"
                                        rowspan="1" colspan="1" aria-label="Total: activate to sort column ascending"
                                        style="width: 100px;">
                                        RTP </th>
                                       
                                </tr>
                            </thead>

                            <tbody class="fw-semibold text-gray-600">


                            </tbody>
                            </table>
                    </div>

            </div>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>


</x-default-layout>
<script type="text/javascript">
    $(document).ready(function() {
        var start_date = moment().subtract(29, "days");
        var end_date = moment();

        function cb(start, end) {
            start_date = start;
            end_date = end;
            // console.log(mode_of_payment);
            $("#kt_daterangepicker").html(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
            build_table(start.format("YYYY-MM-DD"), end.format("YYYY-MM-DD"));

        }
        $("#kt_daterangepicker").daterangepicker({
            
            timePicker: true,
            startDate: start_date,
            endDate: end_date,
            locale: {
                format: "M/DD hh:mm A"
            }
        }, cb);
        cb(start_date, end_date);

/*      setInterval( function () {
    cb(start_date, end_date);
    }, 30000 );
 */
function build_table(start, end) {

            $('#game-table').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.game-report') }}",
                    data: function(d) {
                        d.from_date = start;
                        d.to_date = end;
                    }
                },
                stateSave: true,
                columns: [
                    {
                        data: null
                    },
                    {
                        data: 'transaction_date'
                    },
            
                    {
                            data: 'bet_amount',
                        "searchable": false

                    },
                    {
                            data: 'total_bets',
                                    "searchable": false
                    },
                    {
                            data: 'amount_won',
                                     "searchable": false
                    },
                    {
                            data: 'profit',
                                    "searchable": false

                    },
                    {
                            data: 'rtp',
                                    "searchable": false

                    },            
                   
                    //{data:null}
                 
                ],
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    render: function(data) {
                        return `
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="${data}" />
                                </div>`;
                    }
                }, ],


            });

        }


        $(document).on('change', '.filter', function(e) {
            e.preventDefault();
            table.ajax.reload();

        });


        $('#clear').click(function() {
            location.reload();
        });

        // Handle search input change event
        $('#search').on('input', function() {
            table.search(this.value).draw();
        });
    });
</script>
