<?php

namespace SharePay;

/**
 * Base class for SharePay test cases
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    const API_KEY = '83a8cf683c4c287543311052527ad5f87cef58a00af3e7fd';

    protected static function authorizeFromEnv()
    {
        $apiKey = getenv('SharePay_API_KEY');
        if (!$apiKey) {
            $apiKey = self::API_KEY;
        }

        SharePay::setApiKey($apiKey);
    }
}
