<?php

namespace App\Support;

use Fpdf\Fpdf;

$GB_widths = array(' '=>480,'!'=>480,'"'=>480,'#'=>480,'$'=>480,'%'=>480,'&'=>480,'\''=>480,
    '('=>480,')'=>480,'*'=>480,'+'=>480,','=>480,'-'=>480,'.'=>480,'/'=>480,'0'=>480,'1'=>480,
    '2'=>480,'3'=>480,'4'=>480,'5'=>480,'6'=>480,'7'=>480,'8'=>480,'9'=>480,':'=>480,';'=>480,
    '<'=>480,'='=>480,'>'=>480,'?'=>480,'@'=>480,'A'=>480,'B'=>480,'C'=>480,'D'=>480,'E'=>480,
    'F'=>480,'G'=>480,'H'=>480,'I'=>480,'J'=>480,'K'=>480,'L'=>480,'M'=>480,'N'=>480,'O'=>480,
    'P'=>480,'Q'=>480,'R'=>480,'S'=>480,'T'=>480,'U'=>480,'V'=>480,'W'=>480,'X'=>480,'Y'=>480,
    'Z'=>480,'['=>480,'\\'=>480,']'=>480,'^'=>480,'_'=>480,'`'=>480,'a'=>480,'b'=>480,'c'=>480,
    'd'=>480,'e'=>480,'f'=>480,'g'=>480,'h'=>480,'i'=>480,'j'=>230,'k'=>495,'l'=>228,'m'=>480,
    'n'=>480,'o'=>480,'p'=>480,'q'=>480,'r'=>480,'s'=>480,'t'=>480,'u'=>480,'v'=>480,'w'=>480,
    'x'=>480,'y'=>480,'z'=>480,'{'=>480,'|'=>480,'}'=>480,'~'=>480);

class PdfChinese extends Fpdf
{

    /**
     * @throws \Exception
     */
    function AddCIDFont($family, $style, $name, $cw, $CMap, $registry)
    {
        $fontkey = strtolower($family) . strtoupper($style);
        if (isset($this->fonts[$fontkey]))
            $this->Error("Font already added: $family $style");
        $i = count($this->fonts) + 1;
        $name = str_replace(' ', '', $name);
        $this->fonts[$fontkey] = array('i' => $i, 'type' => 'Type0', 'name' => $name, 'up' => -130, 'ut' => 40, 'cw' => $cw, 'CMap' => $CMap, 'registry' => $registry);
    }

    /**
     * @throws \Exception
     */
    function AddCIDFonts($family, $name, $cw, $CMap, $registry)
    {
        $this->AddCIDFont($family, '', $name, $cw, $CMap, $registry);
        $this->AddCIDFont($family, 'B', $name . ',Bold', $cw, $CMap, $registry);
        $this->AddCIDFont($family, 'I', $name . ',Italic', $cw, $CMap, $registry);
        $this->AddCIDFont($family, 'BI', $name . ',BoldItalic', $cw, $CMap, $registry);
    }


    /**
     * @throws \Exception
     */
    function AddGBFont($family = 'GB', $name = 'STSongStd-Light-Acro')
    {
        global $GB_widths;
        // Add GB font with proportional Latin
        $cw = $GB_widths;
        $CMap = 'GBKp-EUC-H';
        $registry = array('ordering' => 'GB1', 'supplement' => 2);
        $this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
    }

    /**
     * @throws \Exception
     */
    function AddGBhwFont($family = 'GB-hw', $name = 'STSongStd-Light-Acro')
    {
        // Add GB font with half-width Latin
        for ($i = 32; $i <= 126; $i++)
            $cw[chr($i)] = 500;
        $CMap = 'GBK-EUC-H';
        $registry = array('ordering' => 'GB1', 'supplement' => 2);
        $this->AddCIDFonts($family, $name, $cw, $CMap, $registry);
    }

    function GetStringWidth($s): int
    {
        if ($this->CurrentFont['type'] == 'Type0')
            return $this->GetMBStringWidth($s);
        else
            return parent::GetStringWidth($s);
    }

    function GetMBStringWidth($s): int
    {
        // Multi-byte version of GetStringWidth()
        $l = 0;
        $cw = &$this->CurrentFont['cw'];
        $nb = strlen($s);
        $i = 0;
        while ($i < $nb) {
            $c = $s[$i];
            if (ord($c) < 128) {
                $l += $cw[$c];
                $i++;
            } else {
                $l += 1000;
                $i += 2;
            }
        }
        return $l * $this->FontSize / 1000;
    }

    function MultiCell($w, $h, $txt, $border = 0, $align = 'L', $fill = 0)
    {
        if ($this->CurrentFont['type'] == 'Type0')
            $this->MBMultiCell($w, $h, $txt, $border, $align, $fill);
        else
            parent::MultiCell($w, $h, $txt, $border, $align, $fill);
    }

