# Tarea 3: Refactorizacion de Sistemas Criticos

## Contexto y alcance

La base funcional corresponde al sistema de reservas representado en T2.pdf:

- El administrador gestiona las habitaciones.
- El cliente consulta habitaciones disponibles.
- El cliente realiza una reserva.
- El sistema registra el pago.
- El cliente puede consultar sus reservas.

## Aplicacion de SOLID

### 1. SRP: Responsabilidad unica

Las entidades contienen datos y reglas propias. Habitacion, Reserva y TipoHabitacion representan el dominio. El ServicioReservas orquesta casos de uso y los repositorios administran el acceso a datos.

Ejemplo: [ServicioReservas.java](src/com/alsicorp/reservas/servicio/ServicioReservas.java) no calcula impuestos ni implementa almacenamiento concreto.

### 2. OCP: Abierto a extension, cerrado a modificacion

PoliticaPrecio permite agregar estrategias nuevas sin modificar el servicio.

- [PoliticaPrecioEstandar.java](src/com/alsicorp/reservas/dominio/PoliticaPrecioEstandar.java)
- [PoliticaPrecioTemporada.java](src/com/alsicorp/reservas/dominio/PoliticaPrecioTemporada.java)

Para incorporar fines de semana o descuentos, se crea otra implementacion de PoliticaPrecio y se inyecta al servicio.

### 3. LSP: Sustitucion de Liskov

PoliticaPrecioTemporada hereda de PoliticaPrecioEstandar y conserva el contrato del metodo calcular:

- Valida habitacion y noches.
- Devuelve el precio correspondiente.
- No elimina ni contradice el comportamiento base.

El ServicioReservas trabaja con la abstraccion PoliticaPrecio y puede sustituir cualquiera de sus implementaciones.

### 4. ISP: Segregacion de interfaces

Los contratos estan separados segun su responsabilidad:

- RepositorioHabitaciones solo opera habitaciones.
- RepositorioReservas solo opera reservas.
- ProcesadorPago solo procesa pagos.
- PoliticaPrecio solo calcula precios.

Ninguna clase implementa metodos que no utiliza.

### 5. DIP: Inversion de dependencias

ServicioReservas depende de interfaces declaradas en el paquete puertos, no de implementaciones en memoria o de un proveedor de pagos concreto.

La composicion se realiza en [AplicacionReservas.java](src/com/alsicorp/reservas/aplicacion/AplicacionReservas.java).

## Clean Code

Se aplicaron nombres que expresan intencion, clases pequenas, metodos cortos y validaciones claras. El codigo evita duplicacion mediante interfaces y estrategias reutilizables. Los errores se representan con excepciones especificas y mensajes comprensibles.

Se evito anticipar infraestructura innecesaria: la persistencia es reemplazable, pero el proyecto no incluye bases de datos o frameworks que no estaban disponibles en el entorno original.

## Manejo de errores

- IllegalArgumentException: datos requeridos invalidos o fuera de rango.
- IllegalStateException: habitacion ocupada o pago rechazado.
- Optional evita el uso de referencias nulas al buscar una habitacion.

## Funcionalidad preservada

| Caso de uso | Evidencia |
|---|---|
| Administrar habitaciones | RepositorioHabitacionesEnMemoria |
| Buscar habitaciones disponibles | buscarHabitacionesDisponibles |
| Consultar reservas | consultarReservas |
| Realizar reserva | realizarReserva |
| Procesar pago | ProcesadorPagoAprobado |
| Calcular tarifa | PoliticaPrecioEstandar y PoliticaPrecioTemporada |

## Pruebas

[PruebaServicioReservas.java](tests/com/alsicorp/reservas/PruebaServicioReservas.java) valida:

1. Busqueda de habitaciones disponibles.
2. Creacion y consulta de reservas.
3. Rechazo de habitaciones ya reservadas.
4. Recargo por temporada.
5. Rechazo de pagos.

Ejecute en Windows:

```bat
compile-and-run.bat
```

La ejecucion compila con Java 8 y realiza una validacion de aplicacion y las pruebas definidas en el proyecto.
