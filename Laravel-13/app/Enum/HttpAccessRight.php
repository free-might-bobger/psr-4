<?php
namespace App\Enum;

enum HttpAccessRight: int {
    const GET = AccessRight::LISTING;
    const POST = AccessRight::CREATE;
    const PUT = AccessRight::UPDATE;
    const DELETE = AccessRight::DELETE;
    const PATCH = AccessRight::UPDATE;
}

?>
