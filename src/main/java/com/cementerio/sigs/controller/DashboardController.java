package com.cementerio.sigs.controller;

import com.cementerio.sigs.model.Expediente;
import com.cementerio.sigs.repository.ExpedienteRepository;
import com.cementerio.sigs.repository.FallecidoRepository;
import com.cementerio.sigs.repository.SolicitudRepository;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Sort;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import java.util.List;

@Controller
public class DashboardController {

    private final ExpedienteRepository expedienteRepository;
    private final SolicitudRepository solicitudRepository;
    private final FallecidoRepository fallecidoRepository;

    public DashboardController(ExpedienteRepository expedienteRepository,
                               SolicitudRepository solicitudRepository,
                               FallecidoRepository fallecidoRepository) {
        this.expedienteRepository = expedienteRepository;
        this.solicitudRepository = solicitudRepository;
        this.fallecidoRepository = fallecidoRepository;
    }

    @GetMapping("/dashboard")
    public String dashboard(Model model) {
        model.addAttribute("totalExpedientes", expedienteRepository.count());
        model.addAttribute("totalSolicitudes", solicitudRepository.count());
        model.addAttribute("totalFallecidos", fallecidoRepository.count());

        // Obtener los últimos 5 expedientes registrados para mostrar en el panel
        List<Expediente> ultimosExpedientes = expedienteRepository.findAll(
                PageRequest.of(0, 5, Sort.by("idExpediente").descending())
        ).getContent();
        
        model.addAttribute("ultimosExpedientes", ultimosExpedientes);

        return "dashboard";
    }
}
