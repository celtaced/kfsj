@echo off
setlocal
if exist build rmdir /s /q build
mkdir build\classes
javac -encoding UTF-8 -source 8 -target 8 -d build\classes ^
  src\com\alsicorp\reservas\dominio\*.java ^
  src\com\alsicorp\reservas\puertos\*.java ^
  src\com\alsicorp\reservas\infraestructura\*.java ^
  src\com\alsicorp\reservas\servicio\*.java ^
  src\com\alsicorp\reservas\aplicacion\AplicacionReservas.java ^
  tests\com\alsicorp\reservas\PruebaServicioReservas.java
if errorlevel 1 exit /b %errorlevel%
java -cp build\classes com.alsicorp.reservas.aplicacion.AplicacionReservas
java -cp build\classes com.alsicorp.reservas.PruebaServicioReservas
