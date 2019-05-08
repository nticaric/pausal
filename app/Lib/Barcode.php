<?php

namespace App\Lib;

use BigFish\PDF417\PDF417;
use BigFish\PDF417\Renderers\ImageRenderer;
use BigFish\PDF417\Renderers\JsonRenderer;
use BigFish\PDF417\Renderers\SvgRenderer;

class Barcode
{

    public $amount;
    public $purpose;
    public $desription;
    public $sender;
    public $receiver;

    public function generate($data)
    {
        $renderer = $data['renderer'];
        $options  = $data['options'];
        $data     = $data['data'];

        return $this->render($renderer, $options, $data);
    }

    private $renderers = [
        "image" => ImageRenderer::class,
        "json"  => JsonRenderer::class,
        "svg"   => SvgRenderer::class
    ];

    public function render($renderer, $options, $data)
    {
        // Convert JSON to a string by HUB3 standard
        $string = $this->fromArray($data);

        // Encode barcode data
        $pdf417 = new PDF417();

        // Settings required by HUB3 spec
        $pdf417->setSecurityLevel(4);
        $pdf417->setColumns(9);

        $barcodeData = $pdf417->encode($string);

        // Render
        $renderer    = $this->getRenderer($renderer, $options);
        $barcode     = $renderer->render($barcodeData);
        $contentType = $renderer->getContentType();

        return [$barcode, $contentType];
    }

    public function fromArray($array)
    {
        $this->amount      = $array['amount'];
        $this->purpose     = $array['purpose'];
        $this->description = $array['description'];
        $this->sender      = $array['sender'];
        $this->receiver    = $array['receiver'];

        return $this->toString();
    }

    /**
     * Factory method, creates an instance of a renderer.
     *
     * @param  string $name     Name of the renderer (from $this->renderers).
     * @param  array  $options  Renderer options array.
     *
     * @return BigFish\PDF417\RendererInterface An instance of renderer.
     */
    protected function getRenderer($name, array $options = [])
    {
        if (!isset($this->renderers[$name])) {
            throw new \InvalidArgumentException("Unknown renderer \"$name\".");
        }

        $class = $this->renderers[$name];

        return new $class($options);
    }

    public function toString()
    {
        $amount = (integer) ($this->amount * 100);
        $amount = str_pad($amount, 15, '0', STR_PAD_LEFT);

        $parts = [];

        $parts[] = "HRVHUB30";
        $parts[] = "HRK";
        $parts[] = $amount;
        $parts[] = $this->sender['name'];
        $parts[] = $this->sender['street'];
        $parts[] = $this->sender['place'];
        $parts[] = $this->receiver['name'];
        $parts[] = $this->receiver['street'];
        $parts[] = $this->receiver['place'];
        $parts[] = $this->receiver['iban'];
        $parts[] = "HR".$this->receiver['model'];
        $parts[] = $this->receiver['reference'];
        $parts[] = $this->purpose;
        $parts[] = $this->description;

        return implode("\n", $parts)."\n";
    }
}
