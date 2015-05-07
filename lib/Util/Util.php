<?php

namespace SharePay\Util;

use SharePay\Object;

abstract class Util
{
    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     *
     * @param array|mixed $array
     * @return boolean True if the given object is a list.
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }

      // TODO: generally incorrect, but it's correct given SharePay's response
        foreach (array_keys($array) as $k) {
            if (!is_numeric($k)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Recursively converts the PHP SharePay object to an array.
     *
     * @param array $values The PHP SharePay object to convert.
     * @return array
     */
    public static function convertSharePayObjectToArray($values)
    {
        $results = array();
        foreach ($values as $k => $v) {
            // FIXME: this is an encapsulation violation
            if ($k[0] == '_') {
                continue;
            }
            if ($v instanceof Object) {
                $results[$k] = $v->__toArray(true);
            } elseif (is_array($v)) {
                $results[$k] = self::convertSharePayObjectToArray($v);
            } else {
                $results[$k] = $v;
            }
        }
        return $results;
    }

    /**
     * Converts a response from the SharePay API to the corresponding PHP object.
     *
     * @param array $resp The response from the SharePay API.
     * @param array $opts
     * @return Object|array
     */
    public static function convertToSharePayObject($resp, $opts)
    {
        $types = array(
            'account' => 'SharePay\\Account',
            'card' => 'SharePay\\Card',
            'charge' => 'SharePay\\Charge',
            'coupon' => 'SharePay\\Coupon',
            'customer' => 'SharePay\\Customer',
            'list' => 'SharePay\\Collection',
            'invoice' => 'SharePay\\Invoice',
            'invoiceitem' => 'SharePay\\InvoiceItem',
            'event' => 'SharePay\\Event',
            'file' => 'SharePay\\FileUpload',
            'token' => 'SharePay\\Token',
            'transfer' => 'SharePay\\Transfer',
            'plan' => 'SharePay\\Plan',
            'recipient' => 'SharePay\\Recipient',
            'refund' => 'SharePay\\Refund',
            'subscription' => 'SharePay\\Subscription',
            'fee_refund' => 'SharePay\\ApplicationFeeRefund',
            'bitcoin_receiver' => 'SharePay\\BitcoinReceiver',
            'bitcoin_transaction' => 'SharePay\\BitcoinTransaction',
        );
        if (self::isList($resp)) {
            $mapped = array();
            foreach ($resp as $i) {
                array_push($mapped, self::convertToSharePayObject($i, $opts));
            }
            return $mapped;
        } elseif (is_array($resp)) {
            if (isset($resp['object']) && is_string($resp['object']) && isset($types[$resp['object']])) {
                $class = $types[$resp['object']];
            } else {
                $class = 'SharePay\\Object';
            }
            return $class::constructFrom($resp, $opts);
        } else {
            return $resp;
        }
    }
}
