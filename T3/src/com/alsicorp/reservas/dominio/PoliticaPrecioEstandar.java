package com.alsicorp.reservas.dominio;

import com.alsicorp.reservas.puertos.PoliticaPrecio;
import java.math.BigDecimal;

public class PoliticaPrecioEstandar implements PoliticaPrecio {
    @Override
    public BigDecimal calcular(Habitacion habitacion, int cantidadNoches) {
        validarCalculo(habitacion, cantidadNoches);
        return habitacion.getPrecioBase().multiply(new BigDecimal(cantidadNoches));
    }

    protected void validarCalculo(Habitacion habitacion, int cantidadNoches) {
        if (habitacion == null) {
            throw new IllegalArgumentException("La habitacion es obligatoria");
        }
        if (cantidadNoches <= 0) {
            throw new IllegalArgumentException("La cantidad de noches debe ser mayor que cero");
        }
    }
}
