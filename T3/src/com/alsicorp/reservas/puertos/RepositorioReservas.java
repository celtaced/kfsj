package com.alsicorp.reservas.puertos;

import com.alsicorp.reservas.dominio.Reserva;
import java.util.List;

public interface RepositorioReservas {
    void guardar(Reserva reserva);
    List<Reserva> buscarPorCliente(String nombreCliente);
}
