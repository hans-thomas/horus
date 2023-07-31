<?php

namespace Hans\Horus\Tests\Feature;

    use Hans\Horus\Exceptions\HorusErrorCode;
    use Hans\Horus\Exceptions\HorusException;
    use Hans\Horus\Tests\TestCase;

    class ExceptionTest extends TestCase
    {
        /**
         * @test
         *
         * @return void
         */
        public function getErrorCode(): void
        {
            $class = self::class;
            $exception = new HorusException(
                "Class is not a valid model! [ $class ].",
                HorusErrorCode::CLASS_IS_NOT_VALID
            );

            self::assertEquals(
                "Class is not a valid model! [ $class ].",
                $exception->getMessage()
            );

            self::assertEquals(
                HorusErrorCode::CLASS_IS_NOT_VALID,
                $exception->getErrorCode()
            );
        }
    }
