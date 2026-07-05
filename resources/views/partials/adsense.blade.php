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
      - $minheight  (opcional, def. 250) altura reservada (px) para evitar CLS
--}}
@if(($adsEnabled ?? true))
    {{-- Reserva espaco para o anuncio, evitando saltos de layout (CLS). --}}
    <div class="ad-slot" style="min-height: {{ $minheight ?? 250 }}px; margin: 12px 0; text-align: center; overflow: hidden;">
        <ins class="adsbygoogle"
             style="{{ $style ?? 'display:block' }}"
             @isset($layout) data-ad-layout="{{ $layout }}" @endisset
             data-ad-client="ca-pub-2118765549976668"
             data-ad-slot="{{ $slot }}"
             data-ad-format="{{ $format ?? 'auto' }}"
             @if(!empty($responsive)) data-full-width-responsive="true" @endif></ins>
    </div>
    <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
@endif
