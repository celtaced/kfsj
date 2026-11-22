package com.alsicorp.reservas.dominio;

import java.math.BigDecimal;

public final class Reserva {
    private final String id;
    private final String idHabitacion;
    private final String nombreCliente;
    private final int cantidadNoches;
    private final BigDecimal total;

    public Reserva(String id, String idHabitacion, String nombreCliente,
                   int cantidadNoches, BigDecimal total) {
        this.id = validar(id, "El identificador");
        this.idHabitacion = validar(idHabitacion, "La habitacion");
        this.nombreCliente = validar(nombreCliente, "El nombre del cliente");
        if (cantidadNoches <= 0) {
            throw new IllegalArgumentException("La cantidad de noches debe ser mayor que cero");
        }
        if (total == null || total.signum() < 0) {
            throw new IllegalArgumentException("El total no puede ser negativo");
        }
        this.cantidadNoches = cantidadNoches;
        this.total = total;
    }

    public String getId() { return id; }
    public String getIdHabitacion() { return idHabitacion; }
    public String getNombreCliente() { return nombreCliente; }
    public int getCantidadNoches() { return cantidadNoches; }
    public BigDecimal getTotal() { return total; }

    private String validar(String valor, String campo) {
        if (valor == null || valor.trim().isEmpty()) {
            throw new IllegalArgumentException(campo + " no puede estar vacio");
        }
        return valor.trim();
    }
}
