package com.cementerio.sigs.controller;

import com.cementerio.sigs.model.Expediente;
import com.cementerio.sigs.service.ExpedienteService;
import org.springframework.format.annotation.DateTimeFormat;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;
import java.time.LocalDate;
import java.util.List;

@Controller
@RequestMapping("/consulta")
public class ConsultaController {

    private final ExpedienteService expedienteService;

    public ConsultaController(ExpedienteService expedienteService) {
        this.expedienteService = expedienteService;
    }

    @GetMapping
    public String consultar(
            @RequestParam(required = false) String numeroExpediente,
            @RequestParam(required = false) String dni,
            @RequestParam(required = false) String nombres,
            @RequestParam(required = false) String apellidos,
            @RequestParam(required = false) Integer edad,
            @RequestParam(required = false) String sexo,
            @RequestParam(required = false) @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate fechaRegistro,
            @RequestParam(required = false) String buscar,
            Model model) {

        model.addAttribute("numeroExpediente", numeroExpediente);
        model.addAttribute("dni", dni);
        model.addAttribute("nombres", nombres);
        model.addAttribute("apellidos", apellidos);
        model.addAttribute("edad", edad);
        model.addAttribute("sexo", sexo);
        model.addAttribute("fechaRegistro", fechaRegistro);

        if (buscar != null) {
            List<Expediente> resultados = expedienteService.buscarConFiltros(
                    numeroExpediente, dni, nombres, apellidos, edad, sexo, fechaRegistro);
            model.addAttribute("resultados", resultados);
            model.addAttribute("busquedaRealizada", true);
        }

        return "consulta";
    }
}
