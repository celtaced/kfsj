@extends('layouts.app')

@section('titulo', 'Contacto')

@section('styles')
<style>
    .formulario-container {
        max-width: 600px;
        margin: 0 auto;
        background: #f8f9fa;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    h2 {
        color: #333;
        margin-bottom: 30px;
        text-align: center;
        border-bottom: 2px solid #667eea;
        padding-bottom: 10px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }
    
    input[type="text"],
    input[type="email"],
    textarea,
    select {
        width: 100%;
        padding: 12px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        font-size: 1em;
        font-family: inherit;
        transition: border-color 0.3s;
    }
    
    input[type="text"]:focus,
    input[type="email"]:focus,
    textarea:focus,
    select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
    }
    
    textarea {
        resize: vertical;
        min-height: 150px;
    }
    
    .btn-submit {
        display: inline-block;
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1.05em;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.3s;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .error {
        color: #dc3545;
        font-size: 0.85em;
        margin-top: 5px;
    }
    
    .info-contacto {
        background: white;
        padding: 20px;
        border-radius: 5px;
        margin-top: 30px;
        border-left: 4px solid #667eea;
    }
    
    .info-contacto h3 {
        color: #667eea;
        margin-bottom: 15px;
    }
    
    .info-contacto p {
        color: #666;
        line-height: 1.6;
    }
</style>
@endsection

@section('contenido')
    <div class="formulario-container">
        <h2>✉️ Contacto - Solicita Información</h2>
        
        @if ($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('contacto.enviar') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre" required value="{{ old('nombre') }}">
                @error('nombre')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="email">Correo Electrónico *</label>
                <input type="email" id="email" name="email" required value="{{ old('email') }}">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="destino">Destino de Interés *</label>
                <select id="destino" name="destino" required>
                    <option value="">-- Selecciona un destino --</option>
                    <option value="Monumento Nacional El Rosario" {{ old('destino') == 'Monumento Nacional El Rosario' ? 'selected' : '' }}>Monumento Nacional El Rosario</option>
                    <option value="Volcán de Izalco" {{ old('destino') == 'Volcán de Izalco' ? 'selected' : '' }}>Volcán de Izalco</option>
                    <option value="Playa El Tunco" {{ old('destino') == 'Playa El Tunco' ? 'selected' : '' }}>Playa El Tunco</option>
                    <option value="Lago de Coatepeque" {{ old('destino') == 'Lago de Coatepeque' ? 'selected' : '' }}>Lago de Coatepeque</option>
                    <option value="Ruta de las Flores" {{ old('destino') == 'Ruta de las Flores' ? 'selected' : '' }}>Ruta de las Flores</option>
                    <option value="Joya de Cerén" {{ old('destino') == 'Joya de Cerén' ? 'selected' : '' }}>Joya de Cerén</option>
                </select>
                @error('destino')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="mensaje">Mensaje *</label>
                <textarea id="mensaje" name="mensaje" required>{{ old('mensaje') }}</textarea>
                @error('mensaje')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn-submit">Enviar Mensaje</button>
        </form>
        
        <div class="info-contacto">
            <h3>📌 Información Adicional</h3>
            <p>
                Si prefieren contactar directamente, pueden comunicarse con nuestro equipo de atención al cliente. 
                Nuestro equipo está disponible para responder cualquier pregunta sobre nuestros destinos turísticos.
            </p>
        </div>
    </div>
@endsection
