<?php

if (! function_exists('pdfAsset')) {
    /**
     * Gets the asset URL for the PDF.
     *
     * @param string $path
     *
     * @return string
     */
    function pdfAsset(string $path = ''): string
    {
        $path = substr($path, 0, 1) !== '/' ? "/{$path}" : $path;

        return app()->isLocal()
            ? $_SERVER['DOCUMENT_ROOT'] . $path
            : asset($path);
    }
}