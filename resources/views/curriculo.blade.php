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

      <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668" crossorigin="anonymous"></script>
      <ins class="adsbygoogle"
          style="display:block; text-align:center;"
          data-ad-layout="in-article"
          data-ad-format="fluid"
          data-ad-client="ca-pub-2118765549976668"
          data-ad-slot="7583808877">
      </ins>
      <script>
          (adsbygoogle = window.adsbygoogle || []).push({});
      </script>
      <p class="lead">{!!$curriculo['description']!!}</p>

      <p>Download {{$curriculo['link']}} </p>
      <hr>

    </div>

    <!-- Sidebar Widgets Column -->
    <div class="col-md-4"> 
    
      <!-- Anúncio Adaptável -->
      <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2118765549976668"
          crossorigin="anonymous"></script>
      <!-- Anúncio Adaptável -->
      <ins class="adsbygoogle"
          style="display:block"
          data-ad-client="ca-pub-2118765549976668"
          data-ad-slot="8002595367"
          data-ad-format="auto"
          data-full-width-responsive="true"></ins>
      <script>
          (adsbygoogle = window.adsbygoogle || []).push({});
      </script>

    </div>

  </div>
  <!-- /.row -->

</div>
<!-- /.container -->
@endsection('content')
