<?php

namespace App\Http\Controllers;

use App\Lib\Barcode;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public $prosjecnaPlaca = 8448;
    public $osnovica;

    public function __construct()
    {
        $this->osnovica = $this->prosjecnaPlaca * 0.40;
    }

    public function index()
    {
        $oib = "23111769545";

        $mirovinskoPrviStup  = $this->mirovinskoPrviStup($oib);
        $mirovinskoDrugiStup = $this->mirovinskoDrugiStup($oib);
        $zdravstveno         = $this->zdravstveno($oib);

        return view('welcome', compact('mirovinskoPrviStup', 'mirovinskoDrugiStup', 'zdravstveno'));
    }

    public function zdravstveno($oib)
    {
        $barcode = new Barcode;

        $data = [
            "renderer" => "image",
            "options"  => [
                "format"  => "png",
                "scale"   => 2,
                "ratio"   => 3,
                "color"   => "#000",
                "bgColor" => "#fff",
                "padding" => 0
            ],
            "data"     => [
                "amount"      => round(0.165 * $this->osnovica, 2),
                "sender"      => [
                    "name"   => "Nenad Tičarić",
                    "street" => "Sv. Mateja 19",
                    "place"  => "10000 Zagreb"
                ],
                "receiver"    => [
                    "name"      => "Hrvatski zavod za zdravstveno osiguranje",
                    "street"    => "",
                    "place"     => "",
                    "iban"      => "HR6510010051550100001",
                    "model"     => "68",
                    "reference" => "8478-$oib"
                ],
                "purpose"     => "",
                "description" => "doprinos za zdravstveno osiguranje"
            ]
        ];
        list($barcode, $contentType) = $barcode->generate($data);
        return $barcode;
    }

    public function mirovinskoDrugiStup($oib)
    {
        $barcode = new Barcode;

        $data = [
            "renderer" => "image",
            "options"  => [
                "format"  => "png",
                "scale"   => 2,
                "ratio"   => 3,
                "color"   => "#000",
                "bgColor" => "#fff",
                "padding" => 0
            ],
            "data"     => [
                "amount"      => round(0.05 * $this->osnovica, 2),
                "sender"      => [
                    "name"   => "Nenad Tičarić",
                    "street" => "Sv. Mateja 19",
                    "place"  => "10000 Zagreb"
                ],
                "receiver"    => [
                    "name"      => "Doprinos za MO na temelju individualne kapitalizirane štednje",
                    "street"    => "",
                    "place"     => "",
                    "iban"      => "HR7610010051700036001",
                    "model"     => "68",
                    "reference" => "2046-$oib"
                ],
                "purpose"     => "",
                "description" => "doprinos za mirovisko osiguranje na temelju individualne kapitalizirane štednje"
            ]
        ];
        list($barcode, $contentType) = $barcode->generate($data);
        return $barcode;
    }

    public function mirovinskoPrviStup($oib)
    {
        $barcode = new Barcode;

        $data = [
            "renderer" => "image",
            "options"  => [
                "format"  => "png",
                "scale"   => 2,
                "ratio"   => 3,
                "color"   => "#000",
                "bgColor" => "#fff",
                "padding" => 0
            ],
            "data"     => [
                "amount"      => round(0.15 * $this->osnovica, 2),
                "sender"      => [
                    "name"   => "Nenad Tičarić",
                    "street" => "Sv. Mateja 19",
                    "place"  => "10000 Zagreb"
                ],
                "receiver"    => [
                    "name"      => "Državni proračun RH",
                    "street"    => "",
                    "place"     => "",
                    "iban"      => "HR1210010051863000160",
                    "model"     => "68",
                    "reference" => "8214-$oib"
                ],
                "purpose"     => "",
                "description" => "doprinos za mirovisko osiguranje"
            ]
        ];
        list($barcode, $contentType) = $barcode->generate($data);
        return $barcode;
    }
}
