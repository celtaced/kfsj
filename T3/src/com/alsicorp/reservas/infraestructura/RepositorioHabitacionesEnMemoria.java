package com.alsicorp.reservas.infraestructura;

import com.alsicorp.reservas.dominio.Habitacion;
import com.alsicorp.reservas.puertos.RepositorioHabitaciones;
import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Optional;

public final class RepositorioHabitacionesEnMemoria implements RepositorioHabitaciones {
    private final Map<String, Habitacion> habitaciones = new LinkedHashMap<>();

    @Override
    public List<Habitacion> buscarDisponibles() {
        List<Habitacion> disponibles = new ArrayList<>();
        for (Habitacion habitacion : habitaciones.values()) {
            if (habitacion.estaDisponible()) {
                disponibles.add(habitacion);
            }
        }
        return disponibles;
    }

    @Override
    public Optional<Habitacion> buscarPorId(String id) {
        return Optional.ofNullable(habitaciones.get(id));
    }

    @Override
    public void guardar(Habitacion habitacion) {
        habitaciones.put(habitacion.getId(), habitacion);
    }
}