    function MBMultiCell($w, $h, $txt, $border = 0, $align = 'L', $fill = 0)
    {
        // Multi-byte version of MultiCell()
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b = 'LRT';
                $b2 = 'LR';
            } else {
                $b2 = '';
                if (is_int(strpos($border, 'L')))
                    $b2 .= 'L';
                if (is_int(strpos($border, 'R')))
                    $b2 .= 'R';
                $b = is_int(strpos($border, 'T')) ? $b2 . 'T' : $b2;
            }
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            // Get next character
            $c = $s[$i];
            // Check if ASCII or MB
            $ascii = (ord($c) < 128);
            if ($c == "\n") {
                // Explicit line break
                $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                if ($border && $nl == 2)
                    $b = $b2;
                continue;
            }
            if (!$ascii) {
                $sep = $i;
                $ls = $l;
            } elseif ($c == ' ') {
                $sep = $i;
                $ls = $l;
            }
            $l += $ascii ? $cw[$c] : 1000;
            if ($l > $wmax) {
                // Automatic line break
                if ($sep == -1 || $i == $j) {
                    if ($i == $j)
                        $i += $ascii ? 1 : 2;
                    $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                } else {
                    $this->Cell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill);
                    $i = ($s[$sep] == ' ') ? $sep + 1 : $sep;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                if ($border && $nl == 2)
                    $b = $b2;
            } else
                $i += $ascii ? 1 : 2;
        }
        // Last chunk
        if ($border && is_int(strpos($border, 'B')))
            $b .= 'B';
        $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
        $this->x = $this->lMargin;
    }

    function Write($h, $txt, $link = '')
    {
        if ($this->CurrentFont['type'] == 'Type0')
            $this->MBWrite($h, $txt, $link);
        else
            parent::Write($h, $txt, $link);
    }

    function MBWrite($h, $txt, $link)
    {
        // Multi-byte version of Write()
        $cw = &$this->CurrentFont['cw'];
        $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            // Get next character
            $c = $s[$i];
            // Check if ASCII or MB
            $ascii = (ord($c) < 128);
            if ($c == "\n") {
                // Explicit line break
                $this->Cell($w, $h, substr($s, $j, $i - $j), 0, 2, '', 0, $link);
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                if ($nl == 1) {
                    $this->x = $this->lMargin;
                    $w = $this->w - $this->rMargin - $this->x;
                    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
                }
                $nl++;
                continue;
            }
            if (!$ascii || $c == ' ')
                $sep = $i;
            $l += $ascii ? $cw[$c] : 1000;
            if ($l > $wmax) {
                // Automatic line break
                if ($sep == -1 || $i == $j) {
                    if ($this->x > $this->lMargin) {
                        // Move to next line
                        $this->x = $this->lMargin;
                        $this->y += $h;
                        $w = $this->w - $this->rMargin - $this->x;
                        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
                        $i++;
                        $nl++;
                        continue;
                    }
                    if ($i == $j)
                        $i += $ascii ? 1 : 2;
                    $this->Cell($w, $h, substr($s, $j, $i - $j), 0, 2, '', 0, $link);
                } else {
                    $this->Cell($w, $h, substr($s, $j, $sep - $j), 0, 2, '', 0, $link);
                    $i = ($s[$sep] == ' ') ? $sep + 1 : $sep;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                if ($nl == 1) {
                    $this->x = $this->lMargin;
                    $w = $this->w - $this->rMargin - $this->x;
                    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
                }
                $nl++;
            } else
                $i += $ascii ? 1 : 2;
        }
        // Last chunk
        if ($i != $j)
            $this->Cell($l / 1000 * $this->FontSize, $h, substr($s, $j, $i - $j), 0, 0, '', 0, $link);
    }

    function _putType0($font)
    {
        // Type0
        $this->_newobj();
        $this->_out('<</Type /Font');
        $this->_out('/Subtype /Type0');
        $this->_out('/BaseFont /' . $font['name'] . '-' . $font['CMap']);
        $this->_out('/Encoding /' . $font['CMap']);
        $this->_out('/DescendantFonts [' . ($this->n + 1) . ' 0 R]');
        $this->_out('>>');
        $this->_out('endobj');
        // CIDFont
        $this->_newobj();
        $this->_out('<</Type /Font');
        $this->_out('/Subtype /CIDFontType0');
        $this->_out('/BaseFont /' . $font['name']);
        $this->_out('/CIDSystemInfo <</Registry ' . $this->_textstring('Adobe') . ' /Ordering ' . $this->_textstring($font['registry']['ordering']) . ' /Supplement ' . $font['registry']['supplement'] . '>>');
        $this->_out('/FontDescriptor ' . ($this->n + 1) . ' 0 R');
        if ($font['CMap'] == 'ETen-B5-H')
            $W = '13648 13742 500';
        elseif ($font['CMap'] == 'GBK-EUC-H')
            $W = '814 907 500 7716 [500]';
        else
            $W = '1 [' . implode(' ', $font['cw']) . ']';
        $this->_out('/W [' . $W . ']>>');
        $this->_out('endobj');
        // Font descriptor
        $this->_newobj();
        $this->_out('<</Type /FontDescriptor');
        $this->_out('/FontName /' . $font['name']);
        $this->_out('/Flags 6');
        $this->_out('/FontBBox [0 -200 1000 900]');
        $this->_out('/ItalicAngle 0');
        $this->_out('/Ascent 800');
        $this->_out('/Descent -200');
        $this->_out('/CapHeight 800');
        $this->_out('/StemV 50');
        $this->_out('>>');
        $this->_out('endobj');
    }
}
