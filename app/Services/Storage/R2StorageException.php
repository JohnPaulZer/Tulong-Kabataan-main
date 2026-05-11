<?php

namespace App\Services\Storage;

/**
 * Thrown by R2StorageService when an upload, delete, or validation step fails.
 *
 * Controllers should catch this and translate to a user-friendly response.
 */
class R2StorageException extends \RuntimeException
{
}
