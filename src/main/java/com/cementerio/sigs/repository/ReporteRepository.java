package com.cementerio.sigs.repository;

import com.cementerio.sigs.model.ReporteEstadistico;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;
import java.time.LocalDate;
import java.util.List;

@Repository
public interface ReporteRepository extends JpaRepository<ReporteEstadistico, Integer> {

    @Query(value = "SELECT sexo AS sexo, COUNT(*) AS total FROM fallecido " +
                   "WHERE fecha_fallecimiento BETWEEN :desde AND :hasta " +
                   "GROUP BY sexo", nativeQuery = true)
    List<SexoEstadistica> porSexo(@Param("desde") LocalDate desde, @Param("hasta") LocalDate hasta);

    @Query(value = "SELECT " +
                   "CASE " +
                   "  WHEN edad BETWEEN 0 AND 17 THEN 'Menor de edad' " +
                   "  WHEN edad BETWEEN 18 AND 29 THEN 'Joven adulto' " +
                   "  WHEN edad BETWEEN 30 AND 59 THEN 'Adulto' " +
                   "  ELSE 'Adulto mayor' " +
                   "END AS grupoEtario, " +
                   "COUNT(*) AS total " +
                   "FROM fallecido " +
                   "WHERE fecha_fallecimiento BETWEEN :desde AND :hasta " +
                   "GROUP BY " +
                   "CASE " +
                   "  WHEN edad BETWEEN 0 AND 17 THEN 'Menor de edad' " +
                   "  WHEN edad BETWEEN 18 AND 29 THEN 'Joven adulto' " +
                   "  WHEN edad BETWEEN 30 AND 59 THEN 'Adulto' " +
                   "  ELSE 'Adulto mayor' " +
                   "END", nativeQuery = true)
    List<GrupoEtarioEstadistica> porGrupoEtario(@Param("desde") LocalDate desde, @Param("hasta") LocalDate hasta);

    // Interfaces para Proyección JPA
    interface SexoEstadistica {
        String getSexo();
        Long getTotal();
    }

    interface GrupoEtarioEstadistica {
        String getGrupoEtario();
        Long getTotal();
    }
}
