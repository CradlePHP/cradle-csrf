<?php //-->

use Cradle\Http\Request;
use Cradle\Http\Response;

/**
 * Loads CSRF token in stage
 *
 * @param *Request  $request
 * @param *Response $response
 */
$cradle->on('csrf-load', function (Request $request, Response $response) {
    //render the key
    $key = md5(uniqid());
    if($request->hasSession('csrf')) {
        $key = $request->getSession('csrf');
    }

    $request->setSession('csrf', $key);
    $response->setResults('csrf', $key);
});

/**
 * Validates CSRF
 *
 * @param *Request  $request
 * @param *Response $response
 */
$cradle->on('csrf-validate', function (Request $request, Response $response) {
    $actual = $request->getStage('csrf');
    $expected = $request->getSession('csrf');

    //no longer needed
    $request->removeSession('csrf');

    if($actual !== $expected) {
        //prepare to error
        $message = 'We prevented a potential attack on our servers coming from the request you just sent us.';
        $message = $this->package('global')->translate($message);
        $response->setError(true, $message);
    }

    //it passed
});
