<?php


    namespace Hans\Horus\Exceptions;


    use Exception;
    use Illuminate\Http\JsonResponse;
    use Throwable;

    class HorusException extends Exception {

	    private int|string $errorCode;

	    public function __construct( string $message, int|string $errorCode, int $responseCode = 500, Throwable $previous = null ) {
		    parent::__construct( $message, $responseCode, $previous );
		    $this->errorCode = $errorCode;
	    }

	    public function getErrorCode(): int|string {
		    return $this->errorCode;
	    }

    }
