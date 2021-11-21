<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingsController extends Controller
{
    public const DEFAULT_PAGE_SIZE = 10;

    public function index(Request $request): AnonymousResourceCollection
    {
        $pageSize = $request->get('perPage', self::DEFAULT_PAGE_SIZE);
        $page = $request->get('page', 1);

        return BookingResource::collection(
            Booking::orderBy('id')->paginate($pageSize, ['*'], 'bookings', $page)
        );
    }

    public function bookTrip(Request $request): BookingResource
    {
        $this->validate($request, [
            'tripSlug' => 'required',
        ]);

        $trip = Trip::where('slug', $request->get('tripSlug'))->first();

        if (!$trip) {
            throw new NotFoundHttpException(sprintf(
                'Trip %s does not exist',
                $request->get('tripSlug')
            ));
        }

        $user = Auth::user();

        $booking = Booking::create([
            'user_id' => $user->id,
            'trip_id' => $trip->id,
            'status' => Booking::STATUS_RESERVED,
        ]);

        return new BookingResource($booking);
    }
}
