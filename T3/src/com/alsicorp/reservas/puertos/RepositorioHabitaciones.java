package com.alsicorp.reservas.puertos;

import com.alsicorp.reservas.dominio.Habitacion;
import java.util.List;
import java.util.Optional;

public interface RepositorioHabitaciones {
    List<Habitacion> buscarDisponibles();
    Optional<Habitacion> buscarPorId(String id);
    void guardar(Habitacion habitacion);
}
