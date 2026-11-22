package com.alsicorp.reservas.aplicacion;

import com.alsicorp.reservas.dominio.Habitacion;
import com.alsicorp.reservas.dominio.PoliticaPrecioEstandar;
import com.alsicorp.reservas.dominio.Reserva;
import com.alsicorp.reservas.dominio.TipoHabitacion;
import com.alsicorp.reservas.infraestructura.ProcesadorPagoAprobado;
import com.alsicorp.reservas.infraestructura.RepositorioHabitacionesEnMemoria;
import com.alsicorp.reservas.infraestructura.RepositorioReservasEnMemoria;
import com.alsicorp.reservas.servicio.ServicioReservas;
import java.math.BigDecimal;

public final class AplicacionReservas {
    public static void main(String[] args) {
        RepositorioHabitacionesEnMemoria habitaciones =
                new RepositorioHabitacionesEnMemoria();
        RepositorioReservasEnMemoria reservas = new RepositorioReservasEnMemoria();

        habitaciones.guardar(new Habitacion(
                "H101", "101", TipoHabitacion.ESTANDAR, new BigDecimal("75.00")));
        habitaciones.guardar(new Habitacion(
                "H202", "202", TipoHabitacion.SUPERIOR, new BigDecimal("120.00")));

        ServicioReservas servicio = new ServicioReservas(
                habitaciones,
                reservas,
                new ProcesadorPagoAprobado(),
                new PoliticaPrecioEstandar());

        ejecutarValidacionBasica(servicio);
    }

    private static void ejecutarValidacionBasica(ServicioReservas servicio) {
        int habitacionesAntes = servicio.buscarHabitacionesDisponibles().size();
        Reserva reserva = servicio.realizarReserva("H101", "Carlos Dubon", 3);
        int habitacionesDespues = servicio.buscarHabitacionesDisponibles().size();

        if (habitacionesAntes != 2) {
            throw new AssertionError("Se esperaban dos habitaciones disponibles");
        }
        if (habitacionesDespues != 1) {
            throw new AssertionError("La reserva no actualizo la disponibilidad");
        }
        if (reserva.getTotal().compareTo(new BigDecimal("225.00")) != 0) {
            throw new AssertionError("El total calculado no es correcto");
        }
        if (servicio.consultarReservas("Carlos Dubon").size() != 1) {
            throw new AssertionError("No se pudo consultar la reserva");
        }

        System.out.println("Validacion completada");
        System.out.println("Reserva creada: " + reserva.getId());
        System.out.println("Total: $" + reserva.getTotal());
    }
}
