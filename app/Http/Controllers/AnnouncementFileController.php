<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Scopes\AdminDepartmentScope;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnnouncementFileController extends Controller
{
    public function image(int $announcement): StreamedResponse|Response
    {
        $announcement = $this->findAnnouncement($announcement);

        Gate::authorize('view', $announcement);

        abort_unless($announcement->image_path !== null, 404);

        return Storage::disk('local')->response($announcement->image_path);
    }

    public function attachment(int $announcement): StreamedResponse
    {
        $announcement = $this->findAnnouncement($announcement);

        Gate::authorize('view', $announcement);

        abort_unless($announcement->attachment_path !== null, 404);

        return Storage::disk('local')->download(
            $announcement->attachment_path,
            $announcement->attachment_original_name,
        );
    }

    /**
     * Loaded without the admin-department scope: an announcement from
     * another department must still resolve to a real model here, so the
     * policy check below can deny it with a clean 403 instead of a 404
     * from a scoped-away implicit binding.
     */
    private function findAnnouncement(int $id): Announcement
    {
        return Announcement::withoutGlobalScope(AdminDepartmentScope::class)->findOrFail($id);
    }
}
