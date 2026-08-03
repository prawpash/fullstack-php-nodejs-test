<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Mail\NewUserNotificationEmail;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
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
