@extends('layouts.app')

@section('titulo', 'Destinos Turísticos')

@section('styles')
<style>
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .card-image {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3em;
        color: white;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .card-title {
        font-size: 1.3em;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }
    
    .card-info {
        font-size: 0.9em;
        color: #666;
        margin-bottom: 8px;
    }
    
    .card-price {
        font-size: 1.1em;
        color: #667eea;
        font-weight: bold;
        margin: 10px 0;
    }
    
    .btn {
        display: inline-block;
        padding: 10px 20px;
        background: #667eea;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        transition: background 0.3s;
        border: none;
        cursor: pointer;
        font-size: 0.95em;
    }
    
    .btn:hover {
        background: #764ba2;
    }
    
    h2 {
        color: #333;
        margin-bottom: 20px;
        border-bottom: 2px solid #667eea;
        padding-bottom: 10px;
    }
</style>
@endsection

@section('contenido')
    <h2>🗺️ Nuestros Destinos</h2>
    
    @if(empty($destinos))
        <p>No hay destinos disponibles en este momento.</p>
    @else
        <div class="grid">
            @foreach($destinos as $destino)
                <div class="card">
                    <div class="card-image">🌟</div>
                    <div class="card-body">
                        <div class="card-title">{{ $destino['nombre'] }}</div>
                        <div class="card-info">
                            <strong>Departamento:</strong> {{ $destino['departamento'] }}
                        </div>
                        <div class="card-info">
                            <strong>Categoría:</strong> {{ $destino['categoria'] }}
                        </div>
                        <div class="card-price">{{ $destino['precio'] }}</div>
                        <a href="{{ route('destinos.show', $destino['id']) }}" class="btn">Ver Detalles</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
