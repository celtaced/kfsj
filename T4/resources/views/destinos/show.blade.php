@extends('layouts.app')

@section('titulo', $destino['nombre'])

@section('styles')
<style>
    .detalle-container {
        max-width: 900px;
        margin: 0 auto;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .detalle-imagen {
        width: 100%;
        height: 400px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5em;
        color: white;
    }
    
    .detalle-contenido {
        padding: 40px;
    }
    
    .detalle-titulo {
        font-size: 2.5em;
        color: #333;
        margin-bottom: 20px;
        border-bottom: 3px solid #667eea;
        padding-bottom: 15px;
    }
    
    .detalle-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin: 30px 0;
    }
    
    .info-item {
        background: white;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #667eea;
    }
    
    .info-label {
        font-weight: bold;
        color: #667eea;
        font-size: 0.9em;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    
    .info-value {
        color: #333;
        font-size: 1.1em;
    }
    
    .descripcion {
        background: white;
        padding: 20px;
        border-radius: 5px;
        line-height: 1.8;
        font-size: 1.05em;
        margin: 20px 0;
        color: #555;
    }
    
    .btn {
        display: inline-block;
        padding: 12px 30px;
        background: #667eea;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background 0.3s;
        margin-right: 10px;
        margin-top: 20px;
        border: none;
        cursor: pointer;
        font-size: 1em;
    }
    
    .btn:hover {
        background: #764ba2;
    }
    
    .btn-secondary {
        background: #6c757d;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .contacto-link {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 30px;
        background: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background 0.3s;
    }
    
    .contacto-link:hover {
        background: #218838;
    }
</style>
@endsection

@section('contenido')
    <div class="detalle-container">
        <div class="detalle-imagen">🏖️</div>
        
        <div class="detalle-contenido">
            <div class="detalle-titulo">{{ $destino['nombre'] }}</div>
            
            <div class="detalle-info">
                <div class="info-item">
                    <div class="info-label">📍 Departamento</div>
                    <div class="info-value">{{ $destino['departamento'] }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">🏷️ Categoría</div>
                    <div class="info-value">{{ $destino['categoria'] }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">💰 Precio de Entrada</div>
                    <div class="info-value">{{ $destino['precio'] }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">⏰ Horario</div>
                    <div class="info-value">{{ $destino['horario'] }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">📞 Teléfono</div>
                    <div class="info-value">{{ $destino['telefono'] }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">📧 Correo</div>
                    <div class="info-value">{{ $destino['email'] }}</div>
                </div>
            </div>
            
            <div class="descripcion">
                <strong>Acerca de este destino:</strong><br><br>
                {{ $destino['descripcion'] }}
            </div>
            
            <div>
                <a href="{{ route('destinos.index') }}" class="btn btn-secondary">← Volver</a>
                <a href="{{ route('contacto.formulario') }}" class="contacto-link">Contactar para más info →</a>
            </div>
        </div>
    </div>
@endsection
