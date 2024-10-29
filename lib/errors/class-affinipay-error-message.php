<?php

namespace AffiniPayWordPress;


abstract class AffiniPay_Error_Message {
	const AUTHENTICATION_FAILURE = 'Unable to authenticate with AffiniPay, please check your API Keys';
	const API_ERROR = 'There was a problem communicating with the AffiniPay API';
	const INVALID_REQUEST = 'Invalid Request Parameters';
	const INVALID_MERCHANT = 'Invalid Merchant Error Message';
	const INVALID_MERCHANT_ACCOUNT = 'Invalid Merchant Account';
	const DECLINE_UNKNOWN = 'An unknown error occurred while processing the transaction';
	const DECLINE_GENERAL = 'The transaction was declined';
	const DECLINE_CALL_ISSUER = 'The transaction was declined please call card issuer for reason.';
	const TRANSACTION_NOT_FOUND = 'The transaction you are referencing does not exist';
}
