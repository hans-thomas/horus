<?php


    namespace Hans\Horus\Exceptions;


    use Exception;
    use Illuminate\Http\JsonResponse;
    use Throwable;

    class HorusException extends Exception {

	    private int $errorCode;

	    public function __construct( string $message, int $errorCode, int $responseCode = 500, Throwable $previous = null ) {
		    parent::__construct( $message, $responseCode, $previous );
		    $this->errorCode = $errorCode;
	    }

	    public function getErrorCode(): int {
		    return $this->errorCode;
	    }

    }
