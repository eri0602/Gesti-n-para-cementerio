package com.cementerio.sigs.model;

import jakarta.persistence.*;
import java.time.LocalDate;

@Entity
@Table(name = "reporte_estadistico")
public class ReporteEstadistico {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_reporte")
    private Integer idReporte;

    @Column(name = "tipo_reporte", nullable = false, length = 30)
    private String tipoReporte; // e.g. "Trimestral", "Anual", or custom dates

    @Column(name = "fecha_generacion", nullable = false)
    private LocalDate fechaGeneracion;

    @ManyToOne(fetch = FetchType.EAGER)
    @JoinColumn(name = "generado_por", referencedColumnName = "id_usuario", nullable = false)
    private Usuario generadoPor;

    // Constructors
    public ReporteEstadistico() {}

    public ReporteEstadistico(String tipoReporte, LocalDate fechaGeneracion, Usuario generadoPor) {
        this.tipoReporte = tipoReporte;
        this.fechaGeneracion = fechaGeneracion;
        this.generadoPor = generadoPor;
    }

    // Getters and Setters
    public Integer getIdReporte() {
        return idReporte;
    }

    public void setIdReporte(Integer idReporte) {
        this.idReporte = idReporte;
    }

    public String getTipoReporte() {
        return tipoReporte;
    }

    public void setTipoReporte(String tipoReporte) {
        this.tipoReporte = tipoReporte;
    }

    public LocalDate getFechaGeneracion() {
        return fechaGeneracion;
    }

    public void setFechaGeneracion(LocalDate fechaGeneracion) {
        this.fechaGeneracion = fechaGeneracion;
    }

    public Usuario getGeneradoPor() {
        return generadoPor;
    }

    public void setGeneradoPor(Usuario generadoPor) {
        this.generadoPor = generadoPor;
    }
}
