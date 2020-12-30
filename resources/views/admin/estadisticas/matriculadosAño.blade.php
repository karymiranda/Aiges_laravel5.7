@extends('admin.menuprincipal')
@section('tittle','Estadisticas')
@section('content')
    
<div class="box box-primary">
  <!-- /.box-header -->
  <div class="box-header with-border">
    <h3 class="box-title"><Strong>ESTUDIANTES MATRICULADOS POR AÑO</Strong></h3>
  </div>
  @if(count($errors) > 0)
    <div class="alert alert-danger" role="alert">
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  <!-- form start -->
  <div class="box-body">
    {!! Form::open(['class'=>'form-horizontal']) !!}
    <div class="form-group">                                           
      {!! Form::label('lbaño', 'Seleccionar Periodo Escolar',['class'=>'col-sm-3 control-label']) !!}
      <div class="col-sm-2">
        <input name="añoinicio" id="añoinicio" placeholder="Año Inicial" readonly="true" class="rangoPastYear form-control pull-right" type="text">
      </div>
      <!--div class="col-sm-1"-->
        {!! Form::label('lba', 'al',['class'=>'col-sm-1 control-label','style'=>'text-align:center; width:0;padding-left:initial']) !!}
      <!--/div-->
      <div class="col-sm-2">
        <input name="añofin" id="añofin" placeholder="Año Final" readonly="true" class="form-control pull-right" type="text">
      </div>
    </div>
    {!! Form::close() !!}
    <!-- Main content -->
    <section class="content">
      <div class="row" style="margin-left:auto; margin-right:auto;width: 90%">
        <div>
          <!-- BAR CHART -->
          <div class="box box-default">
            <div class="box-header with-border" style="text-align:center;">
              <h3 class="box-title">Niños y Niñas</h3>
            </div>
            <div class="box-body" id="graph">
              <div class="chart" id="contentCanvas">
                <canvas id="barChart" style="height:270px"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

        </div>
        <!-- /.col (RIGHT) -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
  <div class="box-footer" align="right">                
    <a href="{{route('listaestadisticas')}}" class="btn btn-primary"><< Regresar</a>
  </div>
  <!-- /.box-footer -->
</div>
@endsection

@section('script')
<script>
  $(document).ready(function(){
    $('#añoinicio').on('change',function(){
      $('#barChart').remove(); // this is my <canvas> element
      $('#contentCanvas').append('<canvas id="barChart" style="height:270px"><canvas>');
      $hoy = new Date();
      $añoinicial=parseInt($('#añoinicio').val())+1;
      $fechainicio=$añoinicial+'-01-01';
      $añofinal=parseInt($('#añoinicio').val())+10;
      $fechafin=$añofinal+'-01-01';
      $today=$hoy.getFullYear() + '-' + ($hoy.getMonth() + 1).toString().padStart(2, '0') + '-' +  $hoy.getDate().toString().padStart(2, '0');
      $end=$today;
      if ($fechafin < $today){
        $end=$fechafin;
      }
      $('#añofin').datepicker('destroy');
      $('#añofin').datepicker({
         startDate: new Date($fechainicio),
         endDate: new Date($end),
         autoclose: true,
        language: 'es',
         minViewMode: 2,
         format: 'yyyy'
       });
       $('#añofin').val('');
    })
    $('#añofin').on('change',function(){
      if($('#añofin').val()!='' && $('#añoinicio').val()!=''){
        
        var añoI=$('#añoinicio').val();
        var añoF=$('#añofin').val();
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url:"{{ route('buscarMatriculadosAño') }}",
            method:"POST",
            data:{añoI:añoI, añoF:añoF, _token:_token},
           success:  function (data) {
            
              graficar(data);
            
           },
           statusCode: {
              404: function() {
                 alert('web not found');
              }
           },
           error:function(x,xs,xt){
              window.open(JSON.stringify(x));
           }
        });
      }
    })
  })  
  
  function graficar(datos){
    var etiquetas=[];
     var totalF=[];
     var totalM=[];
     
      for(var i=0;i<datos.length;i++){
        etiquetas[i]=datos[i].año;
        totalF[i]=datos[i].niñas;
        totalM[i]=datos[i].niños;
      }
    
    barras(totalF,totalM,etiquetas);
  }
  function barras(F,M,x){
    var areaChartData = {
      labels  : x,
      datasets: [
        {
          label               : 'Niñas',
          fillColor           : 'rgba(255, 48, 160, 1)',
          strokeColor         : 'rgba(255, 48, 160, 1)',
          pointColor          : 'rgba(255, 48, 160, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(255,48,160,1)',
          data                : F
        },
        {
          label               : 'Niños',
          fillColor           : 'rgba(0,58,180,1)',
          strokeColor         : 'rgba(0,58,180,1)',
          pointColor          : '#0000FF',
          pointStrokeColor    : 'rgba(0,58,180,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(0,58,180,1)',
          data                : M
        }
      ]
    }

    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
    var barChart                         = new Chart(barChartCanvas)
    var barChartData                     = areaChartData
    
    var barChartOptions                  = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero        : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - If there is a stroke on each bar
      barShowStroke           : true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth          : 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing         : 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing       : 1,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to make the chart responsive
      responsive              : true,
      maintainAspectRatio     : true
    }
    barChartOptions.datasetFill = false
    barChart.Bar(barChartData, barChartOptions)
  }

</script>
@endsection