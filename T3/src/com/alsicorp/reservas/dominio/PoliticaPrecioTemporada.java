package com.alsicorp.reservas.dominio;

import java.math.BigDecimal;

public final class PoliticaPrecioTemporada extends PoliticaPrecioEstandar {
    private static final BigDecimal RECARGO_TEMPORADA = new BigDecimal("1.20");

    @Override
    public BigDecimal calcular(Habitacion habitacion, int cantidadNoches) {
        validarCalculo(habitacion, cantidadNoches);
        return super.calcular(habitacion, cantidadNoches).multiply(RECARGO_TEMPORADA);
    }
}
