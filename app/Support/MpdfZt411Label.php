<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

function px2in($px): float
{
    return $px * 0.0104166667;
}

function pt2mm($pt): float
{
    return $pt * 0.35;
}

function pt2in($pt): float
{
    return $pt * 0.0138889;
}


function mm2in($mm): float
{
    return $mm * 0.0393701;
}

class Zt411Label
{
    protected int $fs = 12; // unit 12 pt
    protected int $pageWidth = 80; // mm
    protected int $pageHeight = 60; // mm
    protected int $marginXY = 4;

    protected $lineHeight;
    protected $leftMargin;
    protected $topOfCustomer;
    protected $widthOfCustomer;
    protected $topOfProduct;
    protected $widthOfProduct;
    protected $topOfQty;
    protected $topOfLabelPD;
    protected $topOfLabelBBD;
    protected $topOfOrderNo;
    protected $widthOfOrderNo;
    protected $widthOfRun;

    protected $pdf;
    protected $font;

    /**
     * @throws \Exception
     */
    function __construct()
    {
        $this->font = "msyh";
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $defaultFontConfig = (new FontVariables())->getDefaults();
        $this->pdf = new Mpdf(
            [
                'fontDir' => array_merge($defaultConfig['fontDir'], [
                    '/Users/Amber/Downloads/msjh',
                ]),
                'fontdata' => $defaultFontConfig['fontdata'] + [ // lowercase letters only in font key
                        'msyh' => [
                            'R' => 'msyh.ttf',
                            'B' => 'msyhbd.ttf',
                        ]
                    ],
                'default_font' => 'msyh',
                'mode' => 'utf-8',
                'format'=>array( $this->pageHeight, $this->pageWidth),
                'orientation' => 'L'
            ]
        );
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->useSubstitutions = false;
        $this->pdf->simpleTables = true;

        $this->fs = 12;
        $this->pageWidth = 80;
        $this->pageHeight = 60;
        $this->marginXY = 4;

        $this->lineHeight = pt2mm($this->fs) + 1;
        $this->leftMargin = $this->marginXY;
        $this->topOfCustomer = $this->marginXY;
        $this->widthOfCustomer = 52;
        $this->topOfProduct = 18;
        $this->widthOfProduct = $this->pageWidth - $this->marginXY - $this->marginXY;
        $this->topOfQty = $this->topOfProduct + 12;
        $this->topOfLabelPD = $this->topOfQty + 8;
        $this->topOfLabelBBD = $this->topOfLabelPD + 6;
        $this->topOfOrderNo = $this->topOfLabelBBD + 8;
        $this->widthOfOrderNo = 40;
        $this->widthOfRun = 40;
    }

    function addNew($cusName, $prdName, $qty, $pDate, $bbDate, $orderNo, $run): void
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont($this->font, 'B');

        $this->pdf->Image(Storage::path('h.png'), $this->widthOfCustomer, $this->topOfCustomer, 23, 9);
        $this->pdf->setXY($this->topOfCustomer, $this->topOfCustomer);
        $this->pdf->MultiCell($this->widthOfCustomer, $this->lineHeight,  $cusName);


        $this->pdf->SetFont($this->font, '');
        $this->pdf->SetFontSize( 10);
        $this->pdf->setXY($this->leftMargin, $this->topOfProduct);
        $this->pdf->MultiCell(72, $this->lineHeight, $prdName);

        $this->pdf->setXY($this->leftMargin, $this->topOfQty);
        $this->pdf->Cell(72, $this->lineHeight, "Qty: " . $qty);

        $this->pdf->SetXY($this->leftMargin, $this->topOfLabelPD);
        $this->pdf->Cell(72, $this->lineHeight, "Pack Date: " . $pDate);

        $this->pdf->SetXY($this->leftMargin, $this->topOfLabelBBD);
        $this->pdf->Cell(72, $this->lineHeight, "Best Date: " . $bbDate);

        $this->pdf->SetXY($this->leftMargin, $this->topOfOrderNo);
        $this->pdf->Cell($this->widthOfOrderNo, $this->lineHeight,  $orderNo);
        $this->pdf->Cell($this->widthOfRun, $this->lineHeight,  "RUN: " . $run);
    }

    function print(): void
    {
        $this->pdf->Output();
    }
}
