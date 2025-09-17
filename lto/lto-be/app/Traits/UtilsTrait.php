<?php

namespace App\Traits;
use Auth;
use App\Enum\Role;

trait UtilsTrait
 {

    /**
    * Splits a string into an array using a specified regular expression pattern.
    *
    * @param string $pattern The regular expression pattern to use for splitting.
    *                        Example: '/,/' to split by commas.
    * @param string $value   The string to be split into parts.
    *                        Example: 'apple,orange,banana'.
    *
    * @return array Returns an array of substrings obtained by splitting the input string.
    *               Empty elements are excluded from the result.
    *
    * Example:
    * $this->pregSplit( '/,/', 'apple,orange,,banana' );
    * Output: [ 'apple', 'orange', 'banana' ]
    */

    public function pregSplit( string $pattern, string|null $value ): array
    {
        return preg_split( $pattern, $value, 0, PREG_SPLIT_NO_EMPTY );
    }

    public function isSuperAdmin():bool {
        $user = Auth::User();
        $rolesId = $user->roles->pluck( 'id' )->all();
        if ( in_array( Role::SUPER_ADMIN, $rolesId ) ) {
            return true;
        }
        return false;
    }
}
