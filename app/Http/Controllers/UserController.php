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


        return (new UserResource($user))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
