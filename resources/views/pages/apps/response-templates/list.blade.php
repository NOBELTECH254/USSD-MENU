<x-default-layout>

    @section('title')
        Response Templates
    @endsection


    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-12">
            <!--begin::Card title-->
            <div class="card-title">
         


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
          <h5 class="modal-title" id="statusModalTitle">Response Template</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="uuid" name="uuid">
	 <span id="template-name" name = "template-name"> </span>
          <div class="mb-3" id="reasonBox">
            <label for="reason" class="form-label">Template</label>
            <textarea class="form-control" id="template" name="template" required></textarea>
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



<script>
 
$(document).ready(function () {
    let update_route = "{{ route('response-templates.update-status', ':uuid') }}";

 $(document).on('click', '.update-status', function(e) {
        e.preventDefault();
        let id   = $(this).data('id');
        let templatename = $(this).data('templatename');
        let template   = $(this).data('template');
        // Fill modal
        $('#uuid').val(id);
      //  $('#template-name').val(templatename);
        $('#template').val(template);
    
        $('#statusModalTitle').text("Edit " + templatename + " Template");

            $('#statusSubmitBtn').removeClass().addClass('btn btn-success').text('Update');
            $('#reason').attr('required', true);
        
        let modal = new bootstrap.Modal(document.getElementById('statusModal'));
        modal.show();
    });
 $('#statusForm').submit(function(e) {
        e.preventDefault();
        let uuid   = $('#uuid').val();
        let template = $('#template').val();
let url = update_route.replace(':uuid', uuid);
        $.ajax({
            url: url,
            type: 'PATCH', // must match the route
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                template: template
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
    });
 
$(document).ready(function () {
            document.addEventListener('livewire:init', function () {
                Livewire.on('success', function () {
                    window.LaravelDataTables['response-templates-table'].ajax.reload();
                });
            });
        });
        </script>
    @endpush

</x-default-layout>
