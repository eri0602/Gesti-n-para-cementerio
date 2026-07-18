package com.cementerio.sigs.service;

import com.cementerio.sigs.model.ComprobanteRegistro;
import com.cementerio.sigs.model.Expediente;
import com.cementerio.sigs.model.Fallecido;
import com.cementerio.sigs.model.SolicitudSepultura;
import com.cementerio.sigs.repository.ComprobanteRepository;
import com.cementerio.sigs.repository.ExpedienteRepository;
import com.cementerio.sigs.repository.FallecidoRepository;
import com.cementerio.sigs.repository.SolicitudRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.time.LocalDate;
import java.time.Period;
import java.util.List;
import java.util.Optional;

@Service
public class ExpedienteService {

    private final ExpedienteRepository expedienteRepository;
    private final FallecidoRepository fallecidoRepository;
    private final SolicitudRepository solicitudRepository;
    private final ComprobanteRepository comprobanteRepository;

    public ExpedienteService(ExpedienteRepository expedienteRepository,
                             FallecidoRepository fallecidoRepository,
                             SolicitudRepository solicitudRepository,
                             ComprobanteRepository comprobanteRepository) {
        this.expedienteRepository = expedienteRepository;
        this.fallecidoRepository = fallecidoRepository;
        this.solicitudRepository = solicitudRepository;
        this.comprobanteRepository = comprobanteRepository;
    }

    public boolean existeDni(String dni) {
        return fallecidoRepository.existsByDni(dni);
    }

    public boolean existeNumeroExpediente(String numeroExpediente) {
        return expedienteRepository.existsByNumeroExpediente(numeroExpediente);
    }

    @Transactional
    public Expediente registrar(Expediente expediente, Integer idSolicitud) {
        // 1. Obtener la solicitud asociada
        SolicitudSepultura solicitud = solicitudRepository.findById(idSolicitud)
                .orElseThrow(() -> new IllegalArgumentException("La solicitud con ID " + idSolicitud + " no existe."));

        // 2. Calcular edad del fallecido
        Fallecido fallecido = expediente.getFallecido();
        int edad = Period.between(fallecido.getFechaNacimiento(), fallecido.getFechaFallecimiento()).getYears();
        fallecido.setEdad(edad);

        // 3. Guardar Fallecido
        Fallecido fallecidoGuardado = fallecidoRepository.save(fallecido);

        // 4. Crear y Guardar Expediente
        expediente.setFallecido(fallecidoGuardado);
        expediente.setSolicitud(solicitud);
        expediente.setFechaRegistro(LocalDate.now());
        Expediente expedienteGuardado = expedienteRepository.save(expediente);

        // 5. Generar Comprobante de Registro
        ComprobanteRegistro comprobante = new ComprobanteRegistro(expedienteGuardado, LocalDate.now());
        comprobanteRepository.save(comprobante);

        return expedienteGuardado;
    }

    @Transactional
    public Expediente actualizar(Integer idExpediente, Expediente datosNuevos) {
        Expediente expediente = expedienteRepository.findById(idExpediente)
                .orElseThrow(() -> new IllegalArgumentException("El expediente no existe."));

        // Actualizar datos del fallecido
        Fallecido fallecidoExistente = expediente.getFallecido();
        Fallecido fallecidoNuevo = datosNuevos.getFallecido();
        
        fallecidoExistente.setNombres(fallecidoNuevo.getNombres().trim());
        fallecidoExistente.setApellidos(fallecidoNuevo.getApellidos().trim());
        fallecidoExistente.setFechaNacimiento(fallecidoNuevo.getFechaNacimiento());
        fallecidoExistente.setFechaFallecimiento(fallecidoNuevo.getFechaFallecimiento());
        
        // Recalcular edad
        int edad = Period.between(fallecidoNuevo.getFechaNacimiento(), fallecidoNuevo.getFechaFallecimiento()).getYears();
        fallecidoExistente.setEdad(edad);
        fallecidoExistente.setSexo(fallecidoNuevo.getSexo());
        
        // Validar cambio de DNI si es diferente al actual
        if (!fallecidoExistente.getDni().equals(fallecidoNuevo.getDni())) {
            if (fallecidoRepository.existsByDni(fallecidoNuevo.getDni())) {
                throw new IllegalArgumentException("Ya existe un fallecido registrado con el DNI: " + fallecidoNuevo.getDni());
            }
            fallecidoExistente.setDni(fallecidoNuevo.getDni());
        }

        // Actualizar datos del deudo / solicitud
        SolicitudSepultura solicitudExistente = expediente.getSolicitud();
        SolicitudSepultura solicitudNueva = datosNuevos.getSolicitud();
        
        solicitudExistente.setNombreDeudo(trimOrEmpty(solicitudNueva.getNombreDeudo()));
        solicitudExistente.setDniDeudo(solicitudNueva.getDniDeudo().trim());
        solicitudExistente.setParentesco(solicitudNueva.getParentesco().trim());

        // Guardar cambios
        fallecidoRepository.save(fallecidoExistente);
        solicitudRepository.save(solicitudExistente);

        // Actualizar datos del expediente
        if (!expediente.getNumeroExpediente().equals(datosNuevos.getNumeroExpediente())) {
            if (expedienteRepository.existsByNumeroExpediente(datosNuevos.getNumeroExpediente())) {
                throw new IllegalArgumentException("Ya existe un expediente con el número: " + datosNuevos.getNumeroExpediente());
            }
            expediente.setNumeroExpediente(datosNuevos.getNumeroExpediente().trim());
        }

        return expedienteRepository.save(expediente);
    }

    private String trimOrEmpty(String name) {
        return name != null ? name.trim() : "";
    }

    public Optional<Expediente> obtenerPorId(Integer id) {
        return expedienteRepository.findById(id);
    }

    public List<Expediente> buscarConFiltros(String numeroExpediente, String dni, String nombres, String apellidos, Integer edad, String sexo, LocalDate fechaRegistro) {
        // Convertir campos vacíos a null para que la consulta JPQL funcione correctamente
        String numExp = (numeroExpediente == null || numeroExpediente.trim().isEmpty()) ? null : numeroExpediente.trim();
        String d = (dni == null || dni.trim().isEmpty()) ? null : dni.trim();
        String nom = (nombres == null || nombres.trim().isEmpty()) ? null : nombres.trim();
        String ape = (apellidos == null || apellidos.trim().isEmpty()) ? null : apellidos.trim();
        String sex = (sexo == null || sexo.trim().isEmpty()) ? null : sexo;

        return expedienteRepository.buscarConFiltros(numExp, d, nom, ape, edad, sex, fechaRegistro);
    }

    public Optional<ComprobanteRegistro> obtenerComprobantePorExpedienteId(Integer idExpediente) {
        return comprobanteRepository.findByExpedienteIdExpediente(idExpediente);
    }
}
