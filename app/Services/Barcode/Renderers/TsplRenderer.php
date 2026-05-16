<?php

namespace App\Services\Barcode\Renderers;

class TsplRenderer implements LabelRendererInterface
{
    private const FONT_TITLE = '2';
    private const FONT_NORMAL = '1';

    public function render(array $data, string $templateType): string
    {
        return match ($templateType) {
            'ITEM_LABEL' => $this->renderItemLabel($data),
            'BIN_LABEL' => $this->renderBinLabel($data),
            default => throw new \InvalidArgumentException("Unsupported template type: {$templateType}"),
        };
    }

    private function renderItemLabel(array $data): string
    {
        $this->validateData($data, ['item_name', 'erp_code', 'barcode', 'last_stock_in_date']);

        // Character limit of 28 ensures Font "2" fits within 380 dots with clean margins.
        $name = $this->formatItemName($data['item_name'], 28);
        $erp = $this->sanitize($data['erp_code']);
        $barcode = $this->sanitize($data['barcode']);
        $lastIn = $this->sanitize($data['last_stock_in_date']);
        $bin = $this->sanitize($data['bin_code'] ?? '-');

        $cmds = [];
        $cmds[] = "SIZE 50 mm, 30 mm";
        $cmds[] = "GAP 3 mm, 0";
        $cmds[] = "DIRECTION 1,0";
        $cmds[] = "REFERENCE 0,0";
        $cmds[] = "OFFSET 0 mm";
        $cmds[] = "CLS";
        
        // --- Header Zone (45% = 108 dots) ---
        // deterministically formatted to max 2 lines with ellipsis for predictable UI parity.
        $cmds[] = "BLOCK 10,16,380,60,\"" . self::FONT_TITLE . "\",0,1,1,4,0,\"$name\"";
        // Move ERP downward and use FONT_TITLE ("2") for better readability/hierarchy
        $cmds[] = "TEXT 10,92,\"" . self::FONT_TITLE . "\",0,1,1,\"ERP: $erp\"";
        
        // --- Barcode Zone (30% = 72 dots) ---
        // Width multiplier increased to 3 for bold industrial dominance.
        $cmds[] = "BARCODE 35,128,\"128\",50,0,0,3,3,\"$barcode\"";
        
        // --- Human Readable Text (Industrial Best Practice) ---
        // Balanced spacing: 4 dots below barcode, 23 dots above footer.
        $cmds[] = "TEXT 200,182,\"" . self::FONT_TITLE . "\",0,1,1,2,\"$barcode\"";
        
        // --- Footer Zone (25% = 60 dots) ---
        // Shifted to Y=225 to provide maximum breathing room for the barcode area.
        $cmds[] = "TEXT 10,225,\"" . self::FONT_NORMAL . "\",0,1,1,\"Last In: $lastIn\"";
        $cmds[] = "TEXT 390,225,\"" . self::FONT_NORMAL . "\",0,1,1,3,\"Bin: $bin\"";
        
        $cmds[] = "PRINT 1,1";

        return implode("\r\n", $cmds) . "\r\n";
    }

    private function renderBinLabel(array $data): string
    {
        $this->validateData($data, ['item_name', 'erp_code', 'barcode', 'bin_code']);

        $name = $this->sanitize(strtoupper($data['item_name']));
        $erp = $this->sanitize($data['erp_code']);
        $barcode = $this->sanitize($data['barcode']);
        $binCode = $this->sanitize($data['bin_code']);

        $cmds = [];
        $cmds[] = "SIZE 80 mm, 50 mm";
        $cmds[] = "GAP 3 mm, 0";
        $cmds[] = "DIRECTION 1,0";
        $cmds[] = "REFERENCE 0,0";
        $cmds[] = "OFFSET 0 mm";
        $cmds[] = "CLS";

        // --- Header Zone (30% = 120 dots) - Hybrid Split ---
        $cmds[] = "BLOCK 30,15,410,65,\"" . self::FONT_TITLE . "\",0,1,1,2,0,\"$name\"";
        $cmds[] = "TEXT 30,85,\"" . self::FONT_NORMAL . "\",0,1,1,\"ERP: $erp\"";
        
        $cmds[] = "BOX 460,15,620,105,4";
        $cmds[] = "TEXT 540,60,\"" . self::FONT_TITLE . "\",0,2,2,2,\"$binCode\"";
        
        // --- Barcode Zone (50% = 200 dots) ---
        $cmds[] = "BARCODE 20,150,\"128\",160,0,0,2,4,\"$barcode\"";
        
        // --- Footer Zone (20% = 80 dots) ---
        $cmds[] = "TEXT 320,350,\"" . self::FONT_TITLE . "\",0,1,1,2,\"$barcode\"";
        
        $cmds[] = "PRINT 1,1";

        return implode("\r\n", $cmds) . "\r\n";
    }

    /**
     * Formats item name for deterministic 2-line rendering.
     * Uses word-aware truncation and adds ellipsis if the second line overflows.
     */
    private function formatItemName(string $name, int $limitPerLine = 28): string
    {
        $name = strtoupper($this->sanitize($name));
        $words = explode(' ', $name);
        
        $lines = ['', ''];
        $currentLine = 0;

        foreach ($words as $word) {
            if (empty($word)) continue;

            $space = $lines[$currentLine] === '' ? '' : ' ';
            $proposed = $lines[$currentLine] . $space . $word;
            
            if (strlen($proposed) <= $limitPerLine) {
                $lines[$currentLine] = $proposed;
            } else {
                if ($currentLine === 0) {
                    $currentLine = 1;
                    // If the first word of the second line is too long, force truncate it
                    if (strlen($word) > $limitPerLine) {
                        $lines[$currentLine] = substr($word, 0, $limitPerLine - 3) . '...';
                        break;
                    }
                    $lines[$currentLine] = $word;
                } else {
                    // Already on second line, can't fit more words - truncate and add ellipsis
                    if (strlen($lines[1]) > $limitPerLine - 3) {
                        $lines[1] = substr($lines[1], 0, $limitPerLine - 3) . '...';
                    } else {
                        $lines[1] .= '...';
                    }
                    break;
                }
            }
        }

        return implode("\r\n", array_filter($lines));
    }

    private function sanitize(string $value): string
    {
        $value = str_replace(['"', "\r", "\n"], '', $value);
        return preg_replace('/[^\x20-\x7E]/', '', $value);
    }

    private function validateData(array $data, array $required): void
    {
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Missing required field for TSPL rendering: {$field}");
            }
        }
    }
}
