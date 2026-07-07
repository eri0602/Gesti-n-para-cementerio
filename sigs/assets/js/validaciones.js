// Validaciones en cliente
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            let valid = true;
            
            // Validar campos DNI
            const dnis = form.querySelectorAll('input[name="dni"], input[name="dni_deudo"]');
            dnis.forEach(dni => {
                if (dni.value && !/^\d{8}$/.test(dni.value)) {
                    alert('El DNI debe tener exactamente 8 dígitos.');
                    valid = false;
                }
            });

            // Validar fechas de nacimiento y fallecimiento si existen ambas
            const fnac = form.querySelector('input[name="fecha_nacimiento"]');
            const ffall = form.querySelector('input[name="fecha_fallecimiento"]');
            if (fnac && ffall && fnac.value && ffall.value) {
                if (new Date(ffall.value) < new Date(fnac.value)) {
                    alert('La fecha de defunción no puede ser anterior a la fecha de nacimiento.');
                    valid = false;
                }
            }
            
            if (!valid) {
                event.preventDefault();
            }
        });
    });
});
