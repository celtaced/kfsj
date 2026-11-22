# Sistema de reservas - Tarea 3

Proyecto Java de demostracion basado en los casos de uso de T2.pdf y en los conceptos de Clean Code y SOLID de M2S5.pdf.

## Estructura

- src/com/alsicorp/reservas/dominio: entidades y reglas de dominio.
- src/com/alsicorp/reservas/puertos: interfaces para persistencia, pagos y precios.
- src/com/alsicorp/reservas/infraestructura: implementaciones intercambiables.
- src/com/alsicorp/reservas/servicio: casos de uso.
- src/com/alsicorp/reservas/aplicacion: composicion y ejecucion.
- tests/com/alsicorp/reservas: pruebas sin frameworks externos.

## Ejecutar

Requisitos: Java JDK 8 o superior.

```bat
compile-and-run.bat
```

## Entrega

El documento principal es [INFORME-T3.md](INFORME-T3.md).
