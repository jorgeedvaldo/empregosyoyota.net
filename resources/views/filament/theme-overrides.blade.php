{{--
    Override do tema do painel Filament (v2).
    Substitui a cor primaria (amber/laranja) por preto/cinza-escuro.
    A cor "danger" (vermelho) NAO e alterada, por isso os botoes/links
    de eliminar mantem-se vermelhos.
--}}
<style>
    :root {
        --p-50:  #f5f5f5;
        --p-100: #e5e5e5;
        --p-200: #d4d4d4;
        --p-300: #a3a3a3;
        --p-400: #525252;
        --p-500: #333333; /* hover */
        --p-600: #111111; /* cor base dos botoes/links primarios (quase preto) */
        --p-700: #000000;
        --p-800: #000000;
        --p-900: #000000;
    }

    /* ---- Background ---- */
    .bg-primary-50  { background-color: var(--p-50)  !important; }
    .bg-primary-100 { background-color: var(--p-100) !important; }
    .bg-primary-200 { background-color: var(--p-200) !important; }
    .bg-primary-300 { background-color: var(--p-300) !important; }
    .bg-primary-400 { background-color: var(--p-400) !important; }
    .bg-primary-500 { background-color: var(--p-500) !important; }
    .bg-primary-600 { background-color: var(--p-600) !important; }
    .bg-primary-700 { background-color: var(--p-700) !important; }
    .bg-primary-800 { background-color: var(--p-800) !important; }
    .bg-primary-900 { background-color: var(--p-900) !important; }

    .hover\:bg-primary-50:hover  { background-color: var(--p-50)  !important; }
    .hover\:bg-primary-100:hover { background-color: var(--p-100) !important; }
    .hover\:bg-primary-400:hover { background-color: var(--p-400) !important; }
    .hover\:bg-primary-500:hover { background-color: var(--p-500) !important; }
    .hover\:bg-primary-600:hover { background-color: var(--p-600) !important; }
    .hover\:bg-primary-700:hover { background-color: var(--p-700) !important; }

    .checked\:bg-primary-500:checked { background-color: var(--p-500) !important; }
    .checked\:bg-primary-600:checked { background-color: var(--p-600) !important; }

    /* ---- Texto (links, tabs ativos, etc.) ---- */
    .text-primary-300 { color: var(--p-300) !important; }
    .text-primary-400 { color: var(--p-400) !important; }
    .text-primary-500 { color: var(--p-500) !important; }
    .text-primary-600 { color: var(--p-600) !important; }
    .text-primary-700 { color: var(--p-700) !important; }

    .hover\:text-primary-400:hover { color: var(--p-400) !important; }
    .hover\:text-primary-500:hover { color: var(--p-500) !important; }
    .hover\:text-primary-600:hover { color: var(--p-600) !important; }

    /* ---- Bordas ---- */
    .border-primary-400 { border-color: var(--p-400) !important; }
    .border-primary-500 { border-color: var(--p-500) !important; }
    .border-primary-600 { border-color: var(--p-600) !important; }
    .focus\:border-primary-500:focus { border-color: var(--p-500) !important; }
    .focus\:border-primary-600:focus { border-color: var(--p-600) !important; }
    .focus-within\:border-primary-500:focus-within { border-color: var(--p-500) !important; }
    .checked\:border-primary-500:checked { border-color: var(--p-500) !important; }
    .checked\:border-primary-600:checked { border-color: var(--p-600) !important; }

    /* ---- Ring (focos, toggles, checkboxes, radios) ---- */
    .ring-primary-400 { --tw-ring-color: var(--p-400) !important; }
    .ring-primary-500 { --tw-ring-color: var(--p-500) !important; }
    .ring-primary-600 { --tw-ring-color: var(--p-600) !important; }
    .focus\:ring-primary-400:focus { --tw-ring-color: var(--p-400) !important; }
    .focus\:ring-primary-500:focus { --tw-ring-color: var(--p-500) !important; }
    .focus\:ring-primary-600:focus { --tw-ring-color: var(--p-600) !important; }
    .focus-within\:ring-primary-500:focus-within { --tw-ring-color: var(--p-500) !important; }

    /* ---- Fill (spinners / SVG) ---- */
    .fill-primary-500 { fill: var(--p-500) !important; }
    .fill-primary-600 { fill: var(--p-600) !important; }

    /* ---- Fundos com transparencia (realce do item ativo, etc.) ---- */
    .bg-primary-400\/10 { background-color: rgba(17, 17, 17, 0.08) !important; }
    .bg-primary-500\/10 { background-color: rgba(17, 17, 17, 0.08) !important; }
    .bg-primary-600\/10 { background-color: rgba(17, 17, 17, 0.08) !important; }
    .bg-primary-500\/5  { background-color: rgba(17, 17, 17, 0.05) !important; }

    /* ---- Variantes dark mode (caso o painel esteja em modo escuro) ---- */
    .dark .dark\:bg-primary-500  { background-color: var(--p-400) !important; }
    .dark .dark\:bg-primary-600  { background-color: var(--p-500) !important; }
    .dark .dark\:text-primary-400 { color: #d4d4d4 !important; }
    .dark .dark\:text-primary-500 { color: #a3a3a3 !important; }
</style>
