<?php

namespace App\Http\Controllers;

use App\DTO\TripsFilterDTO;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Models\TripLocation;
use App\Services\TripsFilterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TripsController extends Controller
{
    public const DEFAULT_PAGE_SIZE = 10;

    public function index(Request $request): AnonymousResourceCollection
    {
        $pageSize = $request->get('perPage', self::DEFAULT_PAGE_SIZE);
        $page = $request->get('page', 1);

        return TripResource::collection(
            Trip::orderBy('id')->paginate($pageSize, ['*'], 'trips', $page)
        );
    }

    /**
     * @throws NotFoundHttpException
     */
    public function read(string $slug): TripResource
    {
        $trip = Trip::where('slug', $slug)->first();

        if (!$trip) {
            throw new NotFoundHttpException(sprintf(
                'Trip %s does not exist',
                $slug
            ));
        }

        return new TripResource($trip);
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request): TripResource
    {
        $this->validate($request, [
            'title' => 'required|string',
            'description' => 'required|string',
            'location.city' => 'required|string',
            'location.country' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'price' => 'required|numeric',
        ]);

        $location = TripLocation::updateOrCreate([
            'city' => $request->get('location')['city'],
            'country' => $request->get('location')['country'],
        ]);

        $slug = $this->getSlugFromTitle($request->get('title'));

        $startDate = new Carbon($request->start_date);
        $endDate = new Carbon($request->end_date);

        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $trip = Trip::create([
            'slug' => $slug,
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'location_id' => $location->id,
            'price' => $request->get('price'),
        ]);

        return new TripResource($trip);
    }

    private function getSlugFromTitle(string $title): string
    {
        $slug = Str::slug($title);
        $increment = 1;
        while (Trip::where('slug', $slug)->first()) {
            $increment++;
            $slug = Str::slug($title . ' ' . $increment);
        }

        return $slug;
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, string $slug): TripResource
    {
        $this->validate($request, [
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'location.city' => 'sometimes|string',
            'location.country' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'price' => 'sometimes|numeric',
        ]);

        $existingTrip = Trip::where('slug', $slug)->first();
        if (!$existingTrip) {
            throw new NotFoundHttpException(sprintf(
                'Trip %s does not eixst.',
                $slug
            ));
        }

        $location = TripLocation::updateOrCreate([
            'city' => $request->get('location')['city'],
            'country' => $request->get('location')['country'],
        ]);

        $existingTrip->title = $request->get('title', $existingTrip->title);
        $existingTrip->description = $request->get('description', $existingTrip->description);
        $existingTrip->start_date = $request->get('start_date', $existingTrip->start_date);
        $existingTrip->end_date = $request->get('end_date', $existingTrip->end_date);
        $existingTrip->price = $request->get('price', $existingTrip->price);
        $existingTrip->title = $request->get('title', $existingTrip->title);
        $existingTrip->location_id = $location->id;

        $existingTrip->save();

        return new TripResource($existingTrip);
    }

    public function delete(int $id): JsonResponse
    {
        $existingTrip = Trip::find($id);
        if (!$existingTrip) {
            throw new NotFoundHttpException(sprintf(
                'Trip with ID %d does not eixst.',
                $id
            ));
        }

        $existingTrip->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function search(Request $request, TripsFilterService $filterService): AnonymousResourceCollection
    {
        DB::enableQueryLog();

        $pageSize = $request->get('perPage', self::DEFAULT_PAGE_SIZE);
        $page = $request->get('page', 1);
        $data = $filterService->filterTrips(
            TripsFilterDTO::fromRequest($request),
            $pageSize,
            $page
        );

        /* dd(DB::getQueryLog()); */

        return TripResource::collection($data);
    }
}
