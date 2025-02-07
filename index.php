<?php
require_once 'logins.php'; // Файл, в котором только список: "apiLogin" => "название заведения"
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>API Commands Status Checker</title>
  <link rel="stylesheet" href="style.css" />
  <!-- highlight.js (CDN) для подсветки JSON -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
  <!-- Animate.css для анимаций (пример) -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</head>
<body>

  <div class="container">
    <h1>Проверка статуса операций</h1>
    
    <!-- Выбор заведения (apiLogin) -->
    <label for="apiLogin">Выберите заведение:</label>
    <select id="apiLogin">
      <option value="">— Не выбрано —</option>
      <?php foreach ($apiLogins as $loginKey => $loginName): ?>
        <option value="<?= htmlspecialchars($loginKey) ?>">
          <?= htmlspecialchars($loginName) ?> (<?= htmlspecialchars($loginKey) ?>)
        </option>
      <?php endforeach; ?>
    </select>
    
    <!-- Поле для Organization ID -->
    <label for="organizationId">Organization ID:</label>
    <input type="text" id="organizationId" placeholder="Введите Organization ID" />
    
    <!-- Поле для Correlation ID -->
    <label for="correlationId">Correlation ID:</label>
    <input type="text" id="correlationId" placeholder="Введите Correlation ID" />
    
    <!-- Кнопка отправки -->
    <button id="checkStatusBtn">Проверить статус</button>
    
    <!-- Блок вывода результата -->
    <div id="responseContainer" class="hidden animate__animated animate__fadeInUp">
      <h2>Результат:</h2>
      <pre><code id="responseCode" class="json"></code></pre>
      <div id="knowledgeBaseTip" class="knowledge-tip"></div>
    </div>
  </div>

  <!-- JS: highlight.js + наш script.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
  <script src="script.js"></script>
</body>
</html>
