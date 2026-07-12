<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * An expected, named business-rule violation (e.g. "the default list can't
 * be deleted") as opposed to a bug. TryAction shows its message verbatim as
 * the error toast instead of a generic failure message.
 */
class ActionNotAllowedException extends RuntimeException {}
