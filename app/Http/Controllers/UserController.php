<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\GetUsersRequest;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserResource;
use App\Mail\NewUserNotificationEmail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(GetUsersRequest $request): JsonResponse
    {
        $users = User::forList(
            $request->string('search')->toString() ?: null,
            $request->validated('sortBy') ?? 'created_at',
        )->paginate(perPage: 10, page: $request->integer('page', 1));

        return response()->json([
            'page' => $users->currentPage(),
            'users' => UserListResource::collection($users)->resolve($request),
        ]);
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        Mail::to($user)->send(new WelcomeEmail($user));
        Mail::to(User::admins()->get())->send(new NewUserNotificationEmail($user));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
