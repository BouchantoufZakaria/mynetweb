<?php

namespace App\Utils;
trait NumbersUtils
{

    /**
     * @param string $phoneNumber
     * @return string
     *
     * This function standardizes Algerian phone numbers to a uniform format.
     * It ensures that the phone number starts with the country code (213) and removes any leading zeros.
     * Example:
     * Input: "0779847077"
     * Output: "213779847077"
     */
    public function uniformAlgerianNumbers( string $phoneNumber ) : string {
        // Remove all non-digit characters
        $cleanedNumber = preg_replace('/\D/', '', $phoneNumber);

        // Check if the number starts with 0 or 213
        if (str_starts_with($cleanedNumber, '0')) {
            // If it starts with 0, replace it with 213
            $cleanedNumber = '213' . substr($cleanedNumber, 1);
        } elseif (!str_starts_with($cleanedNumber, '213')) {
            // If it does not start with 213, prepend it
            $cleanedNumber = '213' . $cleanedNumber;
        }

        return $cleanedNumber;
    }

    /**
     * @param string $phoneNumber
     * @return string
     *
     * This function formats the phone number for local uses.
     * It removes the country code (213) if it exists,
     * and ensures the number starts with 0.
     *
     * Example:
     * Input: "+213 779847077"
     * Output: "0779847077"
     */
    public function formatNumbersForLocalUses( string $phoneNumber ) : string {
        // Remove all non-digit characters
        $cleanedNumber = preg_replace('/\D/', '', $phoneNumber);

        // Check if the number starts with 213
        if (str_starts_with($cleanedNumber, '213')) {
            // If it starts with 213, remove it
            $cleanedNumber = substr($cleanedNumber, 3);
        }

        // Ensure the number starts with 0
        if (!str_starts_with($cleanedNumber, '0')) {
            $cleanedNumber = '0' . $cleanedNumber;
        }

        return $cleanedNumber;
    }


    /**
     * @param string $phoneNumber
     *
     * this function will hide the phone number and show only his last 2 and first 2 digits
     */
    public function hidePhoneNumber( string $phoneNumber ) : string
    {
        // Remove all non-digit characters
        $cleanedNumber = preg_replace('/\D/', '', $phoneNumber);

        // Check if the number is at least 4 digits long
        if (strlen($cleanedNumber) < 4) {
            return $cleanedNumber; // Return as is if too short
        }

        // Hide the middle part of the number
        return substr($cleanedNumber, 0, 2) . '****' . substr($cleanedNumber, -2);
    }
}
