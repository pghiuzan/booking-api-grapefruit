<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends Controller
{
    public const DEFAULT_PAGE_SIZE = 10;

    public function index(Request $request): AnonymousResourceCollection
    {
        $pageSize = $request->get('perPage', self::DEFAULT_PAGE_SIZE);
        $page = $request->get('page', 1);

        return UserResource::collection(
            User::orderBy('id')->paginate($pageSize, ['*'], 'users', $page)
        );
    }

    /**
     * @throws NotFoundHttpException
     */
    public function read(int $id): UserResource
    {
        $user = User::find($id);

        if (!$user) {
            throw new NotFoundHttpException(sprintf(
                'User with ID %d does not exist',
                $id
            ));
        }

        return new UserResource($user);
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request): UserResource
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // for some reason, the unique rule doesn't work well during tests
        $existingUser = User::where('email', $request->get('email'))->first();
        if ($existingUser) {
            throw ValidationException::withMessages([
                'email' => [
                    sprintf('User with %s email already exists.', $request->get('email')),
                ],
            ]);
        }

        $user = User::create([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        return new UserResource($user);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, int $id): UserResource
    {
        $this->validate($request, [
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'email' => 'sometimes|email',
            'password' => 'sometimes|min:8|confirmed',
        ]);

        $existingUser = User::find($id);
        if (!$existingUser) {
            throw new NotFoundHttpException(sprintf(
                'User with ID %d does not eixst.',
                $id
            ));
        }

        $existingUser->first_name = $request->get('first_name', $existingUser->first_name);
        $existingUser->last_name = $request->get('last_name', $existingUser->last_name);
        $existingUser->email = $request->get('email', $existingUser->email);
        $existingUser->password = $request->has('password')
            ? Hash::make($request->get('password'))
            : $existingUser->password;

        $existingUser->save();

        return new UserResource($existingUser);
    }

    public function delete(int $id): JsonResponse
    {
        $existingUser = User::find($id);
        if (!$existingUser) {
            throw new NotFoundHttpException(sprintf(
                'User with ID %d does not eixst.',
                $id
            ));
        }

        $existingUser->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }
}
