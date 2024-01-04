<?php
    if (isset($_POST['phoneNumber'])) {
        $phoneNumber = $_POST['phoneNumber'];

        // Проверка на пустоту
        if (empty($phoneNumber)) {
            echo json_encode(['error' => 'Поле не может быть пустым']);
            exit;
        }

        // Разделение номера на код страны и оставшиеся цифры
        $countryCode = '';
        $remainingDigits = $phoneNumber;

        // Находим код страны (может быть от 1 до 4 символов)
        for ($i = 1; $i <= 4; $i++) {
            $countryCode = substr($phoneNumber, 0, $i);
            $remainingDigits = substr($phoneNumber, $i);

            // Загрузим данные с кодами стран из файла JSON
            $countryCodes = json_decode(file_get_contents('country_codes.json'), true);

            if (isset($countryCodes[$countryCode])) {
                break;
            }
        }

        // Если код страны не найден
        if (!isset($countryCodes[$countryCode])) {
            echo json_encode(['error' => 'Код страны не найден']);
            exit;
        }

        $countryData = $countryCodes[$countryCode];

        // Проверка соответствия длины оставшихся цифр
        $numberOfDigits = strlen($remainingDigits);

        if ($numberOfDigits === $countryData['digitsAfterCode']) {
            // Проверка наличия префикса оператора в списке
            $prefixToCheck = '';

            // Находим префикс оператора (может быть от 1 до 3 цифр)
            for ($j = 1; $j <= 3; $j++) {
                $prefixToCheck = substr($remainingDigits, 0, $j);

                if (in_array($prefixToCheck, $countryData['operatorPrefixes'])) {
                    echo json_encode([
                        'country' => $countryData['country'],
                        'operatorPrefix' => $prefixToCheck,
                        'numberOfDigits' => $numberOfDigits,
                        'remainingDigits' => $remainingDigits
                    ]);
                    exit;
                }
            }

            // Если первая проверка не успешна, выполняем вторую проверку без учета префикса
            echo json_encode([
                'country' => $countryData['country'],
                'operatorPrefix' => 'Unknown',
            ]);
            exit;
        }
        echo json_encode(['error' => "Неверная длина номера. Код страны:".$countryData['country']]);
    } else {
        echo json_encode(['error' => 'Номер телефона не установлен']);
    }
?>
