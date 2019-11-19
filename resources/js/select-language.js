setUserLanguage = language => {
    let availableLanguages = ['nl', 'en'];

    // Frontend language validation.
    if (!availableLanguages.includes(language)) {
        console.warn('The selected language is currently not available.');
        return;
    }

    // Set a cookie and create a page refresh.
    document.cookie = 'language=' + language;
    // Reload the page to show the changed language.
    location.reload();
};

(createEventHandlerForEveryLanguageButton = () => {
    // Todo: Change the selector to be something more appropriate for the situation.
    const languageButtons = document.querySelectorAll('button[data-language]');

    languageButtons.forEach(languageButton => {
        const language = languageButton.getAttribute('data-language');
        languageButton.addEventListener('click', () => setUserLanguage(language))
    });

})();
