package com.alsicorp.reservas.infraestructura;

import com.alsicorp.reservas.puertos.ProcesadorPago;
import java.math.BigDecimal;

public final class ProcesadorPagoAprobado implements ProcesadorPago {
    @Override
    public boolean procesar(String nombreCliente, BigDecimal monto) {
        return nombreCliente != null
                && !nombreCliente.trim().isEmpty()
                && monto != null
                && monto.signum() > 0;
    }
}
