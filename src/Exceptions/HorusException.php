<?php


    namespace Hans\Horus\Exceptions;


    use Exception;
    use Throwable;

    class HorusException extends Exception {
        private int $errorCode;

        public function __construct( string $message, int $errorCode, $code = 0, Throwable $previous = null ) {
            parent::__construct( $message, $code, $previous );
            $this->errorCode = $errorCode;
        }

        public function getErrorCode() {
            return $this->errorCode;
        }
    }
