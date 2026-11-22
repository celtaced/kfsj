package com.alsicorp.reservas;

import com.alsicorp.reservas.dominio.Habitacion;
import com.alsicorp.reservas.dominio.PoliticaPrecioEstandar;
import com.alsicorp.reservas.dominio.PoliticaPrecioTemporada;
import com.alsicorp.reservas.dominio.Reserva;
import com.alsicorp.reservas.dominio.TipoHabitacion;
import com.alsicorp.reservas.infraestructura.ProcesadorPagoAprobado;
import com.alsicorp.reservas.infraestructura.RepositorioHabitacionesEnMemoria;
import com.alsicorp.reservas.infraestructura.RepositorioReservasEnMemoria;
import com.alsicorp.reservas.servicio.ServicioReservas;
import java.math.BigDecimal;

public final class PruebaServicioReservas {
    public static void main(String[] args) {
        buscarHabitacionDisponible();
        reservarYConsultar();
        rechazarHabitacionOcupada();
        aplicarRecargoTemporada();
        rechazarPagoNulo();
        System.out.println("Todas las pruebas pasaron");
    }

    private static void buscarHabitacionDisponible() {
        ServicioReservas servicio = crearServicio();
        comprobar(servicio.buscarHabitacionesDisponibles().size() == 1,
                "Debe encontrar una sola habitacion disponible");
    }

    private static void reservarYConsultar() {
        ServicioReservas servicio = crearServicio();
        Reserva reserva = servicio.realizarReserva("H101", "Ana Lopez", 2);
        comprobar(reserva.getTotal().compareTo(new BigDecimal("150.00")) == 0,
                "El total estandar debe ser precio por noches");
        comprobar(servicio.consultarReservas("Ana Lopez").size() == 1,
                "Debe consultar la reserva del cliente");
    }

    private static void rechazarHabitacionOcupada() {
        ServicioReservas servicio = crearServicio();
        servicio.realizarReserva("H101", "Ana Lopez", 1);
        esperarExcepcion(IllegalStateException.class, () ->
                servicio.realizarReserva("H101", "Luis Perez", 1));
    }

    private static void aplicarRecargoTemporada() {
        Habitacion habitacion = new Habitacion(
                "H303", "303", TipoHabitacion.SUITE, new BigDecimal("100.00"));
        BigDecimal total = new PoliticaPrecioTemporada()
                .calcular(habitacion, 2);
        comprobar(total.compareTo(new BigDecimal("240.0")) == 0,
                "La temporada debe aplicar 20 por ciento");
    }

    private static void rechazarPagoNulo() {
        RepositorioHabitacionesEnMemoria habitaciones =
                new RepositorioHabitacionesEnMemoria();
        habitaciones.guardar(new Habitacion(
                "H404", "404", TipoHabitacion.ESTANDAR, new BigDecimal("50.00")));
        final ServicioReservas servicio = new ServicioReservas(
                habitaciones,
                new RepositorioReservasEnMemoria(),
                (cliente, monto) -> false,
                new PoliticaPrecioTemporada());
        esperarExcepcion(IllegalStateException.class, () ->
                servicio.realizarReserva("H404", "Ana Lopez", 1));
    }

    private static ServicioReservas crearServicio() {
        RepositorioHabitacionesEnMemoria habitaciones =
                new RepositorioHabitacionesEnMemoria();
        habitaciones.guardar(new Habitacion(
                "H101", "101", TipoHabitacion.ESTANDAR, new BigDecimal("75.00")));
        return new ServicioReservas(
                habitaciones,
                new RepositorioReservasEnMemoria(),
                new ProcesadorPagoAprobado(),
                new PoliticaPrecioEstandar());
    }

    private static void comprobar(boolean condicion, String mensaje) {
        if (!condicion) {
            throw new AssertionError(mensaje);
        }
    }

    private static void esperarExcepcion(Class<? extends Throwable> tipo,
                                         Accion accion) {
        try {
            accion.ejecutar();
            throw new AssertionError("Se esperaba " + tipo.getSimpleName());
        } catch (Throwable error) {
            if (!tipo.isInstance(error)) {
                throw new AssertionError("Se esperaba " + tipo.getSimpleName()
                        + " pero llego " + error.getClass().getSimpleName());
            }
        }
    }

    private interface Accion {
        void ejecutar();
    }
}
