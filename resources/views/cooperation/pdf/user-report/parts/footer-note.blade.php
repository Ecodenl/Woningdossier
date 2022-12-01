{{--
      Necessary evil for the page count.
      Should be placed in a 'page' otherwise a new page is created
  --}}
<script type="text/php">
    if ( isset($pdf) ) {
        $cooperationFooterText = "{$GLOBALS['_inputSource']->name} - {$GLOBALS['_cooperation']->name} - Pagina {PAGE_NUM}";

        // 595.28 == $pdf->get_width()
        $xForCooperationFooterText = 595 - strlen($cooperationFooterText) * 3;

        $font = $fontMetrics->getFont("Arial, Helvetica, sans-serif", "normal");
        $date = date('d-m-Y');
        // 841.89 == $pdf->get_height()
        $y = 810;
        $pdf->page_text($xForCooperationFooterText, $y, $cooperationFooterText, $font, 6, array(0,0,0));

        // So due to how DomPdf renders fixed elements, the content will be overlapped if we do it like this.
        // For now we will use a fixed size for a single digit page.
        //$xForDate = 533.5 + strlen($PAGE_NUM) * 3;
        $xForDate = 536.5;
        $pdf->page_text($xForDate, ($y + 10), $date, $font, 6, array(0,0,0));
    }
</script>