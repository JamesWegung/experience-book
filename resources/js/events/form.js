export function initEventForm() {
    const contentInput = document.getElementById('content_name');
    const resultsContainer = document.getElementById('autocomplete-results');
    let timeoutId;

    contentInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        const content_type_id = document.getElementById('content_type_id').value;

        clearTimeout(timeoutId);

        if (query.length < 2) {
            hideResults();
            return;
        }

        timeoutId = setTimeout(async () => {
            try {
                const {data} = await axios.get('/api/events/autocomplete', {
                    params: {
                        query: query,
                        content_type_id: content_type_id
                    }
                });

                displayResults(data);
            } catch (error) {
                console.error('Ошибка при загрузке подсказок: ', error.response?.data || error.message)
            }
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!contentInput.contains(e.target) && !resultsContainer.contains(e.target)) {
            hideResults();
        }
    });

    contentInput.addEventListener('keydown', function(e) {
        const items = resultsContainer.querySelectorAll('.autocomplete-item');
        const activeItem = resultsContainer.querySelector('.autocomplete-item.active');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            navigateResults(1, items, activeItem);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            navigateResults(-1, items, activeItem);
        } else if (e.key === 'Enter' && activeItem) {
            e.preventDefault();
            selectSuggestion(activeItem);
        } else if (e.key === 'Escape') {
            hideResults();
        }
    });

    function displayResults(suggestions) {
        if (suggestions.length === 0) {
            hideResults();
            return;
        }

        resultsContainer.innerHTML = '';
        suggestions.forEach((suggestion, index) => {
            const item = document.createElement('div');
            item.className = `autocomplete-item px-3 py-2 cursor-pointer hover:bg-blue-50 ${index === 0 ? 'active' : ''}`;
            item.textContent = suggestion;
            item.setAttribute('data-value', suggestion);

            item.addEventListener('click', function() {
                selectSuggestion(this);
            });

            item.addEventListener('mouseenter', function() {
                resultsContainer.querySelectorAll('.autocomplete-item').forEach(el => {
                    el.classList.remove('active');
                });

                this.classList.add('active');
            });

            resultsContainer.appendChild(item);
        });

        resultsContainer.classList.remove('hidden');
    }

    function hideResults() {
        resultsContainer.classList.add('hidden');
        resultsContainer.innerHTML = '';
    }

    function navigateResults(direction, items, activeItem) {
        if (items.length === 0) return;

        let nextIndex = 0;

        if (activeItem) {
            const currentIndex = Array.from(items).indexOf(activeItem);
            nextIndex = currentIndex + direction;

            if (nextIndex < 0) nextIndex = items.length - 1;
            if (nextIndex >= items.length) nextIndex = 0;

            activeItem.classList.remove('active');
        }

        items[nextIndex].classList.add('active');
        items[nextIndex].scrollIntoView({ block: 'nearest' });
    }

    function selectSuggestion(item) {
        contentInput.value = item.getAttribute('data-value');
        hideResults();
        console.log('Selected:', contentInput.value);
    }
}

initEventForm();
