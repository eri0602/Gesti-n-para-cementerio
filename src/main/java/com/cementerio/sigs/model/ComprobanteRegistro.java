package com.cementerio.sigs.model;

import jakarta.persistence.*;
import java.time.LocalDate;

@Entity
@Table(name = "comprobante_registro")
public class ComprobanteRegistro {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_comprobante")
    private Integer idComprobante;

    @OneToOne(fetch = FetchType.EAGER)
    @JoinColumn(name = "id_expediente", referencedColumnName = "id_expediente", nullable = false)
    private Expediente expediente;

    @Column(name = "fecha_emision", nullable = false)
    private LocalDate fechaEmision;

    // Constructors
    public ComprobanteRegistro() {}

    public ComprobanteRegistro(Expediente expediente, LocalDate fechaEmision) {
        this.expediente = expediente;
        this.fechaEmision = fechaEmision;
    }

    // Getters and Setters
    public Integer getIdComprobante() {
        return idComprobante;
    }

    public void setIdComprobante(Integer idComprobante) {
        this.idComprobante = idComprobante;
    }

    public Expediente getExpediente() {
        return expediente;
    }

    public void setExpediente(Expediente expediente) {
        this.expediente = expediente;
    }

    public LocalDate getFechaEmision() {
        return fechaEmision;
    }

    public void setFechaEmision(LocalDate fechaEmision) {
        this.fechaEmision = fechaEmision;
    }
}
