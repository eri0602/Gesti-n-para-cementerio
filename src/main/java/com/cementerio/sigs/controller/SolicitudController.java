package com.cementerio.sigs.controller;

import com.cementerio.sigs.model.SolicitudSepultura;
import com.cementerio.sigs.service.SolicitudService;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

@Controller
@RequestMapping("/solicitud")
public class SolicitudController {

    private final SolicitudService solicitudService;

    public SolicitudController(SolicitudService solicitudService) {
        this.solicitudService = solicitudService;
    }

    @GetMapping("/nueva")
    public String nuevaForm(Model model) {
        if (!model.containsAttribute("solicitud")) {
            model.addAttribute("solicitud", new SolicitudSepultura());
        }
        return "solicitud_form";
    }

    @PostMapping("/nueva")
    public String guardarSolicitud(@ModelAttribute("solicitud") SolicitudSepultura solicitud,
                                   BindingResult result,
                                   RedirectAttributes redirectAttributes) {
        
        // Validaciones manuales robustas
        if (solicitud.getDniDeudo() == null || !solicitud.getDniDeudo().matches("^\\d{8}$")) {
            result.rejectValue("dniDeudo", "error.dniDeudo", "El DNI del deudo debe contener exactamente 8 dígitos.");
        }
        if (solicitud.getNombreDeudo() == null || solicitud.getNombreDeudo().trim().isEmpty()) {
            result.rejectValue("nombreDeudo", "error.nombreDeudo", "El nombre del deudo es obligatorio.");
        }
        if (solicitud.getParentesco() == null || solicitud.getParentesco().trim().isEmpty()) {
            result.rejectValue("parentesco", "error.parentesco", "El parentesco es obligatorio.");
        }

        if (result.hasErrors()) {
            redirectAttributes.addFlashAttribute("org.springframework.validation.BindingResult.solicitud", result);
            redirectAttributes.addFlashAttribute("solicitud", solicitud);
            redirectAttributes.addFlashAttribute("errorMsg", "Existen errores de validación. Por favor corrijalos.");
            return "redirect:/solicitud/nueva";
        }

        SolicitudSepultura guardada = solicitudService.registrar(solicitud);
        return "redirect:/solicitud/confirmacion?id=" + guardada.getIdSolicitud();
    }

    @GetMapping("/confirmacion")
    public String confirmacion(@RequestParam("id") Integer id, Model model) {
        SolicitudSepultura solicitud = solicitudService.obtenerPorId(id)
                .orElseThrow(() -> new IllegalArgumentException("Solicitud no encontrada"));
        model.addAttribute("solicitud", solicitud);
        return "solicitud_confirmacion"; // plantilla nueva que crearemos
    }
}
