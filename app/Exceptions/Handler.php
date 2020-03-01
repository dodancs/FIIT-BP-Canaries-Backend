<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
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
	public function render($request, Exception $e)
	{
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
			$response['trace'] = explode("\n", $e->getTraceAsString());
		}

		if (!empty($request)) {
			try {
				switch ($response['error']) {
					case "Token not provided":
						return response()->json(['code' => 0, 'message' => 'Token not provided'], 400);
						break;
					case "Token has expired":
						return response()->json(['code' => 1, 'message' => 'Unauthorized', 'details' => 'Token has expired'], 401);
						break;
					case "Token Signature could not be verified.":
					case "Wrong number of segments":
						return response()->json(['code' => 1, 'message' => 'Unauthorized', 'details' => 'Invalid token'], 401);
						break;
					case "The given data was invalid.":
						return response()->json(['code' => 0, 'message' => 'Bad request', 'details' => 'Credentials not supplied'], 400);
						break;
					case "Too Many Attempts.":
						return response()->json(['code' => 1, 'message' => 'Rate limit exceeded', 'retry' => $e->getHeaders()['Retry-After']], 429)
							->header('X-RateLimit-Limit', $e->getHeaders()['X-RateLimit-Limit'])
							->header('X-RateLimit-Remaining', $e->getHeaders()['X-RateLimit-Remaining'])
							->header('X-RateLimit-Reset', $e->getHeaders()['X-RateLimit-Reset'])
							->header('Retry-After', $e->getHeaders()['Retry-After']);
						break;
					default:
						return response()->json($response, $code);
						break;
				}
			} catch (\Exception $e) {
				print_r($e);
				die('failed to raise exception');
			}
		}

		return parent::render($request, $e);
	}
}
