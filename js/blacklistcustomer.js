
// console.log("haiii");
// jQuery(document).ready(function ($) {
//     if (typeof $ === 'undefined') {
//         console.error('jQuery is not loaded!');
//     } else {
//         console.log('jQuery is loaded.');
//     }
//     // Define an array of states for each country
//     console.log("hello");
//     var stateOptions = {
//         'US': ['California', 'New York', 'Texas'],
//         'CA': ['Ontario', 'Quebec', 'British Columbia'],
//         'GB': ['England', 'Scotland', 'Wales']
//         // Add more states for other countries as needed
//     };

//     // Cache the state dropdown element
//     var stateSelect = $('.state-select');

//     // Update state dropdown based on the selected country
//     $('.country-select').change(function () {
//         var selectedCountry = $(this).val();
//         var states = stateOptions[selectedCountry] || [];

//         // Clear existing options
//         stateSelect.empty();

//         // Populate state dropdown with new options
//         states.forEach(function (state) {
//             stateSelect.append('<option value="' + state + '">' + state + '</option>');
//         });
//     });
// });

jQuery(document).ready(function ($) {
    // Function to update states based on the selected country
    function updateStates(countryCode) {
        $.ajax({
            url: blacklist_script_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'get_states',
                country_code: countryCode,
            },
            success: function (response) {
                $('#State').html(response);
            },
        });
    }

    // Trigger updateStates when the country dropdown changes
    $('#Country').change(function () {
        var countryCode = $(this).val();
        updateStates(countryCode);
    });

    // Initial states population based on the selected country on page load
    var initialCountryCode = $('#Country').val();
    updateStates(initialCountryCode);
});
