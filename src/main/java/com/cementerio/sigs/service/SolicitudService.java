package com.cementerio.sigs.service;

import com.cementerio.sigs.model.SolicitudSepultura;
import com.cementerio.sigs.repository.SolicitudRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.time.LocalDate;
import java.util.Optional;

@Service
public class SolicitudService {

    private final SolicitudRepository solicitudRepository;

    public SolicitudService(SolicitudRepository solicitudRepository) {
        this.solicitudRepository = solicitudRepository;
    }

    @Transactional
    public SolicitudSepultura registrar(SolicitudSepultura solicitud) {
        solicitud.setFechaSolicitud(LocalDate.now());
        return solicitudRepository.save(solicitud);
    }

    public Optional<SolicitudSepultura> obtenerPorId(Integer id) {
        return solicitudRepository.findById(id);
    }
}
