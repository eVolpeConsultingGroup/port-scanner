document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById('editModal');
    const portsWrapper = document.getElementById('edit-port-fields-wrapper-modal');

    // Wyrażenie regularne dla portów (1-65535)
    const PORT_PATTERN = /^([1-9]\d{0,3}|[1-5]\d{4}|6[0-4]\d{3}|65[0-4]\d{2}|655[0-2]\d|6553[0-5])$/;

    // Funkcja walidująca pojedyncze pole portu
    const validatePort = (input) => {
        const value = input.value.trim();
        const isValid = value === '' || PORT_PATTERN.test(value);

        if (isValid) {
            input.classList.remove('is-invalid');
            input.setCustomValidity('');
        } else {
            input.classList.add('is-invalid');
            input.setCustomValidity('Port must be a number between 1 and 65535.');
        }

        return isValid;
    };

    // Obsługa zdarzenia "input" dla dynamicznych pól portów
    portsWrapper.addEventListener('input', (event) => {
        if (event.target.tagName === 'INPUT' && event.target.name === 'excluded_ports[]') {
            validatePort(event.target);
        }
    });

    // Dodawanie nowego pola portu
    editModal.addEventListener('click', (event) => {
        if (event.target.classList.contains('add-port-field-modal')) {
            const newField = document.createElement('div');
            newField.className = 'input-group mb-2';
            newField.innerHTML = `
                <input type="text" 
                       name="excluded_ports[]" 
                       class="form-control"
                       placeholder="Enter port (1-65535)">
                <button type="button" class="btn btn-danger remove-port-field-modal">-</button>
                <div class="invalid-feedback">Port must be a number between 1 and 65535.</div>`;
            portsWrapper.appendChild(newField);
        }

        // Usuwanie pola portu
        if (event.target.classList.contains('remove-port-field-modal')) {
            event.target.closest('.input-group').remove();
        }
    });

    // Walidacja przed wysłaniem formularza
    editModal.querySelector('form').addEventListener('submit', (event) => {
        let isValid = true;

        portsWrapper.querySelectorAll('input[name="excluded_ports[]"]').forEach((input) => {
            if (!validatePort(input)) isValid = false;
        });

        if (!isValid) {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    // Inicjalizacja modala i wypełnienie istniejących portów
    editModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        const excludedPorts = JSON.parse(button.dataset.excludedPorts || '[]');
        
        // Resetowanie istniejących pól
        portsWrapper.innerHTML = '';
        
        // Dodanie pól dla istniejących portów
        excludedPorts.forEach((port) => {
            const field = document.createElement('div');
            field.className = 'input-group mb-2';
            field.innerHTML = `
                <input type="text" 
                       name="excluded_ports[]" 
                       value="${port}" 
                       class="form-control"
                       placeholder="Enter port (1-65535)">
                <button type="button" class="btn btn-danger remove-port-field-modal">-</button>
                <div class="invalid-feedback">Port must be a number between 1 and 65535.</div>`;
            portsWrapper.appendChild(field);
        });
    });
});
