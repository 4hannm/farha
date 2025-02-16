<?php

namespace App\Repositories;

use App\Models\SubscribeTransaction;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Support\Facades\Session;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;

class BookingRepository implements BookingRepositoryInterface
{
    public function createBooking(array $data)
    {
        return SubscribeTransaction::create($data);
    }

    public function findByTrxIdAndPhoneNumber($bookingTrxId, $phoneNumber)
    {
        return SubscribeTransaction::where('booking_trx_id, $bookingTrxId')
                                    ->where('phone', $phoneNumber)
                                    ->first();
    }

    public function saveToSession(array $data)
    {
        Session::put('bookingData', $data);
    }

    public function updateSessionData(array $data)
    {
        $bookingData = session('bookingData', []);
        $bookingData = array_merge($bookingData, $data);
        session(['bookingData' => $bookingData]);
    }

    public function getBookingDataFromSession()
    {
        return session('bookingData', []);
    }

    public function clearSession()
    {
        Session::forget('bookingData');
    }
}
