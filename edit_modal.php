<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit IP Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit-id">

                    <!-- IP Address -->
                    <div class="mb-3">
                        <label for="edit-ip">IP Address</label>
                        <input type="text" name="ip" id="edit-ip" class="form-control" required>
                    </div>

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="edit-name">Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>

                    <!-- Excluded Ports -->
                    <div class="mb-3">
                        <label>Excluded Ports</label>
                        <div id="edit-port-fields-wrapper"></div>
                        <button type="button" class="btn btn-success add-port-field">+ Add Port</button>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Required JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./scripts_modal.js"></script>
