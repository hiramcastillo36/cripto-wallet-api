<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Block a user
     */
    public function blockUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'reason' => 'required|string|min:5',
        ]);

        try {
            $user = User::findOrFail($request->user_id);

            // Prevent blocking yourself
            if ($user->id === auth('api')->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot block yourself',
                ], 400);
            }

            $user->update([
                'is_blocked' => true,
                'blocked_reason' => $request->reason,
                'blocked_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User blocked successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_blocked' => $user->is_blocked,
                        'blocked_reason' => $user->blocked_reason,
                        'blocked_at' => $user->blocked_at,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error blocking user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unblock a user
     */
    public function unblockUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        try {
            $user = User::findOrFail($request->user_id);

            $user->update([
                'is_blocked' => false,
                'blocked_reason' => null,
                'blocked_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User unblocked successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_blocked' => $user->is_blocked,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error unblocking user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all blocked users
     */
    public function blockedUsers(): JsonResponse
    {
        try {
            $blockedUsers = User::where('is_blocked', true)
                ->select('id', 'name', 'email', 'blocked_reason', 'blocked_at')
                ->orderByDesc('blocked_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Blocked users list',
                'data' => [
                    'users' => $blockedUsers,
                    'count' => $blockedUsers->count(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving blocked users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all users
     */
    public function allUsers(Request $request): JsonResponse
    {
        try {
            $query = User::query();

            // Filter by search term
            if ($request->has('search')) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }

            // Filter by status
            if ($request->has('is_blocked')) {
                $query->where('is_blocked', (bool) $request->is_blocked);
            }

            // Filter by admin status
            if ($request->has('is_admin')) {
                $query->where('is_admin', (bool) $request->is_admin);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Users list',
                'data' => [
                    'users' => $users->items(),
                    'pagination' => [
                        'total' => $users->total(),
                        'count' => $users->count(),
                        'per_page' => $users->perPage(),
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single user details
     */
    public function userDetails(string $userId): JsonResponse
    {
        try {
            \Log::info('sw'. $userId);
            $user = User::findOrFail((int)$userId);

            return response()->json([
                'success' => true,
                'message' => 'User details',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_admin' => $user->is_admin,
                        'is_blocked' => $user->is_blocked,
                        'blocked_reason' => $user->blocked_reason,
                        'blocked_at' => $user->blocked_at,
                        'created_at' => $user->created_at,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all wallets
     */
    public function allWallets(Request $request): JsonResponse
    {
        try {
            $query = Wallet::with('user');

            // Filter by search term (user name or wallet address)
            if ($request->has('search')) {
                $search = $request->search;
                $query->where('wallet_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            }

            // Filter by frozen status
            if ($request->has('is_frozen')) {
                $isFrozen = (bool) $request->is_frozen;
                if ($isFrozen) {
                    $query->whereNotNull('frozen_at');
                } else {
                    $query->whereNull('frozen_at');
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $wallets = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Wallets list',
                'data' => [
                    'wallets' => $wallets->items(),
                    'pagination' => [
                        'total' => $wallets->total(),
                        'count' => $wallets->count(),
                        'per_page' => $wallets->perPage(),
                        'current_page' => $wallets->currentPage(),
                        'last_page' => $wallets->lastPage(),
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving wallets',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get wallet details
     */
    public function walletDetails(Wallet $wallet): JsonResponse
    {
        try {
            $wallet->load(['user', 'balances']);

            return response()->json([
                'success' => true,
                'message' => 'Wallet details',
                'data' => [
                    'wallet' => [
                        'id' => $wallet->id,
                        'wallet_address' => $wallet->wallet_address,
                        'total_value_usd' => $wallet->total_value_usd,
                        'is_frozen' => !is_null($wallet->frozen_at),
                        'frozen_at' => $wallet->frozen_at,
                        'frozen_reason' => $wallet->frozen_reason,
                        'user' => [
                            'id' => $wallet->user->id,
                            'name' => $wallet->user->name,
                            'email' => $wallet->user->email,
                        ],
                        'balances_count' => $wallet->balances->count(),
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving wallet details',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Freeze a wallet
     */
    public function freezeWallet(Request $request): JsonResponse
    {
        $request->validate([
            'wallet_id' => 'required|integer|exists:wallets,id',
            'reason' => 'required|string|min:5',
        ]);

        try {
            $wallet = Wallet::findOrFail($request->wallet_id);

            if (!is_null($wallet->frozen_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet is already frozen',
                ], 400);
            }

            $wallet->update([
                'frozen_at' => now(),
                'frozen_reason' => $request->reason,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wallet frozen successfully',
                'data' => [
                    'wallet' => [
                        'id' => $wallet->id,
                        'wallet_address' => $wallet->wallet_address,
                        'frozen_at' => $wallet->frozen_at,
                        'frozen_reason' => $wallet->frozen_reason,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error freezing wallet',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unfreeze a wallet
     */
    public function unfreezeWallet(Request $request): JsonResponse
    {
        $request->validate([
            'wallet_id' => 'required|integer|exists:wallets,id',
        ]);

        try {
            $wallet = Wallet::findOrFail($request->wallet_id);

            if (is_null($wallet->frozen_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet is not frozen',
                ], 400);
            }

            $wallet->update([
                'frozen_at' => null,
                'frozen_reason' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wallet unfrozen successfully',
                'data' => [
                    'wallet' => [
                        'id' => $wallet->id,
                        'wallet_address' => $wallet->wallet_address,
                        'frozen_at' => $wallet->frozen_at,
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error unfreezing wallet',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
