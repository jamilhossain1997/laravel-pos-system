<?

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB, Storage};
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function index()
    {
        $files = collect(Storage::disk('local')->files('backups'))
            ->map(fn($f) => [
                'name' => basename($f),
                'size' => Storage::disk('local')->size($f),
                'date' => Storage::disk('local')->lastModified($f),
            ])->sortByDesc('date');
        return view('admin.backup.index', compact('files'));
    }
    public function create()
    {
        $filename = 'backup_' . now()->format('Ymd_His') . '.sql';
        $path     = storage_path("app/backups/{$filename}");
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0755, true);

        $db   = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        exec("mysqldump -h{$host} -u{$user} -p{$pass} {$db} > {$path}");
        return back()->with('success', "Backup created: {$filename}");
    }
    public function download($file)
    {
        $path = storage_path("app/backups/{$file}");
        if (!file_exists($path)) abort(404);
        return response()->download($path);
    }
    public function destroy($file)
    {
        Storage::disk('local')->delete("backups/{$file}");
        return back()->with('success', 'Backup deleted!');
    }
}
