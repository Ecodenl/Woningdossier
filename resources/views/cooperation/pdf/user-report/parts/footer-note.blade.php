{{--
      Necessary evil for the page count.
      Should be placed in a 'page' otherwise a new page is created
  --}}
<script type="text/php">
    if ( isset($pdf) ) {
        $cooperationFooterText = "{$GLOBALS['_inputSource']->name} - {$GLOBALS['_cooperation']->name} - Pagina {PAGE_NUM}";

        $xForCooperationFooterText = 595 - strlen($cooperationFooterText) * 3;

        $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
        $date = date('Y-m-d');
        $y = 810;
        $pdf->page_text($xForCooperationFooterText, $y, $cooperationFooterText, $font, 6, array(0,0,0));
        $pdf->page_text(531, $y + 10, $date, $font, 6, array(0,0,0));
    }
</script>