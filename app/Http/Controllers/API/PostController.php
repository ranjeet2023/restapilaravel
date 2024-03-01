<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     // Import JsonResponse class


    //  use db seed 
    //  'email' => 'admin@gmail.com',
    //  'password' => bcrypt(123456),


    public function loginUser(Request $request): JsonResponse 
    {
        try {
            $input = $request->all();
            if (!Auth::attempt($input)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
            $user = Auth::user();
            $token = $user->createToken('restApp')->accessToken;
            return response()->json(['status' => 200, 'token' => $token], 200);
        } catch (\Exception $e) {
            // Log the exception message or stack trace
            Log::error('Login Error: ' . $e->getMessage());
            
            // Return a generic error message
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
  

    public function index()
    {
        if(Auth::guard('api')->check()){
            $posts = Post::paginate(10);
            return response()->json($posts);
        }
        return Response(['data' => 'Unauthorized'],401);
    }

    public function store(Request $request)
    {
        if (Auth::guard('api')->check()) {
            try {
                $request->validate([
                    'title' => 'required|unique:posts',
                    'content' => 'required|min:10',
                ]);
            } catch (ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            }
    
            $post = Post::create($request->all());
            return response()->json($post, 201);
        }
    
        return response()->json(['data' => 'Unauthorized'], 401);
    }

    public function show($id)
    {
        if (Auth::guard('api')->check()) {
            try {
                $post = Post::findOrFail($id);
                return response()->json($post);
            } catch (ModelNotFoundException $exception) {
                return response()->json(['error' => 'Post not found'], 404);
            } catch (\Exception $exception) {
                return response()->json(['error' => 'Something went wrong'], 500);
            }
        }
        return response()->json(['data' => 'Unauthorized'], 401);
    }

    public function update(Request $request, $id)
    {
        if (Auth::guard('api')->check()) {
            $post = Post::findOrFail($id);
            try {
                $request->validate([
                    'title' => 'required|unique:posts,title,' . $post->id,
                    'content' => 'required|min:10',
                ]);
            } catch (ValidationException $e) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            $post->update($request->all());
            return response()->json($post);
        }
        return response()->json(['data' => 'Unauthorized'], 401);
    }

    public function destroy($id)
    {
        if (Auth::guard('api')->check()) {
            try {
                $post = Post::findOrFail($id);
                $post->delete();
                return response()->json(['data' => 'Post deleted successfully']);
            } catch (ModelNotFoundException $exception) {
                return response()->json(['error' => 'Post not found'], 404);
            } catch (\Exception $exception) {
                return response()->json(['error' => 'Something went wrong'], 500);
            }
        }
        return response()->json(['data' => 'Unauthorized'], 401);
    }


    public function userLogout(): Response
    {
        if(Auth::guard('api')->check()){
            $accessToken = Auth::guard('api')->user()->token();

                \DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $accessToken->id)
                    ->update(['revoked' => true]);
            $accessToken->revoke();

            return Response(['data' => 'Unauthorized','message' => 'User logout successfully.'],200);
        }
        return Response(['data' => 'Unauthorized'],401);
    }

   
}
