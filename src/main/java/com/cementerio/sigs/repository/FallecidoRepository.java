package com.cementerio.sigs.repository;

import com.cementerio.sigs.model.Fallecido;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface FallecidoRepository extends JpaRepository<Fallecido, Integer> {
    boolean existsByDni(String dni);
    Fallecido findByDni(String dni);
}
