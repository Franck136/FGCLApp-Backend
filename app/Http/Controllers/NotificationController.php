<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /api/notifications
    public function index(Request $request)
    {
        $query = Notification::where('user_id', $request->user()->id)
            ->with('contrat:id,reference', 'intervention:id,reference')
            ->latest('created_at');

        if ($request->filled('lu')) {
            $query->where('lu', $request->boolean('lu'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return response()->json([
            'notifications'  => $query->paginate(20),
            'total_non_lues' => Notification::where('user_id', $request->user()->id)
                                            ->where('lu', false)
                                            ->count(),
        ]);
    }

    // GET /api/notifications/{id}
    public function show(Request $request, Notification $notification)
    {
        // Un user ne peut voir que ses propres notifications
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        return response()->json($notification->load('contrat:id,reference', 'intervention:id,reference'));
    }

    // PUT /api/notifications/{id}/lire
    public function marquerLue(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $notification->update(['lu' => true]);

        return response()->json(['message' => 'Notification marquée comme lue.']);
    }

    // PUT /api/notifications/lire-tout
    public function marquerToutesLues(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
                    ->where('lu', false)
                    ->update(['lu' => true]);

        return response()->json(['message' => 'Toutes les notifications marquées comme lues.']);
    }

    // DELETE /api/notifications/{id}
    public function destroy(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification supprimée.']);
    }
}
