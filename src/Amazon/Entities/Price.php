<?php
/**
 * Copyright (c) 2015 Giacomo Persichini
 *
 * Amazon Mechanical Turk is a product of Amazon Inc.
 *
 * Distributed under the MIT license.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */
namespace Amazon\Entities;

/**
 * Represents a Price Entity on Amazon Mechanical Turk.
 *
 * @package Amazon
 * @author  Giacomo "Jack" Persichini <giacomopersichini@gmail.com>
 */
class Price extends AbstractEntity {

    /**
     * @var int The amount of money.
     */
    private $amount;

    /**
     * @var string An ISO 4217 currency code. Only USD is supported by MTurk.
     */
    private $currencyCode = 'USD';

    /**
     * @var string The textual representation with proper formatting for the currency.
     */
    private $formattedPrice;

    /**
     * Returns a valid Price.
     *
     * 'USD' is the only currency currently supported by Amazon MTurk.
     *
     * @param int $amount The amount of money.
     *
     * @throws \InvalidArgumentException If the amount is specified and is not valid.
     */
    public function __construct($amount = null) {
        if (!is_null($amount)) {
            if (is_int($amount)) {
                $this->amount = $amount;
                $this->formattedPrice = '$' . number_format($amount, 2, '.', ',');
            } else {
                throw new \InvalidArgumentException('$amount expects an integer.');
            }
        }
    }

    /**
     * Sets this Price.
     *
     * @param int $amount The amount of money.
     *
     * @throws \InvalidArgumentException
     */
    public function setPrice($amount) {
        if (is_int($amount)) {
            $this->amount = $amount;
            $this->formattedPrice = '$' . number_format($amount, 2, '.', ',');
        } else {
            throw new \InvalidArgumentException('$amount expects an integer.');
        }
    }

    /**
     * Returns the amount of money.
     *
     * @return int
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * Returns the ISO 4217 currency code.
     *
     * @return string
     */
    public function getCurrencyCode() {
        return $this->currencyCode;
    }

    /**
     * Returns the textual representation with proper formatting for the currency.
     *
     * @return string
     */
    public function getFormattedPrice() {
        return $this->formattedPrice;
    }

    /**
     * Returns this Price as an associative array.
     *
     * @return array
     */
    public function toArray() {
        $array = parent::toArray();
        $array['amount'] = $this->amount;
        $array['currencyCode'] = $this->currencyCode;
        $array['formattedPrice'] = $this->formattedPrice;
        return $array;
    }

    /**
     * Returns a well-formed Price from an XML response.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return Price
     */
    public static function parseFromXml(\SimpleXMLElement $xml) {
        $obj = new static();
        $obj->amount = intval($xml->Amount);
        $obj->currencyCode = (string) $xml->CurrencyCode;
        $obj->formattedPrice = (string) $xml->FormattedPrice;
        return $obj;
    }
}
