package com.cementerio.sigs.controller;

import com.cementerio.sigs.model.ComprobanteRegistro;
import com.cementerio.sigs.model.Expediente;
import com.cementerio.sigs.model.Fallecido;
import com.cementerio.sigs.model.SolicitudSepultura;
import com.cementerio.sigs.service.ExpedienteService;
import com.cementerio.sigs.service.SolicitudService;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

@Controller
@RequestMapping("/expediente")
public class ExpedienteController {

    private final ExpedienteService expedienteService;
    private final SolicitudService solicitudService;

    public ExpedienteController(ExpedienteService expedienteService, SolicitudService solicitudService) {
        this.expedienteService = expedienteService;
        this.solicitudService = solicitudService;
    }

    @GetMapping("/nuevo")
    public String nuevoForm(@RequestParam("idSolicitud") Integer idSolicitud, Model model) {
        SolicitudSepultura solicitud = solicitudService.obtenerPorId(idSolicitud)
                .orElseThrow(() -> new IllegalArgumentException("La solicitud asociada no existe"));

        model.addAttribute("solicitud", solicitud);
        
        if (!model.containsAttribute("expediente")) {
            Expediente expediente = new Expediente();
            expediente.setFallecido(new Fallecido());
            // Generar número de expediente sugerido
            LocalDate hoy = LocalDate.now();
            expediente.setNumeroExpediente("EXP-" + hoy.getYear() + "-");
            model.addAttribute("expediente", expediente);
        }
        
        return "expediente_form";
    }

    @PostMapping("/nuevo")
    public String guardarExpediente(@RequestParam("idSolicitud") Integer idSolicitud,
                                    @ModelAttribute("expediente") Expediente expediente,
                                    BindingResult result,
                                    RedirectAttributes redirectAttributes) {

        Fallecido fal = expediente.getFallecido();
        List<String> errores = new ArrayList<>();

        // Validaciones del fallecido
        if (fal.getDni() == null || !fal.getDni().matches("^\\d{8}$")) {
            errores.add("El DNI del fallecido debe tener exactamente 8 dígitos.");
        } else if (expedienteService.existeDni(fal.getDni())) {
            errores.add("Ya existe un expediente registrado con este DNI. Verifique con el Técnico Informático si considera que se trata de un error.");
        }

        if (fal.getNombres() == null || !fal.getNombres().trim().matches("^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$")) {
            errores.add("El nombre del fallecido sólo debe contener letras y espacios.");
        }

        if (fal.getApellidos() == null || !fal.getApellidos().trim().matches("^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$")) {
            errores.add("El apellido del fallecido sólo debe contener letras y espacios.");
        }

        if (fal.getFechaNacimiento() == null || fal.getFechaFallecimiento() == null) {
            errores.add("Las fechas de nacimiento y fallecimiento son obligatorias.");
        } else {
            if (fal.getFechaNacimiento().isAfter(LocalDate.now())) {
                errores.add("La fecha de nacimiento no puede ser una fecha futura.");
            }
            if (fal.getFechaFallecimiento().isAfter(LocalDate.now())) {
                errores.add("La fecha de defunción no puede ser una fecha futura.");
            }
            if (fal.getFechaFallecimiento().isBefore(fal.getFechaNacimiento())) {
                errores.add("La fecha de defunción no puede ser anterior a la de nacimiento.");
            }
        }

        if (fal.getSexo() == null || fal.getSexo().trim().isEmpty() || 
            (!fal.getSexo().equals("Masculino") && !fal.getSexo().equals("Femenino"))) {
            errores.add("Debe seleccionar un sexo válido (Masculino o Femenino).");
        }

        // Validaciones del expediente
        if (expediente.getNumeroExpediente() == null || expediente.getNumeroExpediente().trim().isEmpty()) {
            errores.add("El número de expediente es obligatorio.");
        } else if (expedienteService.existeNumeroExpediente(expediente.getNumeroExpediente().trim())) {
            errores.add("Ya existe un expediente registrado con este número de expediente.");
        }

        if (!errores.isEmpty()) {
            redirectAttributes.addFlashAttribute("errores", errores);
            redirectAttributes.addFlashAttribute("expediente", expediente);
            return "redirect:/expediente/nuevo?idSolicitud=" + idSolicitud;
        }

        try {
            Expediente guardado = expedienteService.registrar(expediente, idSolicitud);
            return "redirect:/expediente/comprobante?id=" + guardado.getIdExpediente();
        } catch (Exception e) {
            errores.add("Ocurrió un error inesperado al registrar el expediente: " + e.getMessage());
            redirectAttributes.addFlashAttribute("errores", errores);
            redirectAttributes.addFlashAttribute("expediente", expediente);
            return "redirect:/expediente/nuevo?idSolicitud=" + idSolicitud;
        }
    }

