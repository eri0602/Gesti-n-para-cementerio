// ── Sidebar toggle (móvil) ──
const sidebar   = document.getElementById('sidebar');
const overlay   = document.getElementById('sidebarOverlay');
const menuBtn   = document.getElementById('btnMenuToggle');

function openSidebar() {
  sidebar && sidebar.classList.add('open');
  overlay && (overlay.style.display = 'block');
}
function closeSidebar() {
  sidebar && sidebar.classList.remove('open');
  overlay && (overlay.style.display = 'none');
}
if (menuBtn)   menuBtn.addEventListener('click', openSidebar);
if (overlay)   overlay.addEventListener('click', closeSidebar);

// ── Marcar link activo en el sidebar ──
document.querySelectorAll('.sidebar-link').forEach(link => {
  if (link.href === window.location.href ||
      (link.href && window.location.href.startsWith(link.href + '?'))) {
    link.classList.add('active');
  }
});

// ── Validaciones client-side (complementarias) ──
document.addEventListener('DOMContentLoaded', function () {

  // Validación de formularios
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', function (e) {
      let valid = true;

      // DNI: 8 dígitos
      form.querySelectorAll('[data-validate-dni]').forEach(input => {
        const val = input.value.trim();
        if (val && !/^\d{8}$/.test(val)) {
          markInvalid(input, 'El DNI debe tener exactamente 8 dígitos numéricos.');
          valid = false;
        } else {
          markValid(input);
        }
      });

      // Fechas: fallecimiento >= nacimiento
      const fnac  = form.querySelector('[name="fallecido.fechaNacimiento"]');
      const ffall = form.querySelector('[name="fallecido.fechaFallecimiento"]');
      if (fnac && ffall && fnac.value && ffall.value) {
        if (new Date(ffall.value) < new Date(fnac.value)) {
          markInvalid(ffall, 'La fecha de defunción no puede ser anterior a la fecha de nacimiento.');
          valid = false;
        } else {
          markValid(ffall);
        }
      }

      // Solo letras en nombres / apellidos
      form.querySelectorAll('[data-validate-alpha]').forEach(input => {
        const val = input.value.trim();
        if (val && !/^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$/.test(val)) {
          markInvalid(input, 'Este campo sólo acepta letras y espacios.');
          valid = false;
        } else {
          markValid(input);
        }
      });

      if (!valid) e.preventDefault();
    });
  });

  function markInvalid(el, msg) {
    el.classList.add('is-invalid');
    let fb = el.nextElementSibling;
    if (!fb || !fb.classList.contains('invalid-feedback')) {
      fb = document.createElement('div');
      fb.className = 'invalid-feedback';
      el.parentNode.insertBefore(fb, el.nextSibling);
    }
    fb.textContent = msg;
  }

  function markValid(el) {
    el.classList.remove('is-invalid');
    const fb = el.nextElementSibling;
    if (fb && fb.classList.contains('invalid-feedback')) fb.remove();
  }
});
