function initFilter() {
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');

    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search');
    if (searchQuery) {
        searchInput.value = searchQuery;
        clearSearch.classList.remove('hidden');
    }

    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            performSearch(e.target.value.trim());
        }
    });

    clearSearch.addEventListener('click', function () {
        searchInput.value = '';
        clearSearch.classList.add('hidden');
        performSearch('');
    });

    function performSearch(query) {
        const url = new URL(window.location.href);

        if (query) {
            url.searchParams.set('search', query);
        } else {
            url.searchParams.delete('search');
        }

        url.searchParams.delete('page');

        window.location.href = url.toString();
    }

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });
}

initFilter();