    @GetMapping("/comprobante")
    public String verComprobante(@RequestParam("id") Integer idExpediente, Model model) {
        Expediente expediente = expedienteService.obtenerPorId(idExpediente)
                .orElseThrow(() -> new IllegalArgumentException("Expediente no encontrado"));
        ComprobanteRegistro comprobante = expedienteService.obtenerComprobantePorExpedienteId(idExpediente)
                .orElseThrow(() -> new IllegalArgumentException("Comprobante no emitido para este expediente"));

        model.addAttribute("expediente", expediente);
        model.addAttribute("comprobante", comprobante);
        return "comprobante_ver";
    }

    @GetMapping("/detalle")
    public String verDetalle(@RequestParam("id") Integer idExpediente, Model model) {
        Expediente expediente = expedienteService.obtenerPorId(idExpediente)
                .orElseThrow(() -> new IllegalArgumentException("Expediente no encontrado"));
        ComprobanteRegistro comprobante = expedienteService.obtenerComprobantePorExpedienteId(idExpediente).orElse(null);

        model.addAttribute("expediente", expediente);
        model.addAttribute("comprobante", comprobante);
        return "expediente_detalle";
    }

    @GetMapping("/editar")
    public String editarForm(@RequestParam("id") Integer idExpediente, Model model) {
        Expediente expediente = expedienteService.obtenerPorId(idExpediente)
                .orElseThrow(() -> new IllegalArgumentException("Expediente no encontrado"));
        model.addAttribute("expediente", expediente);
        return "expediente_editar";
    }

    @PostMapping("/editar")
    public String guardarEdicion(@RequestParam("id") Integer idExpediente,
                                 @ModelAttribute("expediente") Expediente datosNuevos,
                                 RedirectAttributes redirectAttributes) {
        List<String> errores = new ArrayList<>();
        Fallecido fal = datosNuevos.getFallecido();
        SolicitudSepultura sol = datosNuevos.getSolicitud();

        // Validaciones
        if (fal.getDni() == null || !fal.getDni().matches("^\\d{8}$")) {
            errores.add("El DNI del fallecido debe tener exactamente 8 dígitos.");
        }
        if (fal.getNombres() == null || !fal.getNombres().trim().matches("^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$")) {
            errores.add("El nombre del fallecido sólo debe contener letras.");
        }
        if (fal.getApellidos() == null || !fal.getApellidos().trim().matches("^[A-Za-zÁÉÍÓÚÑáéíóúñ ]+$")) {
            errores.add("El apellido del fallecido sólo debe contener letras.");
        }
        if (fal.getFechaNacimiento() == null || fal.getFechaFallecimiento() == null) {
            errores.add("Las fechas de nacimiento y fallecimiento son obligatorias.");
        } else if (fal.getFechaFallecimiento().isBefore(fal.getFechaNacimiento())) {
            errores.add("La fecha de defunción no puede ser anterior a la de nacimiento.");
        }
        if (sol.getDniDeudo() == null || !sol.getDniDeudo().matches("^\\d{8}$")) {
            errores.add("El DNI del deudo debe contener exactamente 8 dígitos.");
        }
        if (sol.getNombreDeudo() == null || sol.getNombreDeudo().trim().isEmpty()) {
            errores.add("El nombre del deudo es obligatorio.");
        }
        if (sol.getParentesco() == null || sol.getParentesco().trim().isEmpty()) {
            errores.add("El parentesco es obligatorio.");
        }
        if (datosNuevos.getNumeroExpediente() == null || datosNuevos.getNumeroExpediente().trim().isEmpty()) {
            errores.add("El número de expediente es obligatorio.");
        }

        if (!errores.isEmpty()) {
            redirectAttributes.addFlashAttribute("errores", errores);
            return "redirect:/expediente/editar?id=" + idExpediente;
        }

        try {
            expedienteService.actualizar(idExpediente, datosNuevos);
            redirectAttributes.addFlashAttribute("mensajeExito", "Expediente actualizado exitosamente.");
            return "redirect:/expediente/detalle?id=" + idExpediente;
        } catch (Exception e) {
            errores.add("Ocurrió un error al actualizar: " + e.getMessage());
            redirectAttributes.addFlashAttribute("errores", errores);
            return "redirect:/expediente/editar?id=" + idExpediente;
        }
    }
}
