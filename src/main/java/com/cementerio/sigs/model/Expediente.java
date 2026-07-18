package com.cementerio.sigs.model;

import jakarta.persistence.*;
import java.time.LocalDate;

@Entity
@Table(name = "expediente")
public class Expediente {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_expediente")
    private Integer idExpediente;

    @Column(name = "numero_expediente", nullable = false, unique = true, length = 20)
    private String numeroExpediente;

    @OneToOne(cascade = CascadeType.ALL, fetch = FetchType.EAGER)
    @JoinColumn(name = "id_fallecido", referencedColumnName = "id_fallecido", nullable = false)
    private Fallecido fallecido;

    @OneToOne(fetch = FetchType.EAGER)
    @JoinColumn(name = "id_solicitud", referencedColumnName = "id_solicitud", nullable = false)
    private SolicitudSepultura solicitud;

    @Column(name = "fecha_registro", nullable = false)
    private LocalDate fechaRegistro;

    // Constructors
    public Expediente() {}

    public Expediente(String numeroExpediente, Fallecido fallecido, SolicitudSepultura solicitud, LocalDate fechaRegistro) {
        this.numeroExpediente = numeroExpediente;
        this.fallecido = fallecido;
        this.solicitud = solicitud;
        this.fechaRegistro = fechaRegistro;
    }

    // Getters and Setters
    public Integer getIdExpediente() {
        return idExpediente;
    }

    public void setIdExpediente(Integer idExpediente) {
        this.idExpediente = idExpediente;
    }

    public String getNumeroExpediente() {
        return numeroExpediente;
    }

    public void setNumeroExpediente(String numeroExpediente) {
        this.numeroExpediente = numeroExpediente;
    }

    public Fallecido getFallecido() {
        return fallecido;
    }

    public void setFallecido(Fallecido fallecido) {
        this.fallecido = fallecido;
    }

    public SolicitudSepultura getSolicitud() {
        return solicitud;
    }

    public void setSolicitud(SolicitudSepultura solicitud) {
        this.solicitud = solicitud;
    }

    public LocalDate getFechaRegistro() {
        return fechaRegistro;
    }

    public void setFechaRegistro(LocalDate fechaRegistro) {
        this.fechaRegistro = fechaRegistro;
    }
}
