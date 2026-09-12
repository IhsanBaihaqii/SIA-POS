<?php
// includes/export_helper.php

/**
 * Kirim header agar browser mengunduh file sebagai Excel.
 */
function excelHeaders($filename) {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');
}

/**
 * Buka wrapper HTML untuk Excel.
 */
function excelStart($title, $subtitle = '') {
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head><meta charset="UTF-8">';
    echo '<style>
        table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; }
        th, td { border: 1px solid #999; padding: 6px 8px; }
        th { background: #1e40af; color: #ffffff; font-weight: bold; text-align: center; }
        .title { font-size: 16px; font-weight: bold; }
        .subtitle { font-size: 12px; color: #555; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bg-total { background: #e5e7eb; font-weight: bold; }
        .bg-danger { background: #fee2e2; color: #b91c1c; }
    </style>';
    echo '</head><body>';

    if ($title) {
        echo '<table>';
        echo '<tr><td colspan="10" class="title">' . htmlspecialchars($title) . '</td></tr>';
        if ($subtitle) {
            echo '<tr><td colspan="10" class="subtitle">' . htmlspecialchars($subtitle) . '</td></tr>';
        }
        echo '<tr><td colspan="10"></td></tr>';
        echo '</table>';
    }
}

/**
 * Tutup wrapper HTML.
 */
function excelEnd() {
    echo '</body></html>';
}

/**
 * Format angka untuk Excel (tanpa pemisah ribuan, pakai titik desimal).
 * Excel akan otomatis menganggapnya sebagai number jika formatnya murni angka.
 */
function excelNumber($num) {
    return number_format((float)$num, 2, '.', '');
}