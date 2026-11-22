package com.alsicorp.reservas.puertos;

import com.alsicorp.reservas.dominio.Habitacion;
import java.math.BigDecimal;

public interface PoliticaPrecio {
    BigDecimal calcular(Habitacion habitacion, int cantidadNoches);
}
