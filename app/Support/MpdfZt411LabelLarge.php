<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
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

class MpdfZt411LabelLarge
{
    protected int $fs = 12; // unit 12 pt
    protected int $pageWidth = 100; // mm
    protected int $pageHeight = 100; // mm
    protected int $marginXY = 4;

    protected $lineHeight;
    protected $leftMargin;
    protected $topOfHoC;
    protected $widthOfCustomer;
    protected $topOfProduct;
    protected $widthOfWholeRow;
    protected $topOfQty;
    protected $topOfLabelPD;
    protected $topOfLabelBBD;
    protected $topOfAccNo;
    protected $widthOfAccNo;
    protected $topOfAddress;
    protected $widthOfAddress;
    protected $topOfStorageCondition;
    protected $widthOfStorageCondition;
    protected $topOfPhoneNo;
    protected $widthOfPhoneNo;

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
                    base_path('font'),
                ]),
                'fontdata' => $defaultFontConfig['fontdata'] + [ // lowercase letters only in font key
                        'msyh' => [
                            'R' => 'msyh.ttf',
                            'B' => 'msyhbd.ttf',
                        ]
                    ],
                'default_font' => 'msyh',
                'mode' => 'utf-8',
                'format' => array($this->pageHeight, $this->pageWidth),
                'orientation' => 'L'
            ]
        );
        $this->pdf->setLogger(Log::getLogger());
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->useSubstitutions = false;
        $this->pdf->simpleTables = true;

        $this->lineHeight = pt2mm($this->fs) + 1;
        $this->leftMargin = $this->marginXY;
        $this->topOfHoC = $this->marginXY;
        $this->widthOfWholeRow = $this->pageWidth - $this->marginXY - $this->marginXY;
        $this->widthOfCustomer = $this->widthOfWholeRow;
        $this->topOfProduct = 18;
        $this->topOfQty = $this->topOfProduct + 25;
        $this->topOfLabelPD = $this->topOfQty + 18;
        $this->topOfLabelBBD = $this->topOfLabelPD + 6;
        $this->topOfAccNo = $this->topOfLabelBBD + 10;
        $this->widthOfAccNo = $this->widthOfWholeRow;
        $this->topOfAddress = $this->topOfAccNo + 6;
        $this->widthOfAddress = $this->widthOfWholeRow;
        $this->topOfStorageCondition = $this->topOfAddress + 6;
        $this->widthOfStorageCondition = $this->widthOfWholeRow;
        $this->topOfPhoneNo = $this->topOfStorageCondition + 6;
        $this->widthOfPhoneNo = $this->widthOfWholeRow;
    }

    function addNew($prdName, $qty, $pDate, $bbDate): void
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont($this->font, '', 12);

        $this->pdf->Image(base_path('h-v.png'), 75, 50, 18, 2.56 * 18);
        $this->pdf->setXY($this->leftMargin, $this->topOfHoC);
        $this->pdf->MultiCell($this->widthOfCustomer, $this->lineHeight, 'House of Carnivore Pty Ltd');

        $this->pdf->SetFont($this->font, 'B', 14);
        $this->pdf->setXY($this->leftMargin, $this->topOfProduct);
        $this->pdf->MultiCell($this->widthOfWholeRow, $this->lineHeight, $prdName);

        $this->pdf->SetFont($this->font, '', 14);
        $this->pdf->setXY($this->leftMargin, $this->topOfQty);
        $this->pdf->Cell($this->widthOfWholeRow, $this->lineHeight, "Net Weight:");
        $this->pdf->SetFont($this->font, 'B', 14);
        $this->pdf->setXY($this->leftMargin + 35, $this->topOfQty);
        $this->pdf->Cell($this->widthOfWholeRow, $this->lineHeight, $qty);


        $this->pdf->SetFont($this->font, '', 12);
        $this->pdf->SetXY($this->leftMargin, $this->topOfLabelPD);
        $this->pdf->Cell($this->widthOfWholeRow, $this->lineHeight, "PACKED ON:");
        $this->pdf->SetXY($this->leftMargin + 35, $this->topOfLabelPD);
        $this->pdf->Cell($this->widthOfWholeRow, $this->lineHeight, $pDate);

        $this->pdf->SetXY($this->leftMargin, $this->topOfLabelBBD);
        $this->pdf->Cell(30, $this->lineHeight, "BEST BEFORE:");
        $this->pdf->SetXY($this->leftMargin + 35, $this->topOfLabelBBD);
        $this->pdf->Cell($this->widthOfWholeRow, $this->lineHeight, $bbDate);


        $this->pdf->SetFont($this->font, '', 10);
        $this->pdf->SetXY($this->leftMargin, $this->topOfAccNo);
        $this->pdf->Cell($this->widthOfAccNo, $this->lineHeight, 'Acc. No. 6-111');
        $this->pdf->SetXY($this->leftMargin, $this->topOfAddress);
        $this->pdf->Cell($this->widthOfAddress, $this->lineHeight, '20-28 Tolley St, Wingfield SA 5013');
        $this->pdf->SetXY($this->leftMargin, $this->topOfStorageCondition);
        $this->pdf->Cell($this->widthOfStorageCondition, $this->lineHeight, 'Keep Refrigerated');
//        $this->pdf->SetXY($this->leftMargin, $this->topOfPhoneNo);
//        $this->pdf->Cell($this->widthOfPhoneNo, $this->lineHeight, 'Phone: +61 410 334 213');
//        $this->pdf->Cell($this->widthOfOrderNo, $this->lineHeight,  $orderNo);
//        $this->pdf->Cell($this->widthOfRun, $this->lineHeight,  "RUN: " . $run);
    }

    function print($name = '', $dest = ''): ?string
    {
        return $this->pdf->Output($name, $dest);
    }
}
