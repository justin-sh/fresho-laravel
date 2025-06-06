<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

function px2in($px): float
{
    return $px*0.0104166667;
}

function pt2in($pt): float
{
    return $pt*0.0138889;
}


function mm2in($mm): float
{
    return $mm*0.0393701;
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
    function __construct(){

        $this->font = "msjh";

        $this->pdf=new PdfChinese("L", "in", array(mm2in($this->pageWidth), mm2in($this->pageHeight)));
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->AddGBFont($this->font, $this->font);

        $this->fs = 12;
        $this->pageWidth = 80;
        $this->pageHeight = 60;
        $this->marginXY = 4;

        $this->lineHeight = pt2in($this->fs) + px2in(1);
        $this->leftMargin = mm2in($this->marginXY);
        $this->topOfCustomer = mm2in($this->marginXY);
        $this->widthOfCustomer = mm2in(52);
        $this->topOfProduct = mm2in(18);
        $this->widthOfProduct =  mm2in($this->pageWidth - $this->marginXY - $this->marginXY);
        $this->topOfQty = $this->topOfProduct + mm2in(12);
        $this->topOfLabelPD = $this->topOfQty + mm2in(8);
        $this->topOfLabelBBD = $this->topOfLabelPD + mm2in(6);
        $this->topOfOrderNo = $this->topOfLabelBBD + mm2in(8);
        $this->widthOfOrderNo = mm2in(40);
        $this->widthOfRun = mm2in(40);
    }

    function addNew($cusName, $prdName, $qty, $pDate, $bbDate, $orderNo, $run): void
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont($this->font, 'B', $this->fs);

        $this->pdf->setX($this->topOfCustomer);
//        $this->pdf->Image("h.png", $this->widthOfCustomer, $this->topOfCustomer, mm2in(23), mm2in(9));
        $this->pdf->Image(Storage::path('h.png'), $this->widthOfCustomer, $this->topOfCustomer, mm2in(23), mm2in(9));
        $this->pdf->setY($this->topOfCustomer, false);
        $this->pdf->MultiCell($this->widthOfCustomer, $this->lineHeight,iconv("utf-8","gbk",$cusName));


        $this->pdf->SetFont($this->font,'', 10);
        $this->pdf->setX($this->leftMargin);
        $this->pdf->setY($this->topOfProduct, false);
        $this->pdf->MultiCell(mm2in(72), $this->lineHeight,iconv("utf-8","gbk",$prdName));

        $this->pdf->setX($this->leftMargin);
        $this->pdf->setY($this->topOfQty, false);
        $this->pdf->SetFont($this->font, 'B', $this->fs);
        $this->pdf->Cell(mm2in(22), $this->lineHeight,iconv("utf-8","gbk","Qty:"),0);
        $this->pdf->Cell(mm2in(36), $this->lineHeight,iconv("utf-8","gbk",$qty),0);
        // $this->pdf->Ln();


        $this->pdf->setX($this->leftMargin);
        $this->pdf->setY($this->topOfLabelPD, false);
        $this->pdf->SetFont($this->font, '', $this->fs -1);
        $this->pdf->Cell(mm2in(25), $this->lineHeight,iconv("utf-8","gbk","Pack Date:"),0);
        $this->pdf->Cell(mm2in(36), $this->lineHeight,iconv("utf-8","gbk",$pDate),0);
        // $this->pdf->Ln();

        $this->pdf->setX($this->leftMargin);
        $this->pdf->setY($this->topOfLabelBBD, false);
        $this->pdf->Cell(mm2in(25), $this->lineHeight,iconv("utf-8","gbk","Best Before:"),0);
        $this->pdf->Cell(mm2in(36), $this->lineHeight,iconv("utf-8","gbk",$bbDate),0);
        // $this->pdf->Ln();

        $this->pdf->setX($this->leftMargin);
        $this->pdf->setY($this->topOfOrderNo, false);
        $this->pdf->Cell($this->widthOfOrderNo, $this->lineHeight,iconv("utf-8","gbk", $orderNo),0);
        $this->pdf->Cell($this->widthOfRun, $this->lineHeight,iconv("utf-8","gbk", "RUN: " . $run),0);
    }

    function print(){
        $this->pdf->Output();
    }
}
