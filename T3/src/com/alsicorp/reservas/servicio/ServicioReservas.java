package com.alsicorp.reservas.servicio;

import com.alsicorp.reservas.dominio.Habitacion;
import com.alsicorp.reservas.dominio.Reserva;
import com.alsicorp.reservas.puertos.PoliticaPrecio;
import com.alsicorp.reservas.puertos.ProcesadorPago;
import com.alsicorp.reservas.puertos.RepositorioHabitaciones;
import com.alsicorp.reservas.puertos.RepositorioReservas;
import java.math.BigDecimal;
import java.util.List;
import java.util.UUID;

public final class ServicioReservas {
    private final RepositorioHabitaciones repositorioHabitaciones;
    private final RepositorioReservas repositorioReservas;
    private final ProcesadorPago procesadorPago;
    private final PoliticaPrecio politicaPrecio;

    public ServicioReservas(RepositorioHabitaciones repositorioHabitaciones,
                            RepositorioReservas repositorioReservas,
                            ProcesadorPago procesadorPago,
                            PoliticaPrecio politicaPrecio) {
        this.repositorioHabitaciones = repositorioHabitaciones;
        this.repositorioReservas = repositorioReservas;
        this.procesadorPago = procesadorPago;
        this.politicaPrecio = politicaPrecio;
    }

    public List<Habitacion> buscarHabitacionesDisponibles() {
        return repositorioHabitaciones.buscarDisponibles();
    }

    public Reserva realizarReserva(String idHabitacion, String nombreCliente, int cantidadNoches) {
        Habitacion habitacion = repositorioHabitaciones.buscarPorId(idHabitacion)
                .orElseThrow(() -> new IllegalArgumentException("No existe la habitacion"));
        if (!habitacion.estaDisponible()) {
            throw new IllegalStateException("La habitacion no esta disponible");
        }

        BigDecimal total = politicaPrecio.calcular(habitacion, cantidadNoches);
        if (!procesadorPago.procesar(nombreCliente, total)) {
            throw new IllegalStateException("El pago fue rechazado");
        }

        Reserva reserva = new Reserva(
                UUID.randomUUID().toString(),
                habitacion.getId(),
                nombreCliente,
                cantidadNoches,
                total);
        habitacion.marcarComoOcupada();
        repositorioHabitaciones.guardar(habitacion);
        repositorioReservas.guardar(reserva);
        return reserva;
    }

    public List<Reserva> consultarReservas(String nombreCliente) {
        if (nombreCliente == null || nombreCliente.trim().isEmpty()) {
            throw new IllegalArgumentException("El nombre del cliente es obligatorio");
        }
        return repositorioReservas.buscarPorCliente(nombreCliente);
    }
}
