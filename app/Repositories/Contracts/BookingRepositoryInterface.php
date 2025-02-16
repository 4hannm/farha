<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Arr;

interface BookingRepositoryInterface 
{
    public function createBooking(array $data);
    public function findByTrxIdAndPhoneNumber($bookingTrxId, $phoneNumber);
    public function saveToSession(array $data);
    public function updateSessionData(array $data);
    public function getBookingDataFromSession();
    public function clearSession();
}
