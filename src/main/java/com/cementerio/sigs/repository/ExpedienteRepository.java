package com.cementerio.sigs.repository;

import com.cementerio.sigs.model.Expediente;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;
import java.time.LocalDate;
import java.util.List;

@Repository
public interface ExpedienteRepository extends JpaRepository<Expediente, Integer> {
    
    boolean existsByNumeroExpediente(String numeroExpediente);

    @Query("SELECT e FROM Expediente e " +
           "JOIN e.fallecido f " +
           "WHERE (:numeroExpediente IS NULL OR e.numeroExpediente = :numeroExpediente) " +
           "AND (:dni IS NULL OR f.dni = :dni) " +
           "AND (:nombres IS NULL OR LOWER(f.nombres) LIKE LOWER(CONCAT('%', :nombres, '%'))) " +
           "AND (:apellidos IS NULL OR LOWER(f.apellidos) LIKE LOWER(CONCAT('%', :apellidos, '%'))) " +
           "AND (:edad IS NULL OR f.edad = :edad) " +
           "AND (:sexo IS NULL OR f.sexo = :sexo) " +
           "AND (:fechaRegistro IS NULL OR e.fechaRegistro = :fechaRegistro)")
    List<Expediente> buscarConFiltros(
        @Param("numeroExpediente") String numeroExpediente,
        @Param("dni") String dni,
        @Param("nombres") String nombres,
        @Param("apellidos") String apellidos,
        @Param("edad") Integer edad,
        @Param("sexo") String sexo,
        @Param("fechaRegistro") LocalDate fechaRegistro
    );
}
