package com.cementerio.sigs.repository;

import com.cementerio.sigs.model.SolicitudSepultura;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface SolicitudRepository extends JpaRepository<SolicitudSepultura, Integer> {
}
