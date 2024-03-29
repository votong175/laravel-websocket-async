<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Anton Samuelsson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
?>
<?php namespace VoTong\WebSocketAsync;

/**
 * Generic helper for evaluation and conversion.
 *
 * @package  VoTong\WebSocketAsync
 * @author   Anton Samuelsson <samuelsson.anton@gmail.com>
 */
class Evaluate
{
    /**
     * Checks if the input string is a JSON.
     *
     * @param  string $string
     *
     * @return boolean
     */
    public static function isJson($string)
    {
        json_decode($string);
        
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    /**
     * Returns a PHP standard object from a JSON string.
     *
     * @return stdClass
     */
    public static function jsonDecodeString($string)
    {
        return ((Evaluate::isJson($string))) ? (array) json_decode($string, true) : [];
    }
}
