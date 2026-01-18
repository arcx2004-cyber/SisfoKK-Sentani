<?php

use App\Models\User;
use App\Models\Siswa;

echo "Total Users: " . User::count() . "\n";
echo "Total Siswa: " . Siswa::count() . "\n";

$siswasWithoutUser = Siswa::whereNull('user_id')->count();
echo "Siswa without User ID: " . $siswasWithoutUser . "\n";

$usersWithRoleSiswa = User::role('siswa')->get();
echo "Users with role 'siswa': " . $usersWithRoleSiswa->count() . "\n";

foreach ($usersWithRoleSiswa as $user) {
    $siswa = Siswa::where('user_id', $user->id)->first();
    echo "User [{$user->id}] {$user->name} ({$user->email}) -> Linked Siswa: " . ($siswa ? $siswa->nama_lengkap : 'NONE') . "\n";
    
    // Attempt to match by name if not linked
    if (!$siswa) {
        $potentialMatch = Siswa::where('nama_lengkap', 'like', '%' . $user->name . '%')->first();
        if ($potentialMatch) {
            echo "  -> Potential Match found by name: [{$potentialMatch->id}] {$potentialMatch->nama_lengkap}\n";
        }
    }
}
