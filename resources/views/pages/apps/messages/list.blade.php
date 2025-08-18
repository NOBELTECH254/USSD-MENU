<x-default-layout>

    @section('title')
        Sent Messages
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
$(function() {

  
$(document).ready(function () {

    let start_date = '';
    let end_date = '';
    let updateStatusRoute = "{{ route('profiles.update-status', ':uuid') }}";
    let reset_url = "{{ route('profiles.reset', ':uuid') }}";

    $('#date_range').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear' },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });



    $(document).on('click', '.update-status', function(e) {
        e.preventDefault();

        let profile_id   = $(this).data('id');
        let newStatus = $(this).data('status');
        let action   = $(this).data('action');
console.log(profile_id);
        // Fill modal
        $('#uuid').val(profile_id);
        $('#statusField').val(newStatus);
        $('#statusModalTitle').text(action + " User");

        if(newStatus === "INACTIVE") {
            $('#statusSubmitBtn').removeClass().addClass('btn btn-danger').text('Deactivate');
            $('#reasonBox').show();
            $('#reasonBox').val("");
            $('#reason').attr('required', true);
        } else {
            $('#statusSubmitBtn').removeClass().addClass('btn btn-success').text('Activate');
            $('#reasonBox').show();
            $('#reasonBox').val("");
            $('#reason').attr('required', true);
        }

        let modal = new bootstrap.Modal(document.getElementById('statusModal'));
        modal.show();
    });


 $('#statusForm').submit(function(e) {
        e.preventDefault();

        let uuid   = $('#uuid').val();
        let status = $('#statusField').val();
        let reason = $('#reason').val();
let url = updateStatusRoute.replace(':uuid', uuid);
        $.ajax({
            url: url,
            type: 'PATCH', // must match the route
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                status: status,
                reason: reason
            },
            success: function(res) {
                $('#statusModal').modal('hide');
                alert(res.message || 'Status updated successfully');
                location.reload();
            },
            error: function(err) {
                let message = "An error occurred";
if (err.responseJSON) {
    if (err.responseJSON.message) {
        message = err.responseJSON.message;
    }
    if (err.responseJSON.errors) {
        let details = Object.values(err.responseJSON.errors)
                             .map(e => e.join(', '))
                             .join('\n');
        message += ":\n" + details;
    }
} else if (err.responseText) {
    message = err.responseText;
}
alert(message);
            }
        });
    });   

$(document).on('click', '.reset-password', function(e) {
    e.preventDefault(); // Prevent default anchor navigation

    let uuid = $(this).data('id');
    reset_url
    let url = reset_url.replace(':uuid', uuid);

    Swal.fire({
        title: 'Are you sure?',
        text: "This will reset the user's PIN !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reset it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed){
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    Swal.fire({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // Reload page after success
                    });
                },
                error: function(err){
                    let message = "An error occurred";

                    if(err.responseJSON){
                        message = err.responseJSON.message || message;
                        if(err.responseJSON.errors){
                            let details = Object.values(err.responseJSON.errors)
                                                 .map(e => e.join(', '))
                                                 .join('\n');
                            message += ":\n" + details;
                        }
                    } else if(err.responseText){
                        message = err.responseText;
                    }

                    Swal.fire({
                        title: 'Error',
                        text: message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
});

$('#date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
      //  $('#profiles-table').DataTable().draw();
      $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
        $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
    
        $(this).val($('#start_date').val() + ' - ' + $('#end_date').val());
    });

    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#start_date').val('');
        $('#end_date').val('');
    //    $('#profiles-table').DataTable().draw();
    });
    


        $('#filter').click(function () {
            window.LaravelDataTables['profiles-table'].draw();
        });
        $('#phone_number').on('keyup', function (e) {
            if (e.key === "Enter") {
                // If Enter is pressed, run search
                window.LaravelDataTables['profiles-table'].draw();
            }
        });


        $('#reset').click(function () {
            $('#phone_number').val('');
            $('#date_range').val('');
        $('#start_date').val('');
        $('#end_date').val('');
            window.LaravelDataTables['profiles-table'].draw();
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
