package com.alsicorp.reservas.infraestructura;

import com.alsicorp.reservas.dominio.Reserva;
import com.alsicorp.reservas.puertos.RepositorioReservas;
import java.util.ArrayList;
import java.util.List;
import java.util.Locale;

public final class RepositorioReservasEnMemoria implements RepositorioReservas {
    private final List<Reserva> reservas = new ArrayList<>();

    @Override
    public void guardar(Reserva reserva) {
        reservas.add(reserva);
    }

    @Override
    public List<Reserva> buscarPorCliente(String nombreCliente) {
        String criterio = nombreCliente.trim().toLowerCase(Locale.ROOT);
        List<Reserva> resultado = new ArrayList<>();
        for (Reserva reserva : reservas) {
            if (reserva.getNombreCliente().toLowerCase(Locale.ROOT).equals(criterio)) {
                resultado.add(reserva);
            }
        }
        return resultado;
    }
}
