package com.alsicorp.reservas.puertos;

import java.math.BigDecimal;

public interface ProcesadorPago {
    boolean procesar(String nombreCliente, BigDecimal monto);
}
