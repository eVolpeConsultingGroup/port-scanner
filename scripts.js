document.addEventListener('DOMContentLoaded', () => {
    const PORT_PATTERN = /^(?:[1-9]\d{0,3}|[1-5]\d{4}|6[0-4]\d{3}|65[0-4]\d{2}|655[0-2]\d|6553[0-5])$/;

    // Funkcja walidująca
    const validatePort = (input) => {
        const value = input.value.trim();
        const isValid = value === '' || PORT_PATTERN.test(value);
        
        input.classList.toggle('is-invalid', !isValid);
        input.setCustomValidity(isValid ? '' : 'Invalid port (1-65535)');
        return isValid;
    };

    // Obsługa głównego formularza
    const mainWrapper = document.getElementById('port-fields-wrapper');
    if (mainWrapper) {
        mainWrapper.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-port-field')) {
                const newField = createPortField();
                mainWrapper.appendChild(newField);
            }
            if (e.target.classList.contains('remove-port-field')) {
                e.target.closest('.input-group').remove();
            }
        });
    }

    // Obsługa modala edycji
    const editModal = document.getElementById('editModal');
    if (editModal) {
        const editWrapper = document.getElementById('edit-port-fields-wrapper');

        editModal.addEventListener('show.bs.modal', (e) => {
            const button = e.relatedTarget;
            const excludedPorts = JSON.parse(button.dataset.excludedPorts || '[]');
            
            // Wypełnianie pól
            document.getElementById('edit-id').value = button.dataset.id;
            document.getElementById('edit-ip').value = button.dataset.ip;
            document.getElementById('edit-name').value = button.dataset.name;

            // Resetowanie portów
            editWrapper.innerHTML = '';
            excludedPorts.forEach(port => {
                editWrapper.appendChild(createPortField(port));
            });
        });

        editModal.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-port-field')) {
                editWrapper.appendChild(createPortField());
            }
            if (e.target.classList.contains('remove-port-field')) {
                e.target.closest('.input-group').remove();
            }
        });
    }

    // Funkcja pomocnicza
    function createPortField(value = '') {
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="text" 
                   name="excluded_ports[]" 
                   value="${value}"
                   class="form-control"
                   pattern="${PORT_PATTERN.source}"
                   title="Port must be 1-65535"
                   required>
            <button type="button" class="btn btn-danger remove-port-field">-</button>
            <div class="invalid-feedback">Invalid port number</div>`;
        return div;
    }

    // Globalna walidacja
    document.addEventListener('input', (e) => {
        if (e.target.matches('input[name="excluded_ports[]"]')) {
            validatePort(e.target);
        }
    });
});
