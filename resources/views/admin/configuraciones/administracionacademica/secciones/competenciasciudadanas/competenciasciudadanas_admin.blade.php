@extends('admin.menuprincipal')
@section('tittle', 'Configuraciones/Secciones/Competencias Ciudadanas')
@section('content')

<div class="box box-primary box-solid">
  <div class="box-header">
    <h2 class="box-title"><Strong>COMPETENCIAS CIUDADANAS</Strong></h2>
  </div>

  {!! 
    Form::open([
      'url' => '/seccioncompetenciaadmin', 'method'=>'POST', 'class'=>'form-horizontal'
    ]) !!}
    <div class="box-body">
      <div class="form-group">
        {!! Form::label('periodo', 'Periodo', ['class'=>'col-sm-4 control-label']) !!}
        <div class="col-sm-4">
          <select name="periodo" id="periodo" class="form-control" title="Muestra solamente el periodo de evaluación activo" value>
            @foreach ($periodos as $item)
              <option value="{{ $item->id }}">
                {{ $item->nombre }} [{{$item->descripcion}}]
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="box-footer" align="right">
      <input type="hidden" name="seccion_id" value="{{ $id }}" />
      {!! Form::submit('Registrar', ['class'=>'btn btn-primary']) !!}
      <a href="{{route('listasecciones')}}" class="btn btn-default">Cancelar</a>
    </div>
  {!! Form::close() !!}
  @endsection