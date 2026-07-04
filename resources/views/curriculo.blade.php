@extends('template.app')
@section('title', $curriculo['title'])
@section('description', strip_tags($curriculo['description']))
@section('url', asset('storage/' . $curriculo['photo']))
@section('canonical_link', url('/modelos-de-curriculos/'. $curriculo['slug']))
@section('content')

<!-- Page Content -->
<div class="container">

  <div class="row">

    <!-- Post Content Column -->
    <div class="col-lg-8">

      <!-- Title -->
    <h1 class="mt-4">{{$curriculo['title']}}</h1>

      <!-- Date/Time -->
      <p>Publicado em: {{ date_format(new DateTime($curriculo['created_at']), 'd-m-Y') }}</p>

      <hr>

      <!-- Preview Image -->
      <img class="img-fluid rounded" src="{{asset('storage/' . $curriculo['photo'])}}" alt="Emprego">

      <hr>

      <!-- Post Content -->
      <h3>Descrição:</h3>

      @include('partials.adsense', ['slot' => '7583808877', 'layout' => 'in-article', 'format' => 'fluid', 'style' => 'display:block; text-align:center;'])
      <p class="lead">{!!$curriculo['description']!!}</p>

      <p>Download {{$curriculo['link']}} </p>
      <hr>

    </div>

    <!-- Sidebar Widgets Column -->
    <div class="col-md-4"> 
    
      <!-- Anúncio Adaptável -->
      @include('partials.adsense', ['slot' => '8002595367', 'format' => 'auto', 'responsive' => true])

    </div>

  </div>
  <!-- /.row -->

</div>
<!-- /.container -->
@endsection('content')
