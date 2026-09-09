<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Builds the small placeholder files that back the demo lesson resources.
 *
 * Real academies upload their own material; these exist so the protected
 * download route can be exercised end to end straight after seeding.
 */
final class DemoFileFactory
{
    /**
     * Write a single-page PDF and return its storage path.
     */
    public static function pdf(string $directory, string $filename, string $heading, string $body): string
    {
        $path = trim($directory, '/').'/'.$filename;

        Storage::disk('local')->put($path, self::buildPdf($heading, $body));

        return $path;
    }

    public static function text(string $directory, string $filename, string $contents): string
    {
        $path = trim($directory, '/').'/'.$filename;

        Storage::disk('local')->put($path, $contents);

        return $path;
    }

    /**
     * A minimal but structurally complete PDF: catalogue, page tree, one page
     * of Helvetica text, and a correct cross-reference table.
     */
    private static function buildPdf(string $heading, string $body): string
    {
        $lines = [
            'BT /F1 20 Tf 60 780 Td ('.self::escape($heading).') Tj ET',
        ];

        $y = 745;

        foreach (self::wrap($body) as $line) {
            $lines[] = 'BT /F1 11 Tf 60 '.$y.' Td ('.self::escape($line).') Tj ET';
            $y -= 16;
        }

        $lines[] = 'BT /F1 9 Tf 60 60 Td ('.self::escape('Oacer Academy — demonstration resource').') Tj ET';

        $stream = implode("\n", $lines);

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R '
                .'/Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n".$object."\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $count = count($objects) + 1;

        $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size {$count} /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF\n";

        return $pdf;
    }

    /**
     * @return list<string>
     */
    private static function wrap(string $body): array
    {
        return explode("\n", wordwrap($body, 88, "\n", true));
    }

    private static function escape(string $value): string
    {
        // PDF string literals escape backslashes and both parentheses.
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }
}
