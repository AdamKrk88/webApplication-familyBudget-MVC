<?php

namespace App;

/**
 * Data validation
 *
 * PHP version 7.2.0
 */

class Validation 
{

    /**
     * Capitalize first letter including polish characters
     * @param string $string string from which to extract the substring
     * @param string $encoding the character encoding of the string 
     * 
     * @return string string with first letter capitalized
     */
    private static function mb_ucfirst($string, $encoding)
    {
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, null, $encoding);
        return mb_strtoupper($firstChar, $encoding) . $then;
    }


    /** 
     * Output escaping
     * @param string $data input provided by user
     * 
     * @return string data after output escaping
     */
    public static function testInput($data) 
    {
        $data = trim($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public static function capitalizeFirstLetter($string)
    {
        return static::mb_ucfirst(mb_strtolower($string), 'UTF-8');
    }

    public static function validateCategoryOrPayment($dataToBeValidated, $dataType) 
    {
        $errors = [];

        //Category validation
        if ($dataToBeValidated == '' && $dataType === "Category") 
        {
            $errors[] = 'Category not provided';
        }
        elseif ($dataToBeValidated == '' && $dataType === "Payment")
        {
            $errors[] = 'Payment method not provided';
        }
        elseif ($dataToBeValidated != '') 
        {
            if (!preg_match("/^([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)* ?[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/",$dataToBeValidated) || strlen($dataToBeValidated) > 20) 
            {
                $errors[] = "Only letters and one space allowed. Maximum number of characters is 20";
            }
        }

       return $errors;
    }

    public static function validateAmount($dataToBeValidated)
    {
        $errors = [];

        if ($dataToBeValidated == '') 
        {
            $errors[] = 'Amount not provided';
        }
        else
        {
            if (!preg_match("/^\d+\.\d\d$/",$dataToBeValidated) || strlen($dataToBeValidated) > 13 || (float)$dataToBeValidated > 1000000000.00)
            {
                $errors[] = "Incorrect amount format - only digits allowed and value with two decimal places should be used. Maximum amount is 1000000000";
            }
        }

        return $errors;

    }

    public static function validateDate($dataToBeValidated)
    {
        $errors = [];

        if ($dataToBeValidated == '') 
        {
            $errors[] = 'Date not provided';
        }
        else
        {
            if (!preg_match("/^2\d{3}-[01]\d-[0-3]\d$/",$dataToBeValidated))
            {
                $errors[] = "Incorrect date format. Please use format 2YYY-MM-DD";
            }
        }

        return $errors;
    }

    public static function validateComment($dataToBeValidated) 
    {
        $errors = [];

        $isCommentValid = preg_match("/^[a-ząćęłńóśźż0-9\040\.\-\/]*$/i", $dataToBeValidated);    // \x5C backslash
        $isCommentString = is_string($dataToBeValidated);

        if ($isCommentValid === 0)
        {
            $errors[] = "Only letters, numbers, space, forward slash, period and dash allowed in the comment";
        }
        elseif ($isCommentValid === false)
        {
            $errors[] = "Technical error with validation occurred on server side";
        }



        if ($isCommentString)
        {
            $commentLength = strlen($dataToBeValidated);

            if ($commentLength > 50)
            {
                $errors[] = "Length of comment must be between 0 (no comment) and 50";
            }
        }
        else
        {
            $errors[] = "Comment must be a string";
        }
        
        return $errors;       
      }
   
}