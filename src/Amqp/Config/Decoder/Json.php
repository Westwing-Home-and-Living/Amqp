<?php
/**
 * Implementation of json decoder
 *
 * @author Cristian Datculescu <cristian.datculescu@westwing.de>
 */
namespace Amqp\Config\Decoder;

use Amqp\Config\Exception;

class Json
{
    public function decode($string)
    {
        $decoded = json_decode($string);
        $lastError = json_last_error();
        if ($lastError != \JSON_ERROR_NONE) {
            $errStr = "Error decoding json: ";
            switch ($lastError) {
                case JSON_ERROR_DEPTH:
                    $errStr .= " JSON_ERROR_DEPTH";
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $errStr .= " JSON_ERROR_STATE_MISMATCH";
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $errStr .= " JSON_ERROR_CTRL_CHAR";
                    break;
                case JSON_ERROR_SYNTAX:
                    $errStr .= " JSON_ERROR_SYNTAX";
                    break;
                case JSON_ERROR_UTF8:
                    $errStr .= " JSON_ERROR_UTF8";
                    break;
            }
            throw new Exception($errStr);
        }

        return $decoded;
    }
}