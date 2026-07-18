package com.cementerio.sigs.model;

import jakarta.persistence.*;
import java.time.LocalDate;

@Entity
@Table(name = "fallecido")
public class Fallecido {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_fallecido")
    private Integer idFallecido;

    @Column(name = "dni", nullable = false, unique = true, length = 8)
    private String dni;

    @Column(name = "nombres", nullable = false, length = 100)
    private String nombres;

    @Column(name = "apellidos", nullable = false, length = 100)
    private String apellidos;

    @Column(name = "fecha_nacimiento", nullable = false)
    private LocalDate fechaNacimiento;

    @Column(name = "fecha_fallecimiento", nullable = false)
    private LocalDate fechaFallecimiento;

    @Column(name = "edad", nullable = false)
    private Integer edad;

    @Column(name = "sexo", nullable = false, length = 10)
    private String sexo; // "Masculino" | "Femenino"

    // Constructors
    public Fallecido() {}

    public Fallecido(String dni, String nombres, String apellidos, LocalDate fechaNacimiento, LocalDate fechaFallecimiento, Integer edad, String sexo) {
        this.dni = dni;
        this.nombres = nombres;
        this.apellidos = apellidos;
        this.fechaNacimiento = fechaNacimiento;
        this.fechaFallecimiento = fechaFallecimiento;
        this.edad = edad;
        this.sexo = sexo;
    }

    // Getters and Setters
    public Integer getIdFallecido() {
        return idFallecido;
    }

    public void setIdFallecido(Integer idFallecido) {
        this.idFallecido = idFallecido;
    }

    public String getDni() {
        return dni;
    }

    public void setDni(String dni) {
        this.dni = dni;
    }

    public String getNombres() {
        return nombres;
    }

    public void setNombres(String nombres) {
        this.nombres = nombres;
    }

    public String getApellidos() {
        return apellidos;
    }

    public void setApellidos(String apellidos) {
        this.apellidos = apellidos;
    }

    public LocalDate getFechaNacimiento() {
        return fechaNacimiento;
    }

    public void setFechaNacimiento(LocalDate fechaNacimiento) {
        this.fechaNacimiento = fechaNacimiento;
    }

    public LocalDate getFechaFallecimiento() {
        return fechaFallecimiento;
    }

    public void setFechaFallecimiento(LocalDate fechaFallecimiento) {
        this.fechaFallecimiento = fechaFallecimiento;
    }

    public Integer getEdad() {
        return edad;
    }

    public void setEdad(Integer edad) {
        this.edad = edad;
    }

    public String getSexo() {
        return sexo;
    }

    public void setSexo(String sexo) {
        this.sexo = sexo;
    }
}
