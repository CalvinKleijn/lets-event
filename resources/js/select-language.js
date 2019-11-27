/**
 * Voeg click event listeners toe aan de buttons waarmee je de taal kan veranderen.
 */
(() => {
    // Todo: Change the selector to be something more appropriate for the situation.
    const languageButtons = document.querySelectorAll('button[data-language]');

    languageButtons.forEach(languageButton => {
        const language = languageButton.getAttribute('data-language');
        languageButton.addEventListener('click', () => setUserLanguage(language))
    });
})();

/**
 * Genereer een cookie die de taal van de gebruiker bijhoudt.
 * @param language
 */
const setUserLanguage = language => {
    // TODO: Haal de beschikbare talen op vanuit de backend.
    const availableLanguages = ['nl', 'en'];

    if (!availableLanguages.includes(language)) {
        console.warn('The selected language is currently not available.');
        return;
    }

    // Check of de cookie al bestaat.
    const languageCookieValue = 'language=' + language;

    // Maak een cookie in de taal in op te slaan aan.
    document.cookie = 'language=' + language;
    // Herlaad de pagina om de wijzigingen door te voeren.
    location.reload();
};
