<?php
const ERR_VAL = "Validation Error";
const ERR_API = "External API Error";
const STAT_OK = "Ok";

const AVAILABLE_APIS = ["SharpSpring"];

const EXPECTED_OUTPUT_DICT = [
    "SharpSpring" => [
        "getLeads" => "lead",
    ],
];

const SHARPSPRING_CONFIG = [
    "ACCOUNT_ID" => "",
    "SECRET_KEY" => "",
    "ACTION_URL" => "https://api.sharpspring.com/pubapi/v1/",
    "ALLOWED_METHODS" => [
        'getLeads',
    ],
    "ALLOWED_RESPONSES" => [
        "getLeads" => 'lead', 
    ],
    "MAX_QUOTA" => 500,
];