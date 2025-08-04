<?php

namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{


    function login(Request $request): \Illuminate\Http\JsonResponse
    {

        return \response()->json([
            "message" => "Login successful",
        ]);

    }


    function updateUserInformation(Request $request): JsonResponse
    {
        return \response()->json([
            "message" => "User information updated successfully",
        ]);
    }


    function getUserInformation() : JsonResponse
    {
        return \response()->json([
            "message" => "User information retrieved successfully",
        ]);
    }

    function createUserSession() : JsonResponse
    {
        return \response()->json([
            "message" => "User session created successfully",
        ]);
    }

    function getLastWinnersDraws()
    {

    }
}
