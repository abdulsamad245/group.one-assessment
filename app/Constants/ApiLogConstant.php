<?php

namespace App\Constants;

/**
 * Constants for the api_logs table.
 */
class ApiLogConstant
{
    public const TABLE = 'api_logs';

    public const ID = 'id';
    public const CORRELATION_ID = 'correlation_id';
    public const METHOD = 'method';
    public const PATH = 'path';
    public const IP_ADDRESS = 'ip_address';
    public const USER_AGENT = 'user_agent';
    public const REQUEST_HEADERS = 'request_headers';
    public const REQUEST_BODY = 'request_body';
    public const RESPONSE_HEADERS = 'response_headers';
    public const RESPONSE_BODY = 'response_body';
    public const STATUS_CODE = 'status_code';
    public const CONTENT_TYPE = 'content_type';
    public const REFERER = 'referer';
    public const REQUESTED_AT = 'requested_at';
    public const RESPONDED_AT = 'responded_at';
    public const DURATION_MS = 'duration_ms';
    public const USER_ID = 'user_id';
    public const BRAND_ID = 'brand_id';
}
