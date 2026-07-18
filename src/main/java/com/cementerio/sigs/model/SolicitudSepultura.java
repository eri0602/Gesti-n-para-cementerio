package com.cementerio.sigs.model;

import jakarta.persistence.*;
import java.time.LocalDate;

@Entity
@Table(name = "solicitud_sepultura")
public class SolicitudSepultura {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_solicitud")
    private Integer idSolicitud;

    @Column(name = "nombre_deudo", nullable = false, length = 100)
    private String nombreDeudo;

    @Column(name = "dni_deudo", nullable = false, length = 8)
    private String dniDeudo;

    @Column(name = "parentesco", nullable = false, length = 50)
    private String parentesco;

    @Column(name = "fecha_solicitud", nullable = false)
    private LocalDate fechaSolicitud;

    // Constructors
    public SolicitudSepultura() {}

    public SolicitudSepultura(String nombreDeudo, String dniDeudo, String parentesco, LocalDate fechaSolicitud) {
        this.nombreDeudo = nombreDeudo;
        this.dniDeudo = dniDeudo;
        this.parentesco = parentesco;
        this.fechaSolicitud = fechaSolicitud;
    }

    // Getters and Setters
    public Integer getIdSolicitud() {
        return idSolicitud;
    }

    public void setIdSolicitud(Integer idSolicitud) {
        this.idSolicitud = idSolicitud;
    }

    public String getNombreDeudo() {
        return nombreDeudo;
    }

    public void setNombreDeudo(String nombreDeudo) {
        this.nombreDeudo = nombreDeudo;
    }

    public String getDniDeudo() {
        return dniDeudo;
    }

    public void setDniDeudo(String dniDeudo) {
        this.dniDeudo = dniDeudo;
    }

    public String getParentesco() {
        return parentesco;
    }

    public void setParentesco(String parentesco) {
        this.parentesco = parentesco;
    }

    public LocalDate getFechaSolicitud() {
        return fechaSolicitud;
    }

    public void setFechaSolicitud(LocalDate fechaSolicitud) {
        this.fechaSolicitud = fechaSolicitud;
    }
}
