package com.cementerio.sigs.controller;

import com.cementerio.sigs.model.Usuario;
import com.cementerio.sigs.repository.UsuarioRepository;
import com.cementerio.sigs.service.ReporteService;
import org.springframework.format.annotation.DateTimeFormat;
import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import java.time.LocalDate;
import java.util.Map;

@Controller
@RequestMapping("/reportes")
public class ReporteController {

    private final ReporteService reporteService;
    private final UsuarioRepository usuarioRepository;

    public ReporteController(ReporteService reporteService, UsuarioRepository usuarioRepository) {
        this.reporteService = reporteService;
        this.usuarioRepository = usuarioRepository;
    }

    @GetMapping
    public String reportes(
            @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate desde,
            @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate hasta,
            @RequestParam(required = false) String generar,
            Authentication authentication,
            Model model) {

        // Valores por defecto: mes actual
        if (desde == null) desde = LocalDate.now().withDayOfMonth(1);
        if (hasta == null) hasta = LocalDate.now().withDayOfMonth(LocalDate.now().lengthOfMonth());

        model.addAttribute("desde", desde);
        model.addAttribute("hasta", hasta);

        if (generar != null) {
            Map<String, Object> estadisticas = reporteService.generarEstadisticas(desde, hasta);
            model.addAttribute("estadisticas", estadisticas);

            // Registrar que el admin generó este reporte
            Usuario usuario = usuarioRepository.findByUsername(authentication.getName()).orElse(null);
            if (usuario != null) {
                reporteService.registrarGeneracion(desde + " a " + hasta, usuario);
            }
        }

        return "reportes";
    }

    @GetMapping("/pdf")
    public String reportePdf(
            @RequestParam @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate desde,
            @RequestParam @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate hasta,
            Authentication authentication,
            Model model) {

        Map<String, Object> estadisticas = reporteService.generarEstadisticas(desde, hasta);
        model.addAttribute("estadisticas", estadisticas);
        model.addAttribute("desde", desde);
        model.addAttribute("hasta", hasta);
        model.addAttribute("usuarioNombre", authentication.getName());
        model.addAttribute("fechaGeneracion", LocalDate.now());

        return "reporte_pdf";
    }
}
