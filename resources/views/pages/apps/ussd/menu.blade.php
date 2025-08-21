<x-default-layout>

    @section('title')
        USSD REQUESTS
    @endsection


    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-12">
            <!--begin::Card title-->
            <div class="card-title">
         

                <div class="row  d-flex">
    <div class="col-4">
        <input type="text" id="phone_number" class="form-control" placeholder="Search by Mobile Number">
    </div>
    <div class="col-4">
        <input type="text" id="date_range" class="form-control" placeholder="Select Date Range">
    </div>
    <div class="col-4">
            <button id="filter" class="btn btn-primary">Search</button>
            <button id="reset" class="btn btn-secondary flex-fill">Reset</button>
        </div>
        <input type="hidden" id="start_date">
<input type="hidden" id="end_date">

</div>

            </div>
            <!--begin::Card title-->


        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <!--begin::Table-->
            <div class="table-responsive">
                {{ $dataTable->table() }}
            </div>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>

    <!-- Global Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="statusForm" method="POST">
        @csrf
        @method('PATCH')

        <div class="modal-header">
          <h5 class="modal-title" id="statusModalTitle">Update Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="statusField" name="status">
          <input type="hidden" id="uuid" name="uuid">

          <div class="mb-3" id="reasonBox">
            <label for="reason" class="form-label">Reason</label>
            <textarea class="form-control" id="reason" name="reason" required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="statusSubmitBtn" class="btn"></button>
        </div>
      </form>
    </div>
  </div>
</div>



    @push('scripts')
        {{ $dataTable->scripts() }}


<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
let view_route = "{{ route('menu-requests.show', ':uuid') }}";

$(document).ready(function () {


// Handle View button click
$(document).on('click', '.view-menu', function (){
    let uuid = $(this).data('id');
    let url = view_route.replace(':uuid', $(this).data('id'));
    $.get(url, function (data) {
        Swal.fire({
            title: 'Menu Details',
            html: `
                <table class="table table-striped table-bordered table-hover w-100 text-left">
                    <tr>
                        <th style="width: 30%;">MENU</th>
                        <td>${data.menu}</td>
                    </tr>
                    <tr>
                        <th>mobile number</th>
                        <td>${data.mobile_number}</td>
                    </tr>
                    <tr>
                        <th>request</th>
                        <td>${data.request}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>${data.response}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>${data.created_at}</td>
                    </tr>
                </table>
            `,
          //  icon: 'info',
            width: '900px', // Large modal
            heightAuto: false,  // disable auto shrinking
            confirmButtonText: 'Close',
            customClass: {
                popup: 'swal-wide'
            }
        });
    });
});

});

            document.addEventListener('livewire:init', function () {
                Livewire.on('success', function () {
                    window.LaravelDataTables['profiles-table'].ajax.reload();
                });
            });

        </script>
    @endpush

</x-default-layout>
