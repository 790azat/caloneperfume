<div class="space-y-6">
    <div class="a-card overflow-x-auto !p-0">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-white/8 text-xs text-stone-500 uppercase">
                <tr><th class="p-4">Имя</th><th class="p-4">Email</th><th class="p-4">Заявок</th><th class="p-4">Регистрация</th><th class="p-4">Админ</th></tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach ($users as $user)
                    <tr wire:key="u-{{ $user->id }}">
                        <td class="p-4 text-stone-100">{{ $user->name }}</td>
                        <td class="p-4 text-stone-400">{{ $user->email }}</td>
                        <td class="p-4">{{ $user->inquiries_count }}</td>
                        <td class="p-4 text-stone-500">{{ $user->created_at->format('d.m.Y') }}</td>
                        <td class="p-4">
                            @if ($user->id === auth()->id())
                                <span class="text-xs text-gold-300">это вы</span>
                            @else
                                <x-admin.toggle :on="$user->is_admin" wire:click="toggleAdmin({{ $user->id }})" wire:confirm="Изменить права администратора для {{ $user->email }}?" />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
</div>
