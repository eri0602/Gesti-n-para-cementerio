package com.cementerio.sigs.repository;

import com.cementerio.sigs.model.ComprobanteRegistro;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.Optional;

@Repository
public interface ComprobanteRepository extends JpaRepository<ComprobanteRegistro, Integer> {
    Optional<ComprobanteRegistro> findByExpedienteIdExpediente(Integer idExpediente);
}
