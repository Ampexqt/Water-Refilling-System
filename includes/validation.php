<?php

/**
 * Validation Helper Class
 * Provides comprehensive validation methods to minimize human errors
 */
class Validator
{
    private $errors = [];

    /**
     * Validate Philippine phone number format (09XXXXXXXXX)
     */
    public function validatePhone($phone, $fieldName = 'Phone')
    {
        $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-numeric

        if (!preg_match('/^09[0-9]{9}$/', $phone)) {
            $this->errors[] = "$fieldName must be in format 09XXXXXXXXX (11 digits)";
            return false;
        }
        return true;
    }

    /**
     * Validate date is not in the past
     */
    public function validateFutureDate($date, $fieldName = 'Date', $allowToday = true)
    {
        try {
            $inputDate = new DateTime($date);
            $today = new DateTime();
            $today->setTime(0, 0, 0);

            if ($allowToday) {
                if ($inputDate < $today) {
                    $this->errors[] = "$fieldName cannot be in the past";
                    return false;
                }
            } else {
                if ($inputDate <= $today) {
                    $this->errors[] = "$fieldName must be in the future";
                    return false;
                }
            }
            return true;
        } catch (Exception $e) {
            $this->errors[] = "$fieldName is not a valid date";
            return false;
        }
    }

    /**
     * Validate string length
     */
    public function validateLength($string, $min, $max, $fieldName = 'Field')
    {
        $length = strlen(trim($string));

        if ($length < $min) {
            $this->errors[] = "$fieldName must be at least $min characters";
            return false;
        }

        if ($length > $max) {
            $this->errors[] = "$fieldName must not exceed $max characters";
            return false;
        }

        return true;
    }

    /**
     * Validate number range
     */
    public function validateRange($number, $min, $max, $fieldName = 'Number')
    {
        if ($number < $min || $number > $max) {
            $this->errors[] = "$fieldName must be between $min and $max";
            return false;
        }
        return true;
    }

    /**
     * Validate email format
     */
    public function validateEmail($email, $fieldName = 'Email')
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "$fieldName is not a valid email address";
            return false;
        }
        return true;
    }

    /**
     * Validate password strength
     */
    public function validatePassword($password, $fieldName = 'Password')
    {
        $valid = true;

        if (strlen($password) < 8) {
            $this->errors[] = "$fieldName must be at least 8 characters";
            $valid = false;
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $this->errors[] = "$fieldName must contain at least one uppercase letter";
            $valid = false;
        }

        if (!preg_match('/[a-z]/', $password)) {
            $this->errors[] = "$fieldName must contain at least one lowercase letter";
            $valid = false;
        }

        if (!preg_match('/[0-9]/', $password)) {
            $this->errors[] = "$fieldName must contain at least one number";
            $valid = false;
        }

        return $valid;
    }

    /**
     * Check if value is unique in database
     */
    public function checkUnique($conn, $table, $field, $value, $excludeId = 0, $fieldName = null)
    {
        if ($fieldName === null) {
            $fieldName = ucfirst($field);
        }

        $stmt = $conn->prepare("SELECT id FROM $table WHERE $field = ? AND id != ?");
        $stmt->bind_param("si", $value, $excludeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            $this->errors[] = "$fieldName already exists";
            return false;
        }
        return true;
    }

    /**
     * Validate record exists in database
     */
    public function recordExists($conn, $table, $id, $fieldName = 'Record')
    {
        $stmt = $conn->prepare("SELECT id FROM $table WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 0) {
            $this->errors[] = "$fieldName does not exist";
            return false;
        }
        return true;
    }

    /**
     * Validate business hours (8 AM - 6 PM)
     */
    public function validateBusinessHours($time, $fieldName = 'Time')
    {
        try {
            $hour = (int)date('H', strtotime($time));

            if ($hour < 8 || $hour >= 18) {
                $this->errors[] = "$fieldName must be between 8:00 AM and 6:00 PM";
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->errors[] = "$fieldName is not a valid time";
            return false;
        }
    }

    /**
     * Validate items format (e.g., "3x 5-gallon")
     */
    public function validateItemsFormat($items, $fieldName = 'Items')
    {
        if (!preg_match('/^\d+x\s+[\w\-]+$/i', trim($items))) {
            $this->errors[] = "$fieldName format should be like: 3x 5-gallon";
            return false;
        }
        return true;
    }

    /**
     * Validate required field is not empty
     */
    public function validateRequired($value, $fieldName = 'Field')
    {
        if (empty(trim($value))) {
            $this->errors[] = "$fieldName is required";
            return false;
        }
        return true;
    }

    /**
     * Validate name (letters, spaces, and common punctuation only)
     */
    public function validateName($name, $fieldName = 'Name')
    {
        if (!preg_match("/^[a-zA-Z\s\.\-']+$/", $name)) {
            $this->errors[] = "$fieldName can only contain letters, spaces, and basic punctuation";
            return false;
        }
        return true;
    }

    /**
     * Get all validation errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get errors as HTML string
     */
    public function getErrorsAsString($separator = '<br>')
    {
        return implode($separator, $this->errors);
    }

    /**
     * Check if validation passed (no errors)
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * Clear all errors
     */
    public function clearErrors()
    {
        $this->errors = [];
    }

    /**
     * Add custom error
     */
    public function addError($message)
    {
        $this->errors[] = $message;
    }
}
