<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 py-8">
        <x-empty-state
            icon="user-circle"
            :title="__('No role assigned yet')"
            :description="__('Your account is not yet assigned to a role. Contact the SE Admin Office if this seems wrong.')"
        />
    </div>
</x-layouts::app>
