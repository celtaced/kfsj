package com.alsicorp.reservas.dominio;

import java.math.BigDecimal;

public final class Habitacion {
    private final String id;
    private final String numero;
    private final TipoHabitacion tipo;
    private final BigDecimal precioBase;
    private boolean disponible;

    public Habitacion(String id, String numero, TipoHabitacion tipo, BigDecimal precioBase) {
        this.id = validar(id, "El identificador");
        this.numero = validar(numero, "El numero");
        if (tipo == null) {
            throw new IllegalArgumentException("El tipo de habitacion es obligatorio");
        }
        if (precioBase == null || precioBase.signum() <= 0) {
            throw new IllegalArgumentException("El precio base debe ser mayor que cero");
        }
        this.tipo = tipo;
        this.precioBase = precioBase;
        this.disponible = true;
    }

    public String getId() { return id; }
    public String getNumero() { return numero; }
    public TipoHabitacion getTipo() { return tipo; }
    public BigDecimal getPrecioBase() { return precioBase; }
    public boolean estaDisponible() { return disponible; }
    public void marcarComoOcupada() { disponible = false; }
    public void marcarComoDisponible() { disponible = true; }

    private String validar(String valor, String campo) {
        if (valor == null || valor.trim().isEmpty()) {
            throw new IllegalArgumentException(campo + " no puede estar vacio");
        }
        return valor.trim();
    }
}
