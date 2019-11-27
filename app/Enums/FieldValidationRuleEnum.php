<?php
namespace App\Enums;

class FieldValidationRuleEnum
{
    const PHONE_NUMBER = 'regex:/^([+]31)(\s06-)([0-9]{8})$/u';
}
