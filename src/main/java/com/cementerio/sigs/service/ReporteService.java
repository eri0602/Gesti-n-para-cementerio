package com.cementerio.sigs.service;

import com.cementerio.sigs.model.ReporteEstadistico;
import com.cementerio.sigs.model.Usuario;
import com.cementerio.sigs.repository.ReporteRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.time.LocalDate;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Service
public class ReporteService {

    private final ReporteRepository reporteRepository;

    public ReporteService(ReporteRepository reporteRepository) {
        this.reporteRepository = reporteRepository;
    }

    public Map<String, Object> generarEstadisticas(LocalDate desde, LocalDate hasta) {
        Map<String, Object> stats = new HashMap<>();
        List<ReporteRepository.SexoEstadistica> porSexo = reporteRepository.porSexo(desde, hasta);
        List<ReporteRepository.GrupoEtarioEstadistica> porGrupoEtario = reporteRepository.porGrupoEtario(desde, hasta);
        
        stats.put("por_sexo", porSexo);
        stats.put("por_grupo_etario", porGrupoEtario);
        return stats;
    }

    @Transactional
    public void registrarGeneracion(String tipoReporte, Usuario usuario) {
        ReporteEstadistico reporte = new ReporteEstadistico(tipoReporte, LocalDate.now(), usuario);
        reporteRepository.save(reporte);
    }
}
