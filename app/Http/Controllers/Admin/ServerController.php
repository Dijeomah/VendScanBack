<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\BusinessServer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ServerController extends Controller
{
    /**
     * Get all servers
     */
    public function index()
    {
        try {
            $servers = Server::with(['business_servers.business_link'])
                ->latest()
                ->paginate(50);

            return success('Servers fetched successfully', $servers, Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Servers exception: ' . $exception->getMessage());
            return error('Error fetching servers', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get server statistics
     */
    public function statistics()
    {
        try {
            $totalServers = Server::count();
            $activeServers = Server::where('status', 'active')->count();
            $inactiveServers = Server::where('status', 'inactive')->count();
            $assignedServers = BusinessServer::where('status', 'active')->distinct('server_id')->count();

            return success('Server statistics fetched successfully', [
                'total_servers' => $totalServers,
                'active_servers' => $activeServers,
                'inactive_servers' => $inactiveServers,
                'assigned_servers' => $assignedServers
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Get Server Statistics exception: ' . $exception->getMessage());
            return error('Error fetching server statistics', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a server
     */
    public function destroy($id)
    {
        try {
            $server = Server::findOrFail($id);
            $server->delete();

            return success('Server deleted successfully', [], Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error('Admin Delete Server exception: ' . $exception->getMessage());
            return error('Error deleting server', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
