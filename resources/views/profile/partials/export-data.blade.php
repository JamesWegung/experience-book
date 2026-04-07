<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('profile.export-data') }}
        </h2>
    </header>

    <div class="mt-4">
        <a href="{{ route('events.exportByXlsx') }}" class="btn btn-primary shadow">Скачать все мероприятия в формате Excel</a>
    </div>

</section>
