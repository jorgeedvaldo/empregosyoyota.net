{{--
    Bloco de anuncio AdSense (unidade <ins>).
    So e renderizado se os anuncios estiverem ativos no painel de administracao.
    O loader (adsbygoogle.js) e carregado uma unica vez no <head> (template.app).

    Parametros:
      - $slot       (obrigatorio) data-ad-slot
      - $format     (opcional, def. 'auto')
      - $layout     (opcional) ex.: 'in-article'
      - $style      (opcional, def. 'display:block')
      - $responsive (opcional, bool) adiciona data-full-width-responsive
--}}
@if(($adsEnabled ?? true))
    <ins class="adsbygoogle"
         style="{{ $style ?? 'display:block' }}"
         @isset($layout) data-ad-layout="{{ $layout }}" @endisset
         data-ad-client="ca-pub-2118765549976668"
         data-ad-slot="{{ $slot }}"
         data-ad-format="{{ $format ?? 'auto' }}"
         @if(!empty($responsive)) data-full-width-responsive="true" @endif></ins>
    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
@endif
