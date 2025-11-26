// Global password visibility toggle functionality
function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Auto-enhance password fields on page load
document.addEventListener('DOMContentLoaded', function() {
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach((field, index) => {
        // Skip if already enhanced
        if (field.parentElement.classList.contains('input-group')) return;
        
        // Create unique ID if none exists
        if (!field.id) {
            field.id = 'password-field-' + index;
        }
        
        // Wrap in input group
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group';
        field.parentNode.insertBefore(wrapper, field);
        wrapper.appendChild(field);
        
        // Add toggle button
        const button = document.createElement('button');
        button.className = 'btn btn-outline-secondary';
        button.type = 'button';
        button.innerHTML = '<i class="bi bi-eye"></i>';
        button.onclick = () => togglePasswordVisibility(field.id, button);
        wrapper.appendChild(button);
    });
});