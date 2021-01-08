<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OpeningTimesRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $openingHours = json_decode($value, true);
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($weekdays as $day) {
            if ($openingHours[$day] != null) {

                $slots = $openingHours[$day];
                $lastSlotClosesAt = null;

                foreach ($slots as $slot) {
                    $opensAt = strtotime($slot['opens_at']);
                    $closesAt = strtotime($slot['closes_at']);

                    if (($lastSlotClosesAt != null && $lastSlotClosesAt >= $opensAt) || ($opensAt >= $closesAt)) {
                        return false;
                    }

                    $lastSlotClosesAt = $closesAt;
                }
            }
        }


        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Set opening hours properly';
    }
}
