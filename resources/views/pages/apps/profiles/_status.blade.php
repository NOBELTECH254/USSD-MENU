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
            <textarea class="form-control" id="reason" name="reason"></textarea>
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
