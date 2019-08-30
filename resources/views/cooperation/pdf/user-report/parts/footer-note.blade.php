{{--
      Necessary evil for the page count.
      Should be placed in a 'page' otherwise a new page is created
  --}}
<script type="text/php">
    if ( isset($pdf) ) {
        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
        $y = 750;
        $x = 525;
        $pdf->page_text($x, $y, "{$GLOBALS['_inputSource']->name} - {$GLOBALS['_cooperation']->name} - Pagina {PAGE_NUM}", $font, 6, array(0,0,0));
    }
</script>