function validatePhoneNumber() {
    var phoneNumber = $('#phoneNumber').val();

    $.ajax({
        type: 'POST',
        url: 'validate_phone.php',
        data: { phoneNumber: phoneNumber },
        success: function(response) {
            var result = JSON.parse(response);
            var errorMessage = '';
            var resultMessage = '';
            // Обработка ошибок
            if (result.error) {
                var errorMessage = 'Ошибка: ' + result.error;

                if (result.country) {
                    errorMessage += ' (Country: ' + result.country;
                    if (result.operatorPrefix && result.operatorPrefix !== 'Unknown') {
                        errorMessage += ', Operator Prefix: ' + result.operatorPrefix;
                    }
                    errorMessage += ')';
                }
                $('#error-message').text(errorMessage);
                $('#result-message').text('');

            } else {
                // Обработка успешного результата
                var resultMessage = 'Номер телефона действителен. Код страны: ' + result.country;

                if (result.operatorPrefix && result.operatorPrefix === 'Unknown') {
                    resultMessage = 'Номер телефона не действителен(неизвестный оператор). Код страны: ' + result.country;
                }
                $('#error-message').text('');
                $('#result-message').text(resultMessage);
            }
        },
    });
}

// Обработчик событий для поля ввода(отключаем enter так как он перезагружает страницу)
$('#phoneNumber').keypress(function(e) {
    if (e.which === 13) {
        // 13 - код клавиши Enter
        validatePhoneNumber();
    }
});
