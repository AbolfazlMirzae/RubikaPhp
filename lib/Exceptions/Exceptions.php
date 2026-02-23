<?php

namespace RubikaPhp\Exceptions;

/**
 * Base exception class for RubikaPhp
 */
class RubikaException extends \Exception {}

/**
 * Exception thrown when API returns an error
 */
class RubikaAPIException extends RubikaException {}

/**
 * Exception thrown for network-related errors
 */
class RubikaNetworkException extends RubikaException {}

/**
 * Exception thrown when required parameters are missing
 */
class RubikaParameterException extends RubikaException {}

/**
 * Exception thrown when file operations fail
 */
class RubikaFileException extends RubikaException {}

/**
 * Exception thrown when authentication fails
 */
class RubikaAuthException extends RubikaException {}

/**
 * Exception thrown when bot is not running or stopped
 */
class RubikaBotException extends RubikaException {}
