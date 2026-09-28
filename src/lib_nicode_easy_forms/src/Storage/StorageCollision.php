<?php
declare(strict_types=1);
namespace Nicode\EasyForms\Storage;
/** Exclusive create failed because the key already exists; it is not owned by this write. */
final class StorageCollision extends \RuntimeException {}
