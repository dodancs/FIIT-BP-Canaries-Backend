<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler {
	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		AuthorizationException::class,
		//HttpException::class,
		ModelNotFoundException::class,
		ValidationException::class,
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e) {
		$code = 422;
		if (method_exists($e, 'getStatusCode')) {
			$code = $e->getStatusCode();
			if ($code < 400)
				$code = 422;
		}

		$message = $e->getMessage();
		if (is_object($message))
			$message = $message->toArray();
		if (empty($message))
			$message = "http status $code";

		$response = [];
		$response['error'] = $message;
		if (env('APP_DEBUG')) {
			$response['file'] = $e->getFile();
			$response['line'] = $e->getLine();
	//		$response['trace'] = $e->getTrace();
			$response['trace'] = explode("\n",$e->getTraceAsString());
		}

		if (!empty($request)) {
			try {
				return response()->json($response, $code);
			} catch (\Exception $e) {
				print_r($e);
				die('failed to raise exception');
			}
		}

		return parent::render($request, $e);
	}
}
