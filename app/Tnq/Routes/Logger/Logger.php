<?php
namespace Tnq\Routes\Logger;

interface Logger {
    public function logException(\Exception $e);
}
