{{--
      Necessary evil for the page count.
      Should be placed in a 'page' otherwise a new page is created
      See https://github.com/dompdf/dompdf/wiki/Usage
--}}
<script type="text/php">
    if ( isset($pdf) ) {
        // NOTE: We use `text` instead of `page_text`, as though `text` can't render the DOMPDF constants (such as
        // PAGE_NUM, we do have access to it as variable ($PAGE_NUM), and `page_text` renders from top to bottom for
        // each page, while `text` only renders once per page. If we use `page_text`, we get weird overlapping text.

        $cooperationFooterText = "{$GLOBALS['_inputSource']->name} - {$GLOBALS['_cooperation']->name} - Pagina";

        // 595.28 == $pdf->get_width()
        $xForCooperationFooterText = 595 - (strlen($cooperationFooterText) + 10) * 3;

        $cooperationFooterText .= " {$PAGE_NUM}";

        $font = $fontMetrics->getFont("Arial, Helvetica, sans-serif", "normal");
        $date = date('d-m-Y');
        // 841.89 == $pdf->get_height()
        $y = 810;
        $pdf->text($xForCooperationFooterText, $y, $cooperationFooterText, $font, 6, array(0,0,0), 0, 0, 0);

        // Somewhat reasonable average position for the date; as the font is not 1 to 1 with char length,
        // and not each cooperation name is the same length either.
        $xForDate = 595 - (strlen($date) + 10) * 3;
        $pdf->text($xForDate, ($y + 10), $date, $font, 6, array(0,0,0), 0, 0, 0);
    }
</script>